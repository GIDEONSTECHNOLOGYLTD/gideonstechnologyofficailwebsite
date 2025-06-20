/**
 * JavaScript Error Diagnostic Tool
 * 
 * This script helps diagnose common JavaScript errors seen in the console.
 */

(function() {
  console.log('Diagnostics script running...');
  
  // Check if jQuery is loaded properly
  function checkJQuery() {
    if (typeof jQuery === 'undefined') {
      console.error('jQuery is not loaded properly');
    } else {
      console.log('jQuery version: ' + jQuery.fn.jquery);
      
      // Test jQuery selector that's causing issues
      try {
        jQuery('<input/>');
        console.log('jQuery selector test passed');
      } catch (e) {
        console.error('jQuery selector test failed:', e.message);
      }
    }
  }
  
  // Check if Bootstrap is loaded properly
  function checkBootstrap() {
    if (typeof bootstrap === 'undefined') {
      console.error('Bootstrap is not loaded properly');
    } else {
      console.log('Bootstrap appears to be loaded');
    }
  }
  
  // Check script loading order and MIME types
  function checkScripts() {
    const scripts = document.getElementsByTagName('script');
    console.log('Scripts loading order:');
    for (let i = 0; i < scripts.length; i++) {
      console.log(`${i+1}. ${scripts[i].src || 'Inline script'} (type: ${scripts[i].type || 'text/javascript'})`);
    }
  }
  
  // Check for HTML in JavaScript files
  function checkHtmlInJs() {
    console.log('Checking for potential HTML in JS files...');
    // This is just a notification - actual check requires examining the files
    console.log('Several errors indicate "<" tokens in JS files, suggesting HTML content in JS files');
  }
  
  // Run diagnostics
  window.runJsDiagnostics = function() {
    console.log('=== JAVASCRIPT DIAGNOSTICS ===');
    checkJQuery();
    checkBootstrap();
    checkScripts();
    checkHtmlInJs();
    console.log('=== DIAGNOSTICS COMPLETE ===');
    
    return {
      fixRecommendations: [
        '1. Ensure scripts are being served with correct MIME types',
        '2. Check that email-decode.min.js and scripts.js contain JavaScript, not HTML',
        '3. Load jQuery before jQuery-migrate',
        '4. Verify script paths in your HTML are correct',
        '5. Consider using defer or async attributes for script tags'
      ]
    };
  };
  
  // Auto-run if in browser context
  if (typeof window !== 'undefined') {
    // Wait for DOM to be ready
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
      setTimeout(window.runJsDiagnostics, 1000);
    } else {
      document.addEventListener('DOMContentLoaded', function() {
        setTimeout(window.runJsDiagnostics, 1000);
      });
    }
  }
})();