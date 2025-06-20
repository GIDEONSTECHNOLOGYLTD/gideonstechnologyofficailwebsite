// jQuery selector fix script
(function() {
  // Wait for jQuery to be defined
  function checkjQuery() {
    if (typeof jQuery !== 'undefined') {
      // Store original jQuery init function
      var originalInit = jQuery.fn.init;
      
      // Create a patched version that fixes the invalid selector
      jQuery.fn.init = function(selector, context, root) {
        // Check if it's a string selector and has an invalid self-closing tag
        if (typeof selector === 'string' && 
            selector.indexOf('<') === 0 && 
            selector.indexOf('/>') > 0) {
          // Fix the selector by converting self-closing tags to regular ones
          selector = selector.replace('/>', '>');
          console.log("Fixed invalid selector:", selector);
        }
        
        // Call the original init function with fixed selector
        return originalInit.call(this, selector, context, root);
      };
      
      // Ensure prototype chain is maintained
      jQuery.fn.init.prototype = jQuery.fn;
      
      console.log("jQuery selector fix applied successfully");
    } else {
      // If jQuery isn't loaded yet, check again in 50ms
      setTimeout(checkjQuery, 50);
    }
  }
  
  // Start checking for jQuery
  checkjQuery();
})();