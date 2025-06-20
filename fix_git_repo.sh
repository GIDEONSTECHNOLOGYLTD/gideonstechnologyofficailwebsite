#!/bin/bash
# Script to identify and fix corrupted files in git repository

# Clean any locks
rm -f .git/index.lock

# Ensure we're in a clean state
git reset

# Remove the already identified corrupted files
rm -f app.php
rm -f app/providers/EventServiceProvider.php

# Function to identify next corrupted file
find_next_corrupted_file() {
  git add . > /dev/null 2> git_errors.txt
  if [ $? -eq 0 ]; then
    echo "No more corrupted files found!"
    return 0
  fi
  
  local file=$(grep "unable to index file" git_errors.txt | sed "s/error: unable to index file '//g" | sed "s/'//g")
  if [ -n "$file" ]; then
    echo "Found corrupted file: $file"
    echo "Removing $file"
    rm -f "$file"
    return 1
  else
    echo "Unknown error occurred:"
    cat git_errors.txt
    return 2
  fi
}

# Main loop to keep removing corrupted files until none remain
echo "Starting corruption cleanup..."
while true; do
  find_next_corrupted_file
  status=$?
  if [ $status -eq 0 ]; then
    break
  elif [ $status -eq 2 ]; then
    echo "Encountered an error. Stopping."
    exit 1
  fi
done

# Final cleanup and commit
echo "All corrupted files removed!"
echo "Committing all clean files..."
git add .
git commit -m "Add all clean files after removing corrupted ones"
echo "Done!"
