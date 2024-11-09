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
		'log_name', 'description', 'subject', 'causer',
		'properties', 'log_level',
	];

	// filters
	public function scopeLogName($query, $logName)
	{
		return $query->where('log_name', $logName);
	}

	public function scopeLogLevel($query, $logLevel)
	{
		return $query->where('log_level', $logLevel);
	}

	public function scopeDate($query, $date)
	{
		return $query->whereDate('created_at', $date);
	}
}