<?php

namespace LaithAlEnooz\ActivityLog\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MongoActivityLog extends Model
{
	protected $collection = 'activity_logs';
	protected $connection = 'mongodb';
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
		return $this->morphTo(null, null, 'causer_id', 'causer_type');
	}

	/**
	 * Get the subject of the activity.
	 */
	public function subject(): MorphTo
	{
		return $this->morphTo(null, null, 'subject_id', 'subject_type');
	}
}