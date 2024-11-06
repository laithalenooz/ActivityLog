<?php

namespace LaithAlEnooz\ActivityLog\Repositories;

use LaithAlEnooz\ActivityLog\Contracts\ActivityLogRepositoryInterface;
use LaithAlEnooz\ActivityLog\Models\ActivityLog;

class MySQLActivityLogRepository implements ActivityLogRepositoryInterface
{
	public function log(array $data)
	{
		return ActivityLog::create($data);
	}

	public function getLogs(array $criteria = [])
	{
		return ActivityLog::where($criteria)->get();
	}

	public function prune($cutoffDate)
	{
		return ActivityLog::where('created_at', '<', $cutoffDate)->delete();
	}
}