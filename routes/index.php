<?php
/**
 * Route Index
 * 
 * This file serves as the entry point for loading all route files.
 * It ensures routes are loaded in the correct order and prevents duplicate routes.
 */

// Do not define any routes directly in this file
// This file only manages which route files should be loaded

// All route files should return a callable function that accepts the app instance:
// return function (App $app) { ... };