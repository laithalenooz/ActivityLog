<?php

namespace LaithAlEnooz\ActivityLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
	protected $connection = 'mysql';

	protected $table = 'activity_logs';

	protected $fillable = [
		'log_name', 'description', 'subject_id', 'subject_type',
		'causer_id', 'causer_type', 'properties', 'log_level',
	];

	protected $casts = [
		'properties' => 'array',
	];

	/**
	 * Get the causer of the activity.
	 */
	public function causer(): MorphTo
	{
		return $this->morphTo();
	}

	/**
	 * Get the subject of the activity.
	 */
	public function subject(): MorphTo
	{
		return $this->morphTo();
	}
}