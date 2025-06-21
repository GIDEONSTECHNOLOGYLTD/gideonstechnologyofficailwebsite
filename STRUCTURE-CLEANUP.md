# Project Structure Cleanup Guide

This guide provides instructions for standardizing your project structure based on the improvements we've made.

## Directory Structure

The standardized directory structure for your project should be:

```
/app
  /controllers       - Web and API controllers (organized by feature)
  /models            - Business logic models
  /core              - Framework core components
  /providers         - Service providers for application components
  /config            - Application configuration

/config              - Root level configuration (primary)

/database
  /migrations        - Database migrations (PHP format only)
  /seeds             - Database seeders for test/sample data

/public              - Publicly accessible files (web root)
  /assets            - CSS, JS, images, etc.
  
/resources           - Non-public resources
  /views             - View templates
  /lang              - Language files
  
/storage             - Generated files, logs, cache, etc.

/vendor              - Composer dependencies
```

## Duplicate File Cleanup

1. **Database-related classes:**
   
   - Keep: 
     - `/database/Migration.php`
     - `/database/Schema.php`
     - `/database/Blueprint.php`
   
   - Remove:
     - `/app/core/Migration.php` 
     - `/app/core/Database/Migration.php`
     - `/app/core/Blueprint.php`
     - `/app/core/Database/Blueprint.php`
     - `/app/core/Schema.php` (if exists)

2. **Migrations:**
   
   - Keep PHP-based migrations in the `/database/migrations/` directory
   - Remove `.sql` migrations (after converting to PHP format if needed)
   - Use the standardized migration format (extending `Database\Migration`)

3. **Config files:**
   
   - Keep the root `/config/*.php` files as primary
   - Phase out the `/app/config/*.php` files (merge any unique settings)

4. **Controllers:**
   
   - Ensure all controllers extend `App\Controllers\BaseController`
   - API controllers should extend `App\Controllers\Api\ApiBaseController`

5. **Models:**
   
   - Ensure all models extend `App\Models\BaseModel`
   - Remove any duplicate model implementations

6. **Database connections:**
   
   - Use the database connection from `App\Providers\DatabaseServiceProvider`
   - Remove any direct PDO connections in other files

## How to Use the New Structure

### Creating Models

```php
namespace App\Models;

class User extends BaseModel
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
}
```

### Creating Controllers

```php
namespace App\Controllers;

class UserController extends BaseController
{
    public function index()
    {
        $users = (new \App\Models\User)->all();
        return $this->render('users/index', ['users' => $users]);
    }
}
```

### Creating API Controllers

```php
namespace App\Controllers\Api;

class UserController extends ApiBaseController
{
    public function index()
    {
        $users = (new \App\Models\User)->all();
        return $this->success($users, 'Users retrieved successfully');
    }
}
```

### Creating Migrations

Use the command:

```
php console.php db make:migration create_new_table
```

Then implement the migration:

```php
namespace Database\Migrations;

use Database\Migration;

class CreateNewTable extends Migration
{
    public function up()
    {
        $this->schema->create('new_table', function($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->dropIfExists('new_table');
    }
}
```

### Service Providers

To create a new service provider:

```php
namespace App\Providers;

use App\Core\ServiceProvider;

class YourServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('your-service', function($app) {
            return new YourService();
        });
    }
    
    public function boot()
    {
        // Bootstrap code here
    }
}
```

Then register it in `config/app.php`:

```php
'providers' => [
    // ...
    App\Providers\YourServiceProvider::class,
],
```

## Migration Helper

Use the `migration-helper.php` script to guide you through the migration process:

```
php migration-helper.php
```

This will:
1. Set up the required directories
2. Identify duplicate files
3. Backup old files
4. Check and run migrations
5. Help you remove duplicates safely