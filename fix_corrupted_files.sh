#!/bin/bash
# Script to identify and remove corrupted files that block git operations

# Clean up any existing git process and locks
pkill -9 git 2>/dev/null
rm -f .git/index.lock

# Reset git state to clean slate
git reset

# Function to attempt git add and identify the next corrupted file
find_and_fix_corrupted() {
  # Try to add everything
  git add . > /dev/null 2> git_add_errors.txt
  
  # Check if there were any errors
  if [ $? -eq 0 ]; then
    echo "No more corrupted files found!"
    return 0
  fi
  
  # Extract the filename of the corrupted file from the error message
  corrupted_file=$(grep "unable to index file" git_add_errors.txt | head -1 | sed "s/error: unable to index file '//g" | sed "s/'//g")
  
  if [ -z "$corrupted_file" ]; then
    echo "No specific file identified in error message."
    cat git_add_errors.txt
    return 1
  fi
  
  echo "Found corrupted file: $corrupted_file"
  
  # Check if file exists before attempting to remove
  if [ -f "$corrupted_file" ]; then
    echo "Removing corrupted file: $corrupted_file"
    rm -f "$corrupted_file"
    echo "File removed."
    return 2
  else
    echo "Warning: File $corrupted_file doesn't exist or can't be accessed."
    return 1
  fi
}

# Main loop to find and fix all corrupted files
echo "Starting corruption cleanup..."
result=2

while [ $result -eq 2 ]; do
  find_and_fix_corrupted
  result=$?
done

if [ $result -eq 0 ]; then
  echo "All corrupted files have been removed."
  echo "You can now commit your changes with: git commit -m \"Add all files after removing corrupted ones\""
else
  echo "Process stopped due to an error or unknown issue."
  echo "Check git_add_errors.txt for more details."
fi
