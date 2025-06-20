// Function to ask if user wants to continue iterating
function continueToIterate() {
    let keepGoing = true;
    let count = 0;
    
    while (keepGoing) {
        count++;
        console.log(`Iteration ${count}`);
        
        // In a real application, you would get user input here
        // For example with readline in Node.js or prompt in browser
        const userInput = getUserInput("Continue to iterate? (yes/no): ");
        
        if (userInput.toLowerCase() !== 'yes') {
            keepGoing = false;
            console.log("Iteration stopped");
        }
    }
}

// Simulated function to get user input (replace with actual implementation)
function getUserInput(prompt) {
    // In a browser environment:
    // return window.prompt(prompt);
    
    // In a Node.js environment:
    // const readline = require('readline-sync');
    // return readline.question(prompt);
    
    console.log(prompt);
    return "yes"; // Simulated response for demonstration
}

// Start the process
continueToIterate();