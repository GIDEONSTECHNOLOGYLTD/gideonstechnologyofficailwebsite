/**
 * jQuery Selector Fix
 * This script patches jQuery's selector functionality to handle invalid selectors properly
 */

(function() {
    // Store the original jQuery init function
    var originalInit = jQuery.fn.init;
    
    // Override the jQuery init function to catch invalid selectors
    jQuery.fn.init = function(selector, context, root) {
        try {
            // Attempt to use the original initialization
            return new originalInit(selector, context, root);
        } catch (e) {
            if (e.message && e.message.indexOf('querySelectorAll') !== -1) {
                console.warn("jQuery selector fix: Invalid selector detected - '" + selector + "'");
                
                // Return an empty jQuery object instead of throwing an error
                return new originalInit(document.createDocumentFragment());
            }
            
            // For other errors, rethrow
            throw e;
        }
    };
    
    // Ensure the prototype chain is maintained
    jQuery.fn.init.prototype = jQuery.fn;
    
    console.log("jQuery selector fix applied successfully");
})();