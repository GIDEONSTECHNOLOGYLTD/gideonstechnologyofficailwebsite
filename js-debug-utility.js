/**
 * JavaScript Debug Utility
 * 
 * This script helps diagnose common JavaScript loading issues:
 * 1. HTML syntax errors in JavaScript files (Unexpected token '<')
 * 2. jQuery selector errors
 * 3. Bootstrap loading issues
 * 4. Message channel errors
 */

// Self-executing function to avoid polluting global namespace
(function() {
  console.log('Debug utility loaded');
  
  // Check if files are loading correctly
  function checkResourceLoading() {
    const resources = document.querySelectorAll('script, link');
    console.log('Checking resource loading status:');
    
    resources.forEach(resource => {
      const type = resource.tagName.toLowerCase();
      const src = type === 'script' ? resource.src : resource.href;
      
      if (src) {
        fetch(src, { method: 'HEAD' })
          .then(response => {
            if (!response.ok) {
              console.error(`Resource loading error: ${src} - Status: ${response.status}`);
            } else {
              console.log(`Resource loaded successfully: ${src}`);
            }
          })
          .catch(err => {
            console.error(`Failed to fetch resource: ${src}`, err);
          });
      }
    });
  }
  
  // Fix common jQuery issues
  function fixJQueryIssues() {
    if (typeof jQuery !== 'undefined') {
      console.log('jQuery version:', jQuery.fn.jquery);
      
      // Safely patch querySelectorAll issues
      const originalInit = jQuery.fn.init;
      jQuery.fn.init = function(selector, context, root) {
        try {
          return new originalInit(selector, context, root);
        } catch(e) {
          console.warn('jQuery selector error:', e.message, 'for selector:', selector);
          return new originalInit(document, context, root);
        }
      };
      jQuery.fn.init.prototype = originalInit.prototype;
      
      console.log('Applied jQuery selector error protection');
    } else {
      console.warn('jQuery not found on page');
    }
  }
  
  // Monitor for SyntaxErrors that might indicate HTML in JS files
  function monitorScriptErrors() {
    window.addEventListener('error', function(event) {
      if (event.error instanceof SyntaxError && 
          event.error.message.includes('Unexpected token')) {
        console.error('Possible HTML in JS file:', event.filename);
        console.info('This usually happens when a server returns an HTML error page instead of a JS file');
      }
    }, true);
  }
  
  // Initialize debugging
  function init() {
    console.group('JavaScript Debug Utility');
    
    // Wait for DOM to be fully loaded
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', runChecks);
    } else {
      runChecks();
    }
    
    function runChecks() {
      checkResourceLoading();
      fixJQueryIssues();
      monitorScriptErrors();
      console.log('All debug checks initialized');
    }
    
    console.groupEnd();
  }
  
  init();
})();