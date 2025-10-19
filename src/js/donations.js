// PayPal popup + Crypto copy handlers
(function(){
  const btn = document.querySelector('.paypal-btn');
  if (btn) {
    btn.addEventListener('click', function(e){
      e.preventDefault();
      const url = this.getAttribute('href');
      const w = 640, h = 720;
      const y = Math.max(0, (window.screen.height - h) / 2);
      const x = Math.max(0, (window.screen.width - w) / 2);
      const features = `noopener,noreferrer,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=${w},height=${h},left=${x},top=${y}`;
      let newWin = window.open(url, 'paypal_popup', features);
      if (!newWin || newWin.closed) {
        newWin = window.open(url, '_blank', 'noopener,noreferrer');
      }
    });
  }

  const container = document.getElementById('crypto-container');
  if (container) {
    container.querySelectorAll('.crypto-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const address = btn.getAttribute('data-address');
        if (!address) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(address).then(() => showCopied(btn)).catch(() => fallbackPrompt(address));
        } else {
          fallbackPrompt(address);
        }
      });
    });
  }

  function showCopied(btn){
    const text = btn.querySelector('.copy-text');
    if (!text) return;
    const prev = { t: text.textContent, c: text.style.color };
    text.textContent = 'Copied!';
    text.style.color = 'green';
    setTimeout(() => { text.textContent = prev.t || 'Click to Copy'; text.style.color = prev.c || '#c00'; }, 1500);
  }
  function fallbackPrompt(address){
    window.prompt('Copy address:', address);
  }
})();