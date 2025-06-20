# Route Registration System Documentation

## Overview

The route registration system in Gideon's Technology has been updated to prevent duplicate route registrations, which were causing 500 Internal Server errors. The solution implements a comprehensive approach using global flags, the RouteRegistry class, and proper middleware configuration.

## Latest Updates

- Added a proper homepage route with Bootstrap styling
- Ensured all routes use the RouteRegistry pattern to prevent duplicates
- Modified `.gitignore` to allow tracking important files in the public directory
- Created route testing tools to verify route functionality

## Key Components

### 1. RouteRegistry Class

The `RouteRegistry` class is used to track and manage route registrations across the application. It provides two main methods:

- `isRegistered(string $method, string $pattern): bool` - Checks if a route with the given method and pattern is already registered
- `register(string $method, string $pattern): void` - Registers a route with the given method and pattern

### 2. Global Flag System

Global flags are used to prevent route files from being loaded multiple times:

```php
// Define global flags to prevent duplicate loading
if (!defined('MAIN_ROUTES_REGISTERED')) {
    define('MAIN_ROUTES_REGISTERED', true);
}

if (!defined('AUTH_ROUTES_REGISTERED')) {
    define('AUTH_ROUTES_REGISTERED', true);
}

if (!defined('GSTORE_ROUTES_REGISTERED')) {
    define('GSTORE_ROUTES_REGISTERED', true);
}

if (!defined('USER_ROUTES_REGISTERED')) {
    define('USER_ROUTES_REGISTERED', true);
}

if (!defined('API_ROUTES_REGISTERED')) {
    define('API_ROUTES_REGISTERED', true);
}
```

### 3. Middleware Configuration

Middleware is added in the correct order to ensure proper routing and error handling:

1. Routing middleware (first)
2. Body parsing middleware (second)
3. Error middleware (last)

```php
// Add middleware in the correct order
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);
```

## Route Registration Pattern

All route files now follow this pattern to prevent duplicate registrations:

```php
// Check if the route is already registered
if (!RouteRegistry::isRegistered('GET', '/example-route')) {
    // Register the route
    RouteRegistry::register('GET', '/example-route');
    // Add the route to the application
    $app->get('/example-route', function ($request, $response) {
        // Route handler code
        return $response;
    });
}
```

## Route Loading Sequence

Routes are loaded in a specific order to ensure dependencies are met:

1. Core routes (`routes.php`)
2. Authentication routes (`auth.php`)
3. User account routes (`user.php`)
4. GStore routes (`gstore.php`)
5. API endpoints (`api.php`)

## Error Handling

Each route file is loaded with proper error handling to catch and log any issues:

```php
try {
    if (file_exists($routePath)) {
        $routes = require $routePath;
        if (is_callable($routes)) {
            $routes($app, $container);
            error_log("Successfully loaded routes from {$routeFile}");
        } else {
            error_log("Warning: {$routeFile} did not return a callable function");
        }
    } else {
        error_log("Warning: Route file not found: {$routeFile}");
    }
} catch (\Exception $e) {
    error_log("Error loading {$routeFile}: " . $e->getMessage());
}
```

## Troubleshooting

If you encounter route registration issues:

1. Check for duplicate route registrations using the browser console or server logs
2. Ensure all route files are using the RouteRegistry pattern
3. Verify that global flags are set correctly
4. Check the middleware order in `index.php`

## Testing Routes

To test if your routes are working correctly, you can use one of the following approaches:

1. **Direct Browser Testing**: Navigate to the route in your browser (e.g., `http://localhost:8000/auth/login`)

2. **Route Tester Tool**: Use the `test-route.php` script to test individual routes:
   ```
   http://localhost:8000/test-route.php?route=/auth/login
   ```

3. **Command Line Testing**: Use curl to test routes from the command line:
   ```bash
   curl -i http://localhost:8000/auth/login
   ```

4. **Systematic Testing**: Run the route-tester.php script to test all routes at once:
   ```
   http://localhost:8000/route-tester.php
   ```
