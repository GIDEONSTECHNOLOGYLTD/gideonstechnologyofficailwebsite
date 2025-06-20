/**
 * Asks the user whether to continue iteration and returns their response
 * @returns {boolean} True if user wants to continue, false otherwise
 */
function continueToIterate() {
  // Get user confirmation
  const response = prompt("Continue to iterate? (y/n)");
  
  // Return true if the response starts with 'y' or 'Y'
  return response && response.toLowerCase().startsWith('y');
}

// Example usage:
// while(continueToIterate()) {
//   // perform iteration
//   performIteration();
// }