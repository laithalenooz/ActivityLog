<?php

namespace LaithAlEnooz\ActivityLog\Repositories;

use LaithAlEnooz\ActivityLog\Contracts\ActivityLogRepositoryInterface;
use LaithAlEnooz\ActivityLog\Models\MongoActivityLog;

class MongoDBActivityLogRepository implements ActivityLogRepositoryInterface
{
	public function log(array $data)
	{
		return MongoActivityLog::create($data);
	}

	public function getLogs(array $criteria = [])
	{
		return MongoActivityLog::where($criteria)->get();
	}

	public function prune($cutoffDate)
	{
		return MongoActivityLog::where('created_at', '<', $cutoffDate)->delete();
	}
}