<?php
// Parser for poems.txt format
// Format:
// === [datetime] ===
// * title (optional)
// body text...

define('POEM_TITLE_MAX_LENGTH', 50);
define('POEMS_CACHE_FILE', __DIR__ . '/../cache/poems_cache.php');

function parse_poems($filepath) {
    if (!file_exists($filepath)) return [];
    
    global $SETTINGS;
    $isDebug = isset($SETTINGS['debug']) && $SETTINGS['debug'];
    
    // Check cache (skip in debug mode)
    $fileCrc = hash_file('crc32', $filepath);
    $cacheData = null;
    
    if (!$isDebug && file_exists(POEMS_CACHE_FILE)) {
        $cacheData = @include POEMS_CACHE_FILE;
        if (is_array($cacheData) && isset($cacheData['crc']) && $cacheData['crc'] === $fileCrc) {
            return $cacheData['poems'];
        }
    }
    
    $content = file_get_contents($filepath);
    // Normalize line endings to LF to avoid accidental extra blank lines
    $content = str_replace(["\r\n", "\r"], "\n", $content);
    $blocks = preg_split('/^=== /m', $content, -1, PREG_SPLIT_NO_EMPTY);
    
    $poems = [];
    foreach ($blocks as $block) {
        $lines = explode("\n", trim($block));
        if (empty($lines[0])) continue;
        
        // First line: datetime ===
    // Extract and clean date string like "[Sep 25, 2025 9:08:34 am] ==="
    $dateLine = trim($lines[0]);
    $dateLine = str_replace('===', '', $dateLine);
    $dateTime = trim($dateLine, "[] \t");
        $date = '';
        $dateISO = '';
        $sortKey = '';
        try {
            $dt = new DateTime($dateTime);
            // Display without time, keep timestamp for sorting
            $date = $dt->format('M d, Y');
            $dateISO = $dt->format('Y-m-d');
            $sortKey = $dt->getTimestamp(); // Use timestamp for reliable sorting
        } catch (Exception $e) {
            $date = $dateTime;
            $dateISO = '';
            $sortKey = 0;
        }
        
        $title = '';
        $bodyStartIndex = 1;
        
        // Check if second line starts with *
        if (isset($lines[1]) && strpos(trim($lines[1]), '*') === 0) {
            $title = trim(substr(trim($lines[1]), 1));
            // Remove trailing punctuation from title
            $title = rtrim($title, '.,:;!?');
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
        // Ensure no empty line(s) immediately after the title
        while (!empty($bodyLines) && trim($bodyLines[0]) === '') {
            array_shift($bodyLines);
        }
        
    // Join and preserve original empty lines exactly as in source
    $body = implode("\n", $bodyLines);
        
        // If no title, use first line of body (max 50 chars)
        if (empty($title) && !empty($body)) {
            $firstLine = explode("\n", $body)[0];
            $title = mb_strlen($firstLine) > POEM_TITLE_MAX_LENGTH 
                ? mb_substr($firstLine, 0, POEM_TITLE_MAX_LENGTH) . '…' 
                : $firstLine;
            // Remove trailing punctuation from auto-generated title too
            $title = rtrim($title, '.,:;!?…');
            if (mb_strlen($firstLine) > POEM_TITLE_MAX_LENGTH) {
                $title .= '…';
            }
        }
        
        // Generate slug from title
        $slug = transliterate_to_slug($title);
        
        $poems[] = [
            'date' => $date, // display only
            'dateISO' => $dateISO,
            'sortKey' => $sortKey,
            'title' => $title,
            'slug' => $slug,
            'body' => $body
        ];
    }
    
    // Sort by sortKey ascending (oldest first); newest will be at the bottom
    usort($poems, function($a, $b) {
        $aKey = isset($a['sortKey']) ? $a['sortKey'] : 0;
        $bKey = isset($b['sortKey']) ? $b['sortKey'] : 0;
        return $aKey <=> $bKey; // Numeric comparison
    });
    
    // Save to cache (skip in debug mode)
    if (!$isDebug) {
        $cacheDir = dirname(POEMS_CACHE_FILE);
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $cacheContent = "<?php\nreturn " . var_export(['crc' => $fileCrc, 'poems' => $poems], true) . ";\n";
        @file_put_contents(POEMS_CACHE_FILE, $cacheContent);
    }
    
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
