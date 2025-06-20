#!/bin/bash

echo "Fetching latest changes..."
git fetch origin main

echo "Stashing your local changes..."
git stash

echo "Resetting to match remote main branch..."
git reset --hard origin/main

echo "Reapplying your local changes..."
git stash pop

echo "Your repository is now synchronized with remote."
echo "You can now try to commit and push your changes again."