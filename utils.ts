function askToContinue(): boolean {
    const answer = prompt('Continue to iterate? (y/n)');
    return answer?.toLowerCase() === 'y';
}

function iterate() {
    let shouldContinue = true;
    while (shouldContinue) {
        // Perform iteration logic here

        shouldContinue = askToContinue();
    }
}