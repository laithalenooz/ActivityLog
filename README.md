# ActivityLog

A flexible and customizable activity log package for Laravel 11, supporting multiple databases like MySQL and MongoDB. It provides middleware for logging HTTP requests, customizable log levels, Eloquent relationships to retrieve the `causer` and `subject` of activities, and a data retention feature to manage log storage.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running Migrations](#running-migrations)
- [Usage](#usage)
    - [Logging Activities](#logging-activities)
    - [Using Custom Log Levels](#using-custom-log-levels)
    - [Logging HTTP Requests](#logging-http-requests)
    - [Automatic Model Event Logging](#automatic-model-event-logging)
    - [Retrieving Logs](#retrieving-logs)
- [Data Retention](#data-retention)
    - [Configuring Data Retention](#configuring-data-retention)
    - [Pruning Old Logs](#pruning-old-logs)
    - [Scheduling Automatic Pruning](#scheduling-automatic-pruning)
- [Advanced Usage](#advanced-usage)
    - [Customizing the Activity Logger](#customizing-the-activity-logger)
    - [Extending the Package](#extending-the-package)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

---

## Features

- **Database Flexibility**: Supports both MySQL and MongoDB.
- **Middleware for HTTP Request Logging**: Logs HTTP requests and responses.
- **Customizable Log Levels**: Supports `info`, `warning`, `error`, and custom log levels.
- **Eloquent Relationships**: Retrieve `causer` and `subject` models via Eloquent relationships.
- **Automatic Model Event Logging**: Automatically logs model `created`, `updated`, and `deleted` events.
- **Data Retention**: Configurable data retention period with automatic pruning of old logs.
- **Configurable**: Offers extensive configuration options.
- **Extensible**: Easily extendable to fit custom needs.

---

## Requirements

- PHP >= 8.2
- Laravel 11
- For MongoDB support: [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) package

---

## Installation

You can install the package via Composer:

```bash
composer require laithalenooz/activity-log
```

---

## Configuration

After installing the package, publish the configuration file and middleware:

```bash
php artisan vendor:publish --provider="LaithAlEnooz\ActivityLog\ActivityLogServiceProvider" --tag="config"

php artisan vendor:publish --provider="LaithAlEnooz\ActivityLog\ActivityLogServiceProvider" --tag="middleware"

php artisan vendor:publish --provider="LaithAlEnooz\ActivityLog\ActivityLogServiceProvider" --tag="resources"

php artisan vendor:publish --provider="LaithAlEnooz\ActivityLog\ActivityLogServiceProvider" --tag="controller"
```

This will create an `activitylog.php` file in your `config` directory and copy the `LogHttpRequests` middleware to your `app/Http/Middleware` directory.

### Configuration Options

#### `config/activitylog.php`

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection
    |--------------------------------------------------------------------------
    |
    | The database connection that will be used to store the activity logs.
    | You may set this to any of the connections defined in your database
    | configuration file.
    |
    */

    'connection' => env('ACTIVITY_LOG_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Default Log Level
    |--------------------------------------------------------------------------
    |
    | The default log level to use when logging activities. You can override
    | this level when logging specific activities.
    |
    */

    'default_log_level' => 'info',

    /*
    |--------------------------------------------------------------------------
    | Log HTTP Requests
    |--------------------------------------------------------------------------
    |
    | Determines whether HTTP requests should be automatically logged using
    | the provided middleware. Set to true to enable request logging.
    |
    */

    'log_http_requests' => false,

    /*
    |--------------------------------------------------------------------------
    | HTTP Request Log Level
    |--------------------------------------------------------------------------
    |
    | The log level for HTTP request logs.
    |
    */

    'http_request_log_level' => 'info',

	/*
	|--------------------------------------------------------------------------
	| Data Retention Period
	|--------------------------------------------------------------------------
	|
	| This value determines how long (in days) the activity logs should be
	| retained before they are deleted. Set it to null if you don't want
	| to delete old logs automatically.
	|
	*/

	'data_retention_days' => env('ACTIVITY_LOG_RETENTION_DAYS', 365),

	/*
	|--------------------------------------------------------------------------
	| Log Viewer
	|--------------------------------------------------------------------------
	|
	| The log viewer configuration allows you to customize the behavior of the
	| activity log viewer. You can specify the route prefix, the middleware
	| to use, and the controller to handle the activity log requests.
	|
	*/

	'log_viewer' => [

		'prefix' => 'activity-logs',

		'middleware' => ['web', 'auth'],

		'controller' => App\Http\Controllers\ActivityLogController::class,

		'view_enabled' => true,

	],
];
```

### Environment Variables

Set the following in your `.env` file if you need to customize the database connection and data retention period:

```env
ACTIVITY_LOG_CONNECTION=mysql
ACTIVITY_LOG_RETENTION_DAYS=365
```

---

## Running Migrations

Run the migrations to create the necessary tables in your database:

```bash
php artisan migrate
```

This will create an `activity_logs` table or collection in the specified database connection.

---

## Usage

### Logging Activities

To log an activity, use the `ActivityLog` facade:

```php
use LaithAlEnooz\ActivityLog\Facades\ActivityLog;

// Basic log
ActivityLog::log('user_login', 'User logged in', $user, $user);

// Log with properties
ActivityLog::log('user_profile_update', 'User updated profile', $user, $user, [
    'changed_fields' => ['name', 'email'],
]);

// Log without subject or causer
ActivityLog::log('system_maintenance', 'System maintenance scheduled');
```

- **Parameters:**
    - `logName` (string): A name for the log entry.
    - `description` (string): A description of the activity.
    - `subject` (Model|null): The subject model of the activity.
    - `causer` (Model|null): The causer (usually the user) of the activity.
    - `properties` (array): Additional data to store with the log.
    - `logLevel` (string|null): The log level (overrides default if provided).

### Using Custom Log Levels

You can specify custom log levels when logging activities:

```php
// Using the 'warning' log level
ActivityLog::log('low_disk_space', 'Disk space is running low', null, null, [], 'warning');

// Using convenience methods
ActivityLog::info('cache_cleared', 'Application cache cleared');
ActivityLog::warning('high_memory_usage', 'Memory usage is above threshold');
ActivityLog::error('system_failure', 'Critical system failure occurred');
```

### Logging HTTP Requests

To enable HTTP request logging:

1. **Register the Middleware**

   Add the `LogHttpRequests` middleware to your `app/Http/Kernel.php`:

   ```php
   protected $middlewareGroups = [
       'web' => [
           // Other middleware...
           \App\Http\Middleware\LogHttpRequests::class,
       ],

       'api' => [
           // Other middleware...
           \App\Http\Middleware\LogHttpRequests::class,
       ],
   ];
   ```

2. **Enable Request Logging in Configuration**

   Set `log_http_requests` to `true` in `config/activitylog.php`:

   ```php
   'log_http_requests' => true,
   ```

   Optionally, set the log level for HTTP requests:

   ```php
   'http_request_log_level' => 'info',
   ```

**Note:** The middleware captures the request and response, excluding sensitive data like passwords.

### Automatic Model Event Logging

You can automatically log model events (`created`, `updated`, `deleted`) by using the `LogsActivity` trait in your models.

**1. Use the Trait in Your Model**

```php
use Illuminate\Database\Eloquent\Model;
use LaithAlEnooz\ActivityLog\Traits\LogsActivity;

class Post extends Model
{
    use LogsActivity;

    // Your model code...
}
```

**2. Customize Events and Log Levels (Optional)**

By default, the trait logs `created`, `updated`, and `deleted` events with default log levels. To customize, override the methods in your model:

```php
class Post extends Model
{
    use LogsActivity;

    protected static function getModelEventsToLog()
    {
        return ['created', 'deleted']; // Only log created and deleted events
    }

    protected static function getLogLevelForEvent($event)
    {
        $levels = [
            'created' => 'info',
            'deleted' => 'warning',
        ];

        return $levels[$event] ?? 'info';
    }
}
```

### Retrieving Logs

You can retrieve logs using the `ActivityLog` model:

```php
use LaithAlEnooz\ActivityLog\Models\ActivityLog;

$logs = ActivityLog::where('log_level', 'error')->get();

foreach ($logs as $log) {
    echo $log->description;

    // Retrieve the causer and subject
    $causer = $log->causer;   // Eloquent model or null
    $subject = $log->subject; // Eloquent model or null
}
```

You can also use Eloquent relationships to get activities related to a specific model:

```php
// In your User model
use Illuminate\Database\Eloquent\Relations\MorphMany;

class User extends Authenticatable
{
    // ...

    public function activities(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'causer');
    }
}

// Usage
$user = User::find(1);
$activities = $user->activities;
```

---

## Data Retention

### Configuring Data Retention

You can configure how long activity logs should be retained before being pruned. This is set in the `config/activitylog.php` configuration file:

```php
'data_retention_days' => env('ACTIVITY_LOG_RETENTION_DAYS', 365),
```

- **Default:** 365 days.
- **Disable Automatic Pruning:** Set `'data_retention_days' => null`.

### Pruning Old Logs

The package provides an Artisan command to prune old activity logs:

```bash
php artisan activitylog:prune
```

**Options:**

- `--days=`: Specify the number of days to retain logs. Overrides the configuration.

**Example:**

```bash
php artisan activitylog:prune --days=30
```

This command will delete all activity logs older than the specified number of days.

### Scheduling Automatic Pruning

To automate the pruning process, schedule the command in your application's console kernel:

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // Prune activity logs daily
    $schedule->command('activitylog:prune')->daily();
}
```

This will run the pruning command every day at midnight, deleting logs older than the retention period specified in your configuration.

---

## Advanced Usage

### Customizing the Activity Logger

You can customize the `ActivityLogger` by extending it or modifying the configuration.

**Extending ActivityLogger:**

```php
namespace App\Services;

use LaithAlEnooz\ActivityLog\Logger\ActivityLogger as BaseActivityLogger;

class ActivityLogger extends BaseActivityLogger
{
    public function customMethod()
    {
        // Your custom logic
    }
}
```

**Binding Custom Logger in Service Container:**

```php
// In a service provider
$this->app->singleton(\LaithAlEnooz\ActivityLog\Logger\ActivityLogger::class, function ($app) {
    return new \App\Services\ActivityLogger(
        $app->make(\LaithAlEnooz\ActivityLog\Contracts\ActivityLogRepositoryInterface::class),
        config('activitylog.default_log_level', 'info')
    );
});
```

### Extending the Package

You can extend the package by implementing custom repositories, adding new middleware, or creating new traits.

**Implementing a Custom Repository:**

1. **Create a New Repository Class**

   ```php
   namespace App\Repositories;

   use LaithAlEnooz\ActivityLog\Contracts\ActivityLogRepositoryInterface;

   class CustomActivityLogRepository implements ActivityLogRepositoryInterface
   {
       public function log(array $data)
       {
           // Your custom implementation
       }

       public function getLogs(array $criteria = [])
       {
           // Your custom implementation
       }

       public function prune($cutoffDate)
       {
           // Your custom implementation
       }
   }
   ```

2. **Bind the Custom Repository**

   ```php
   // In a service provider
   $this->app->bind(
       \LaithAlEnooz\ActivityLog\Contracts\ActivityLogRepositoryInterface::class,
       \App\Repositories\CustomActivityLogRepository::class
   );
   ```

---

## Testing

To run tests, set up PHPUnit in your Laravel application and write test cases in the `tests` directory of your application.

**Example Test Case:**

```php
namespace Tests\Feature;

use Tests\TestCase;
use LaithAlEnooz\ActivityLog\Facades\ActivityLog;
use LaithAlEnooz\ActivityLog\Models\ActivityLog as ActivityLogModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_is_logged()
    {
        ActivityLog::log('test_log', 'This is a test log');

        $this->assertDatabaseHas('activity_logs', [
            'log_name' => 'test_log',
            'description' => 'This is a test log',
        ]);
    }

    public function test_prune_command_deletes_old_logs()
    {
        // Create logs with different timestamps
        ActivityLogModel::factory()->create(['created_at' => now()->subDays(10)]);
        ActivityLogModel::factory()->create(['created_at' => now()->subDays(5)]);
        ActivityLogModel::factory()->create(['created_at' => now()]);

        // Run the prune command with a retention period of 7 days
        $this->artisan('activitylog:prune', ['--days' => 7]);

        // Assert that logs older than 7 days are deleted
        $this->assertDatabaseCount('activity_logs', 2);
    }
}
```

---

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bug fix:

   ```bash
   git checkout -b feature/your-feature-name
   ```

3. Make your changes and commit them:

   ```bash
   git commit -m "Add your message here"
   ```

4. Push to your forked repository:

   ```bash
   git push origin feature/your-feature-name
   ```

5. Create a pull request against the `main` branch.

**Please ensure all tests pass and adhere to the PSR coding standards.**

---

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---

**Maintained by Laith Al Enooz**

If you encounter any issues or have suggestions for improvements, please open an issue on [GitHub](https://github.com/LaithAlEnooz/ActivityLog/issues).

Happy logging!