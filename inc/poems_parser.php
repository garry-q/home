<?php
// Parser for poems.txt format
// Format:
// === [datetime] ===
// * title (optional)
// body text...

define('POEM_TITLE_MAX_LENGTH', 50);

function parse_poems($filepath) {
    if (!file_exists($filepath)) return [];
    
    $content = file_get_contents($filepath);
    $blocks = preg_split('/^=== /m', $content, -1, PREG_SPLIT_NO_EMPTY);
    
    $poems = [];
    foreach ($blocks as $block) {
        $lines = explode("\n", trim($block));
        if (empty($lines[0])) continue;
        
        // First line: datetime ===
        $dateTime = trim(str_replace('===', '', $lines[0]));
        $date = '';
        $sortKey = '';
        try {
            $dt = new DateTime($dateTime);
            $date = $dt->format('Y-m-d'); // Date only, no time
            $sortKey = $dt->format('Y-m-d H:i:s'); // Full for sorting
        } catch (Exception $e) {
            $date = $dateTime;
            $sortKey = $dateTime;
        }
        
        $title = '';
        $bodyStartIndex = 1;
        
        // Check if second line starts with *
        if (isset($lines[1]) && strpos(trim($lines[1]), '*') === 0) {
            $title = trim(substr(trim($lines[1]), 1));
            $bodyStartIndex = 2;
        }
        
        // Body is remaining lines
        $bodyLines = array_slice($lines, $bodyStartIndex);
        
        // If title was parsed with *, remove it from body if it appears as first line
        if (!empty($title) && $bodyStartIndex === 2) {
            $firstBodyLine = !empty($bodyLines) ? trim($bodyLines[0]) : '';
            if ($firstBodyLine === $title) {
                $bodyLines = array_slice($bodyLines, 1);
            }
        }
        
        // Join and preserve original empty lines from source
        $body = implode("\n", $bodyLines);
        
        // Only remove excessive empty lines (3+ consecutive empty lines become max 2)
        $body = preg_replace('/(\n\s*){3,}/', "\n\n", $body);
        $body = trim($body);
        
        // If no title, use first line of body (max 50 chars)
        if (empty($title) && !empty($body)) {
            $firstLine = explode("\n", $body)[0];
            $title = mb_strlen($firstLine) > POEM_TITLE_MAX_LENGTH 
                ? mb_substr($firstLine, 0, POEM_TITLE_MAX_LENGTH) . '…' 
                : $firstLine;
        }
        
        // Generate slug from title
        $slug = transliterate_to_slug($title);
        
        $poems[] = [
            'date' => $date,
            'sortKey' => $sortKey,
            'title' => $title,
            'slug' => $slug,
            'body' => $body
        ];
    }
    
    // Sort by sortKey descending (newest first)
    usort($poems, function($a, $b) {
        $aKey = isset($a['sortKey']) ? $a['sortKey'] : $a['date'];
        $bKey = isset($b['sortKey']) ? $b['sortKey'] : $b['date'];
        return strcmp($bKey, $aKey);
    });
    
    return $poems;
}

function transliterate_to_slug($text) {
    // Simple transliteration for Cyrillic
    $cyrillic = ['а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',' ',',','.',':',';','!','?'];
    $latin =    ['a','b','v','g','d','e','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','ts','ch','sh','sch','','y','','e','yu','ya','-','','','','','',''];
    
    $slug = mb_strtolower($text);
    $slug = str_replace($cyrillic, $latin, $slug);
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    $slug = preg_replace('/\-+/', '-', $slug);
    $slug = trim($slug, '-');
    
    return $slug ?: 'poem-' . substr(md5($text), 0, 8);
}

function get_alphabet_index($poems) {
    $index = [];
    $seenTitles = []; // Track unique titles to avoid duplicates
    
    foreach ($poems as $poem) {
        $titleKey = $poem['title'] . '|' . $poem['slug']; // Unique key
        if (isset($seenTitles[$titleKey])) {
            continue; // Skip duplicates
        }
        $seenTitles[$titleKey] = true;
        
        $first = mb_substr($poem['title'], 0, 1);
        $first = mb_strtoupper($first);
        if (!isset($index[$first])) {
            $index[$first] = [];
        }
        $index[$first][] = $poem;
    }
    uksort($index, function($a, $b) {
        // Cyrillic first, then Latin
        $isCyrA = preg_match('/[А-ЯЁ]/u', $a);
        $isCyrB = preg_match('/[А-ЯЁ]/u', $b);
        if ($isCyrA && !$isCyrB) return -1;
        if (!$isCyrA && $isCyrB) return 1;
        return strcmp($a, $b);
    });
    return $index;
}
