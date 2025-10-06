// Language switching functionality
const langButtons = document.querySelectorAll('.lang-btn');
let currentLanguage = 'en';
let settings = {};

// Load settings from JSON
async function loadSettings() {
    try {
        const response = await fetch('settings.json');
        settings = await response.json();
        console.log('Settings loaded:', settings);
        
        // Apply background image if specified
        if (settings.background_image) {
            document.body.style.background = `url('./img/${settings.background_image}') center/cover, linear-gradient(135deg, #667eea 0%, #764ba2 100%)`;
        }
        
        // Update social links if specified
        if (settings.social_links) {
            updateSocialLinks();
        }
    } catch (error) {
        console.error('Failed to load settings:', error);
        // Fallback settings - English default
        settings = {
            "default_language": "en",
            "languages": {
                "en": {
                    "intro_text": "I'm Igor, I'm a translator.",
                    "body_text": "I translate from English and Estonian into Russian in a human way.",
                    "image": "missing_en.png"
                }
            }
        };
    }
}

// Update social links from settings
function updateSocialLinks() {
    const socialButtons = document.querySelectorAll('.social-btn');
    socialButtons.forEach(button => {
        const socialType = Array.from(button.classList).find(cls => 
            cls !== 'social-btn' && settings.social_links[cls]
        );
        if (socialType && settings.social_links[socialType]) {
            button.href = settings.social_links[socialType];
        }
    });
}

// Update text content based on selected language
function updateContent(lang) {
    const introText = document.getElementById('intro-text');
    const bodyText = document.getElementById('body-text');
    const mainImage = document.getElementById('main-image');

    console.log('Updating content for language:', lang);
    console.log('Available languages:', Object.keys(settings.languages || {}));

    if (settings.languages && settings.languages[lang]) {
        const langData = settings.languages[lang];
        console.log('Language data:', langData);
        
        if (introText && langData.intro_text) {
            introText.textContent = langData.intro_text;
        }
        
        if (bodyText && langData.body_text) {
            bodyText.innerHTML = langData.body_text.replace(/\n/g, '<br>');
        }
        
        if (mainImage && langData.image) {
            mainImage.src = `./img/${langData.image}`;
            mainImage.alt = lang.toUpperCase();
        }
    } else {
        console.error('Language data not found for:', lang);
        // Fallback to English
        if (lang !== 'en' && settings.languages && settings.languages.en) {
            updateContent('en');
        }
    }
}

// Get language from URL parameter
function getLanguageFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const lang = urlParams.get('lang');
    const defaultLang = settings.default_language || 'en';
    return (lang === 'en' || lang === 'et' || lang === 'ru') ? lang : defaultLang;
}

// Switch language function
function switchLanguage(lang) {
    console.log('Switching to language:', lang);
    
    // Update active language button
    langButtons.forEach(btn => btn.classList.remove('active'));
    const activeButton = document.querySelector(`[data-lang="${lang}"]`);
    if (activeButton) {
        activeButton.classList.add('active');
    }
    
    // Update content
    updateContent(lang);
    
    currentLanguage = lang;
    
    // Update URL without reloading
    const url = new URL(window.location);
    url.searchParams.set('lang', lang);
    window.history.replaceState({}, '', url);
}

// Event listeners for language buttons
langButtons.forEach(button => {
    button.addEventListener('click', () => {
        const lang = button.getAttribute('data-lang');
        console.log('Language button clicked:', lang);
        switchLanguage(lang);
    });
});

// Initialize with language from URL or default to English
document.addEventListener('DOMContentLoaded', async function() {
    console.log('DOM loaded, initializing...');
    await loadSettings();
    const initialLang = getLanguageFromURL();
    console.log('Initial language:', initialLang);
    switchLanguage(initialLang);
});