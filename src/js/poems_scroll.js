// Reset poems navigation scroll to top on page load
// Use multiple triggers to ensure it works
function resetPoemsNavScroll() {
    const poemsNav = document.querySelector('.poems-nav');
    if (poemsNav) {
        poemsNav.scrollTop = 0;
    }
}

// Try immediately when DOM is ready
document.addEventListener('DOMContentLoaded', resetPoemsNavScroll);

// Try again when page fully loads (after all resources)
window.addEventListener('load', function() {
    resetPoemsNavScroll();
    // And once more with a tiny delay to override any browser scroll restoration
    setTimeout(resetPoemsNavScroll, 50);
});

// Highlight active poem link in navigation based on scroll position
document.addEventListener('DOMContentLoaded', function() {
    const timeline = document.querySelector('.poems-timeline');
    const navLinks = document.querySelectorAll('.poem-title-link');
    const poems = document.querySelectorAll('.poem-item');
    
    if (!timeline || !navLinks.length || !poems.length) return;
    
    let scrollTimeout;
    
    function updateActiveLink() {
        let currentActive = null;
        
        // Find which poem is currently most visible in timeline
        poems.forEach(poem => {
            const rect = poem.getBoundingClientRect();
            const timelineRect = timeline.getBoundingClientRect();
            
            // Check if poem is in viewport of timeline (within first 30% of visible area)
            if (rect.top >= timelineRect.top && rect.top <= timelineRect.top + timelineRect.height * 0.3) {
                currentActive = poem.id;
            }
        });
        
        // Update active class on navigation links
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href.startsWith('#')) {
                const targetId = href.substring(1);
                if (targetId === currentActive) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            }
        });
    }
    
    // Update only after scroll stops (debounce)
    timeline.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(updateActiveLink, 150);
    });
    
    // Update on hash change (when clicking nav links)
    window.addEventListener('hashchange', function() {
        setTimeout(updateActiveLink, 100);
    });
    
    // Initial update
    setTimeout(updateActiveLink, 100);
});
