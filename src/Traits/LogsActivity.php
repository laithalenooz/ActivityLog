<?php

namespace LaithAlEnooz\ActivityLog\Traits;

use Illuminate\Support\Facades\Auth;
use LaithAlEnooz\ActivityLog\Facades\ActivityLog;

trait LogsActivity
{
	public static function bootLogsActivity()
	{
		foreach (static::getModelEventsToLog() as $event) {
			static::$event(function ($model) use ($event) {
				$description = ucfirst($event) . ' ' . class_basename($model);
				$causer = Auth::user();

				ActivityLog::log(
					$event,
					$description,
					$model,
					$causer,
					['attributes' => $model->getAttributes()],
					static::getLogLevelForEvent($event)
				);
			});
		}
	}

	protected static function getModelEventsToLog()
	{
		return ['created', 'updated', 'deleted'];
	}

	protected static function getLogLevelForEvent($event)
	{
		$levels = [
			'created' => 'info',
			'updated' => 'info',
			'deleted' => 'warning',
		];

		return isset($levels[$event]) ? $levels[$event] : 'info';
	}
}
