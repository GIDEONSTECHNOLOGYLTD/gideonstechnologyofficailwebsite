/**
 * Main script file for site functionality
 */
(function() {
    // Initialize on document ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeComponents();
        setupEventHandlers();
    });

    function initializeComponents() {
        // Initialize any third-party components
        if (typeof bootstrap !== 'undefined') {
            // Initialize all tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Initialize all popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        }
    }

    function setupEventHandlers() {
        // Add any global event handlers here
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                // Basic form validation
                var required = form.querySelectorAll('[required]');
                var valid = true;
                
                required.forEach(function(field) {
                    if (!field.value.trim()) {
                        valid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (!valid) {
                    e.preventDefault();
                }
            });
        });
    }
})();