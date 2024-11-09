<?php

namespace LaithAlEnooz\ActivityLog\Traits;

use Illuminate\Support\Facades\Auth;
use LaithAlEnooz\ActivityLog\Facades\ActivityLog;

trait LogsActivity
{
	public static function bootLogsActivity(): void
	{
		foreach (static::getModelEventsToLog() as $event) {
			static::$event(function ($model) use ($event) {
				if ($model instanceof \LaithAlEnooz\ActivityLog\Models\ActivityLog) {
					return;
				}
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

	protected static function getModelEventsToLog(): array
	{
		return ['created', 'updated', 'deleted'];
	}

	protected static function getLogLevelForEvent($event): string
	{
		$levels = [
			'created' => 'info',
			'updated' => 'info',
			'deleted' => 'warning',
		];

		return $levels[$event] ?? 'info';
	}
}
