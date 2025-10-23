// Overlay arrows for land page that do not affect layout or scroll
// Draws two slanted lines with arrowheads, anchored to the red coffee link text

(function(){
  const root = document.documentElement;
  let svg, leftLine, rightLine, leftHead, rightHead;

  function createOverlay(){
    svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('id', 'arrow-overlay');
    svg.setAttribute('width', String(window.innerWidth));
    svg.setAttribute('height', String(window.innerHeight));
    svg.setAttribute('viewBox', `0 0 ${window.innerWidth} ${window.innerHeight}`);
    svg.style.position = 'fixed';
    svg.style.left = '0';
    svg.style.top = '0';
    svg.style.width = '100vw';
    svg.style.height = '100vh';
    svg.style.pointerEvents = 'none';
    svg.style.zIndex = '2147483649'; // above any widget

    leftLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
    rightLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
    leftHead = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
    rightHead = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');

    for (const el of [leftLine, rightLine]){
      el.setAttribute('stroke', getAccent());
      el.setAttribute('stroke-width', '3');
      el.setAttribute('stroke-linecap', 'butt');
    }
    for (const el of [leftHead, rightHead]){
      el.setAttribute('fill', getAccent());
    }

    svg.appendChild(leftLine);
    svg.appendChild(rightLine);
    svg.appendChild(leftHead);
    svg.appendChild(rightHead);
    document.body.appendChild(svg);
  }

  function getAccent(){
    const v = getComputedStyle(root).getPropertyValue('--accent').trim();
    return v || '#e74c3c';
  }

  function getArrowAngleRad(){
    const v = getComputedStyle(root).getPropertyValue('--arrow-angle-deg').trim();
    const deg = parseFloat(v || '5');
    return (deg * Math.PI) / 180;
  }

  function getArrowLengthPx(){
    let v = getComputedStyle(root).getPropertyValue('--arrow-length').trim();
    if (!v) return window.innerWidth * 0.33;
    if (v.endsWith('vw')){
      const n = parseFloat(v);
      if (!isNaN(n)) return window.innerWidth * (n/100);
    }
    if (v.endsWith('px')){
      const n = parseFloat(v);
      if (!isNaN(n)) return n;
    }
    const n = parseFloat(v);
    return isNaN(n) ? window.innerWidth * 0.33 : n;
  }

  function update(){
    const link = document.querySelector('.coffee-link .link-text') || document.querySelector('.coffee-link');
    if (!link){
      if (svg) svg.style.display = 'none';
      return;
    }
    if (!svg) createOverlay();
    svg.style.display = 'block';

    // Base metrics
    const rect = link.getBoundingClientRect();
    const angle = getArrowAngleRad();
    const len = getArrowLengthPx();
    const padY = 5; // small gap under text

    const startY = rect.bottom + padY;
    const startLeftX = rect.left;   // left edge under text
    const startRightX = rect.right; // right edge under text

    // Compute ends and clamp to viewport
    const dx = Math.cos(angle) * len;
    const dy = Math.sin(angle) * len;

    // Left arrow goes to the left
    let endLeftX = Math.max(4, startLeftX - dx);
    let endLeftY = startY + dy;

    // Right arrow goes to the right
    let endRightX = Math.min(window.innerWidth - 4, startRightX + dx);
    let endRightY = startY + dy;

    // Update line positions
    leftLine.setAttribute('x1', String(startLeftX));
    leftLine.setAttribute('y1', String(startY));
    leftLine.setAttribute('x2', String(endLeftX));
    leftLine.setAttribute('y2', String(endLeftY));

    rightLine.setAttribute('x1', String(startRightX));
    rightLine.setAttribute('y1', String(startY));
    rightLine.setAttribute('x2', String(endRightX));
    rightLine.setAttribute('y2', String(endRightY));

    // Arrowheads (triangles) at ends
    const tipLen = 12, tipHalf = 8;

    function headPoints(x1, y1, x2, y2){
      // Direction from start -> end
      const vx = x2 - x1, vy = y2 - y1;
      const mag = Math.max(1, Math.hypot(vx, vy));
      const ux = vx / mag, uy = vy / mag; // unit along line
      // Perpendicular
      const px = -uy, py = ux;
      const tipX = x2, tipY = y2;
      const baseX = x2 - ux * tipLen, baseY = y2 - uy * tipLen;
      const p1x = baseX + px * tipHalf, p1y = baseY + py * tipHalf;
      const p2x = baseX - px * tipHalf, p2y = baseY - py * tipHalf;
      return `${tipX},${tipY} ${p1x},${p1y} ${p2x},${p2y}`;
    }

    leftHead.setAttribute('points', headPoints(startLeftX, startY, endLeftX, endLeftY));
    rightHead.setAttribute('points', headPoints(startRightX, startY, endRightX, endRightY));

    // Keep SVG sized to viewport
    svg.setAttribute('width', String(window.innerWidth));
    svg.setAttribute('height', String(window.innerHeight));
    svg.setAttribute('viewBox', `0 0 ${window.innerWidth} ${window.innerHeight}`);
  }

  function onReady(){
    // Hide CSS pseudo-element arrows on this page to avoid duplication
    document.documentElement.classList.add('land-overlay-arrows');
    update();
    window.addEventListener('resize', update, { passive: true });
    const scroller = document.querySelector('.content');
    if (scroller) scroller.addEventListener('scroll', update, { passive: true });
  }

  if (document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', onReady);
  } else {
    onReady();
  }
})();
