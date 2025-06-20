// Responsible for initializing SES lockdown with proper configuration
// This file should be loaded before any other JavaScript in the application

import './lockdown-config.js';

console.log('SES lockdown installed successfully');

// Prevent 'Removing unpermitted intrinsics' error by applying additional protection
// for JavaScript intrinsics before application code runs
(function protectIntrinsics() {
  try {
    // Ensure Math, Date, and other core objects are protected
    Object.freeze(Math);
    Object.freeze(Date);
    Object.freeze(RegExp);
    Object.freeze(String.prototype);
    Object.freeze(Number.prototype);
    Object.freeze(Array.prototype);
    Object.freeze(Object.prototype);
    Object.freeze(Function.prototype);
    
    console.log('Intrinsics protection applied successfully');
  } catch (err) {
    console.warn('Could not fully protect intrinsics:', err);
  }
})();