<?php

namespace LaithAlEnooz\ActivityLog;

use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\ServiceProvider;
use LaithAlEnooz\ActivityLog\Contracts\ActivityLogRepositoryInterface;
use LaithAlEnooz\ActivityLog\Repositories\MySQLActivityLogRepository;
use LaithAlEnooz\ActivityLog\Repositories\MongoDBActivityLogRepository;

class ActivityLogServiceProvider extends ServiceProvider
{
	public function boot()
	{
		// Publish configuration
		$this->publishes(
			[
				__DIR__ . '/../config/activitylog.php' => config_path('activitylog.php'),
			], 'config');

		// Load migrations
		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

		// Publish middleware
		$this->publishes(
			[
				__DIR__ . '/Middleware/LogHttpRequests.php' => app_path('Http/Middleware/LogHttpRequests.php'),
			], 'middleware');

		// Publish resources
		$this->publishes(
			[
				__DIR__ . '/../resources' => base_path('resources'),
			], 'resources');

		// Publish controller
		$this->publishes(
			[
				__DIR__ . '/Http/Controllers/ActivityLogController.php' => app_path('Http/Controllers/ActivityLogController.php'),
			], 'controller');

		// Register the route macro for the activity log
		\Route::macro('activityLog', function () {
			\Route::prefix(config('activitylog.log_viewer.prefix'))
				->middleware(config('activitylog.log_viewer.middleware'))
				->group(function () {
				\Route::get('/', [config('activitylog.log_viewer.controller'), 'index'])
					->name('activity_logs.index');
			});
		});
	}

	public function register()
	{
		// Merge configuration
		$this->mergeConfigFrom(
			__DIR__ . '/../config/activitylog.php',
			'activitylog'
		);

		// Bind the repository interface to the implementation
		$this->app->bind(
			ActivityLogRepositoryInterface::class, function ($app) {
			$connection = config('activitylog.connection');
			return $connection === 'mongodb'
				? new MongoDBActivityLogRepository()
				: new MySQLActivityLogRepository();
		});

		// Register the ActivityLogger
		$this->app->singleton(
			\LaithAlEnooz\ActivityLog\Logger\ActivityLogger::class, function ($app) {
			return new \LaithAlEnooz\ActivityLog\Logger\ActivityLogger(
				$app->make(\LaithAlEnooz\ActivityLog\Contracts\ActivityLogRepositoryInterface::class),
				config('activitylog.default_log_level', 'info')
			);
		});

		// Register the command
		if ($this->app->runningInConsole()) {
			$this->commands(
				[
					\LaithAlEnooz\ActivityLog\Console\Commands\PruneActivityLogs::class,
				]);
		}
	}
}
