import { getLanguageFromURL, updateContent } from './i18n.js';
import { applyThemeVariables, updateSocialLinks, renderVersionBadge } from './ui.js';

let settings = {};

async function loadSettings() {
  const response = await fetch('settings.json');
  settings = await response.json();
  // First apply theme variables so CSS vars are ready
  applyThemeVariables(settings);
  // Apply background image (from theme only)
  const bgImage = settings.theme?.background_image;
  if (bgImage) {
    document.body.style.background = `url('./img/${bgImage}') center/cover, linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%)`;
  }
  updateSocialLinks(settings);
  renderVersionBadge(settings);
}

function wireLangButtons() {
  const langButtons = document.querySelectorAll('.lang-btn');
  langButtons.forEach(button => {
    button.addEventListener('click', () => {
      const lang = button.getAttribute('data-lang');
      switchLanguage(lang);
    });
  });
}

function switchLanguage(lang) {
  const langButtons = document.querySelectorAll('.lang-btn');
  langButtons.forEach(btn => btn.classList.remove('active'));
  const activeButton = document.querySelector(`[data-lang="${lang}"]`);
  if (activeButton) activeButton.classList.add('active');
  updateContent(settings, lang);
  const url = new URL(window.location);
  url.searchParams.set('lang', lang);
  window.history.replaceState({}, '', url);
}

document.addEventListener('DOMContentLoaded', async () => {
  await loadSettings();
  wireLangButtons();
  const initialLang = getLanguageFromURL(settings);
  switchLanguage(initialLang);
});
