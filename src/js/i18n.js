export function getLanguageFromURL(settings) {
  const urlParams = new URLSearchParams(window.location.search);
  const lang = urlParams.get('lang');
  const defaultLang = settings.default_language || 'en';
  return (lang === 'en' || lang === 'et' || lang === 'ru') ? lang : defaultLang;
}

export function updateContent(settings, lang) {
  const introText = document.getElementById('intro-text');
  const bodyText = document.getElementById('body-text');
  const mainImage = document.getElementById('main-image');

  if (settings.languages && settings.languages[lang]) {
    const langData = settings.languages[lang];
    if (introText && langData.intro_text) introText.textContent = langData.intro_text;
    if (bodyText && langData.body_text) {
      bodyText.innerHTML = langData.body_text.replace(/\n/g, '<br>');
      setTimeout(() => {
        const coffeeLink = bodyText.querySelector('.coffee-link');
        if (coffeeLink && !coffeeLink.querySelector('.link-text')) {
          const inner = document.createElement('span');
          inner.className = 'link-text';
          inner.textContent = coffeeLink.textContent;
          coffeeLink.textContent = '';
          coffeeLink.appendChild(inner);
        }
      }, 50);
    }
    if (mainImage && langData.image) {
      mainImage.src = `./img/${langData.image}`;
      mainImage.alt = lang.toUpperCase();
    }
  }
}
