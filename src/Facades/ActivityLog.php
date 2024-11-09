<?php

namespace LaithAlEnooz\ActivityLog\Facades;

use Illuminate\Support\Facades\Facade;

class ActivityLog extends Facade
{
	protected static function getFacadeAccessor()
	{
		return \LaithAlEnooz\ActivityLog\Logger\ActivityLogger::class;
	}
}