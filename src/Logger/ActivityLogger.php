<?php

namespace LaithAlEnooz\ActivityLog\Logger;


use LaithAlEnooz\ActivityLog\Contracts\ActivityLogRepositoryInterface;

class ActivityLogger
{
	protected $repository;
	protected $defaultLogLevel;

	public function __construct(ActivityLogRepositoryInterface $repository, $defaultLogLevel = 'info')
	{
		$this->repository = $repository;
		$this->defaultLogLevel = $defaultLogLevel;
	}

	public function log(
		string $logName,
		string $description,
			   $subject = null,
			   $causer = null,
		array $properties = [],
		string $logLevel = null
	) {
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