<?php

namespace LaithAlEnooz\ActivityLog\Logger;


use LaithAlEnooz\ActivityLog\Contracts\ActivityLogRepositoryInterface;

class ActivityLogger
{
	protected $repository;
	protected $defaultLogLevel;
	protected $connection;

	public function __construct(ActivityLogRepositoryInterface $repository, $defaultLogLevel = 'info')
	{
		$this->repository = $repository;
		$this->defaultLogLevel = $defaultLogLevel;
		$this->connection = config('activitylog.connection');
	}

	public function log(
		string $logName,
		string $description,
			   $subject = null,
			   $causer = null,
		array $properties = [],
		string $logLevel = null
	) {
		if ($this->connection === 'mongodb')
		{
			$data = [
				'log_name'      => $logName,
				'description'   => $description,
				'subject'    	=> $subject?->toArray() ?? null,
				'causer'     	=> $causer?->toArray() ?? null,
				'properties'    => $properties,
				'log_level'     => $logLevel ?? $this->defaultLogLevel,
			];
		} else {
			$data = [
				'log_name'      => $logName,
				'description'   => $description,
				'subject_id'    => optional($subject)->getKey(),
				'subject_type'  => $subject ? get_class($subject) : null,
				'causer_id'     => optional($causer)->getKey(),
				'causer_type'   => $causer ? get_class($causer) : null,
				'properties'    => $properties,
				'log_level'     => $logLevel ?? $this->defaultLogLevel,
			];
		}

		if (in_array($logName, ['updated', 'deleted']))
		{
			$data['properties']['original'] = collect($subject->getOriginal())->except(['created_at', 'updated_at', 'deleted_at'])->toArray();
			$data['properties']['dirty'] = collect($subject->getDirty())->except(['created_at', 'updated_at', 'deleted_at'])->toArray();

			if (empty($data['properties']['dirty']) && empty($data['properties']['original'])) {
				return null;
			}

			$data['properties']['original'] = collect($data['properties']['dirty'])->mapWithKeys(function ($value, $key) use ($data) {
				return [$key => $data['properties']['original'][$key] ?? null];
			})->toArray();
		}

		return $this->repository->log($data);
	}

	// Convenience methods for log levels
	public function info(string $logName, string $description, $subject = null, $causer = null, array $properties = [])
	{
		return $this->log($logName, $description, $subject, $causer, $properties, 'info');
	}

	public function warning(string $logName, string $description, $subject = null, $causer = null, array $properties = [])
	{
		return $this->log($logName, $description, $subject, $causer, $properties, 'warning');
	}

	public function error(string $logName, string $description, $subject = null, $causer = null, array $properties = [])
	{
		return $this->log($logName, $description, $subject, $causer, $properties, 'error');
	}
}