<?php

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
];
