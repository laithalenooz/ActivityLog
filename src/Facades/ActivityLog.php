<?php

namespace LaithAlEnooz\ActivityLog\Facades;

use Illuminate\Support\Facades\Facade;
use LaithAlEnooz\ActivityLog\Logger\ActivityLogger;

class ActivityLog extends Facade
{
	protected static function getFacadeAccessor()
	{
		return ActivityLogger::class;
	}
}