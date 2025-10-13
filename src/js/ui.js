export function applyThemeVariables(settings) {
  if (!settings || !settings.theme) return;
  const root = document.documentElement;
  const t = settings.theme;
  if (t.gradient_start) root.style.setProperty('--gradient-start', t.gradient_start);
  if (t.gradient_end) root.style.setProperty('--gradient-end', t.gradient_end);
  if (t.accent) root.style.setProperty('--accent', t.accent);
  if (typeof t.widget_margin === 'number') {
    root.style.setProperty('--widget-margin', `${t.widget_margin}px`);
  }
  if (typeof t.widget_height === 'number') {
    root.style.setProperty('--widget-height', `${t.widget_height}px`);
  }
  if (typeof t.arrow_angle_deg === 'number') {
    root.style.setProperty('--arrow-angle-deg', String(t.arrow_angle_deg));
    // precompute radians and slope factor used in tip vertical offset (tan(angle))
    const radians = (t.arrow_angle_deg * Math.PI) / 180;
    root.style.setProperty('--arrow-angle-rad', String(radians));
    root.style.setProperty('--arrow-slope', String(Math.tan(radians)));
  }
  if (typeof t.arrow_length_vw === 'number') {
    root.style.setProperty('--arrow-length', `${t.arrow_length_vw}vw`);
  }
  if (typeof t.container_bg_alpha === 'number') {
    const alpha = Math.min(Math.max(t.container_bg_alpha, 0), 1);
    let styleEl = document.getElementById('dynamic-theme');
    if (!styleEl) {
      styleEl = document.createElement('style');
      styleEl.id = 'dynamic-theme';
      document.head.appendChild(styleEl);
    }
    styleEl.textContent = `.container{background:rgba(255,255,255,${alpha});}`;
  }
}

export function updateSocialLinks(settings) {
  const socialButtons = document.querySelectorAll('.social-btn');
  socialButtons.forEach(button => {
    const socialType = Array.from(button.classList).find(cls => cls !== 'social-btn' && settings.social_links?.[cls]);
    if (socialType && settings.social_links[socialType]) {
      button.href = settings.social_links[socialType];
    }
  });
}

export function renderVersionBadge(settings) {
  if (!settings || !settings.version) return;
  let badge = document.getElementById('version-badge');
  if (!badge) {
    badge = document.createElement('div');
    badge.id = 'version-badge';
    badge.className = 'version-badge';
    const container = document.querySelector('.container');
    if (container) {
      container.style.position = container.style.position || 'relative';
      container.appendChild(badge);
    } else {
      document.body.appendChild(badge);
    }
  }
  badge.textContent = `v${settings.version}`;
}
