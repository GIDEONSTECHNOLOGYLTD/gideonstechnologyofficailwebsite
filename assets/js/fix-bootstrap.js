// Bootstrap loader and fixer
(function() {
  // Check if Bootstrap is already loaded
  if (typeof bootstrap === 'undefined') {
    console.log('Bootstrap not detected, loading from CDN...');
    
    // Create script element for Bootstrap
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js';
    script.integrity = 'sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p';
    script.crossOrigin = 'anonymous';
    
    // Add load event handler
    script.onload = function() {
      console.log('Bootstrap loaded successfully from CDN');
      initBootstrapComponents();
    };
    
    // Add error handler
    script.onerror = function() {
      console.error('Failed to load Bootstrap from CDN');
      // Try to load from local fallback if CDN fails
      var fallbackScript = document.createElement('script');
      fallbackScript.src = 'assets/js/bootstrap.bundle.min.js';
      fallbackScript.onload = function() {
        console.log('Bootstrap loaded from local fallback');
        initBootstrapComponents();
      };
      document.head.appendChild(fallbackScript);
    };
    
    document.head.appendChild(script);
  } else {
    console.log('Bootstrap already loaded');
    initBootstrapComponents();
  }
  
  // Initialize Bootstrap components
  function initBootstrapComponents() {
    if (typeof bootstrap !== 'undefined') {
      // Initialize tooltips
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      if (tooltipTriggerList.length > 0) {
        tooltipTriggerList.map(function(tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
      }
      
      // Initialize popovers
      var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
      if (popoverTriggerList.length > 0) {
        popoverTriggerList.map(function(popoverTriggerEl) {
          return new bootstrap.Popover(popoverTriggerEl);
        });
      }
    }
  }
})();