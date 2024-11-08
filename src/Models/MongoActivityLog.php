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
	 * Get the causer attribute manually from MySQL.
	 */
	public function getCauserAttribute()
	{
		// Check if we have both causer_type and causer_id
		if (!empty($this->causer_type) && !empty($this->causer_id)) {
			// Use the causer_type to resolve the model class
			$modelClass = $this->causer_type;

			// Make sure the model class exists
			if (class_exists($modelClass)) {
				return $modelClass::find($this->causer_id);
			}
		}

		return null; // Return null if no causer is found
	}

	/**
	 * Get the subject attribute manually from MySQL.
	 */
	public function getSubjectAttribute()
	{
		// Check if we have both subject_type and subject_id
		if (!empty($this->subject_type) && !empty($this->subject_id)) {
			// Use the subject_type to resolve the model class
			$modelClass = $this->subject_type;

			// Make sure the model class exists
			if (class_exists($modelClass)) {
				return $modelClass::find($this->subject_id);
			}
		}

		return null; // Return null if no subject is found
	}
}