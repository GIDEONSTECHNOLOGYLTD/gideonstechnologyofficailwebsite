// Script loader to avoid errors with missing or corrupted scripts
(function() {
  // Function to load a script with error handling
  function loadScript(src, callback) {
    var script = document.createElement('script');
    script.src = src;
    script.async = false;
    
    script.onload = function() {
      console.log('Script loaded: ' + src);
      if (callback) callback(null);
    };
    
    script.onerror = function() {
      console.error('Error loading script: ' + src);
      if (callback) callback(new Error('Script load error: ' + src));
    };
    
    document.head.appendChild(script);
  }
  
  // Load scripts in sequence
  function loadScriptsSequentially(scripts, index) {
    if (!index) index = 0;
    
    if (index < scripts.length) {
      loadScript(scripts[index], function(error) {
        if (!error) {
          loadScriptsSequentially(scripts, index + 1);
        }
      });
    }
  }
  
  // Wait for document ready
  document.addEventListener('DOMContentLoaded', function() {
    // List of scripts to load in order
    var scripts = [
      'assets/js/jquery-3.6.0.min.js',
      'assets/js/jquery-migrate.min.js',
      'assets/js/jquery-fix.js',
      'assets/js/bootstrap.bundle.min.js',
      'assets/js/wow.min.js',
      'assets/js/main.js',
      'assets/js/scripts.js'
    ];
    
    loadScriptsSequentially(scripts);
  });
})();