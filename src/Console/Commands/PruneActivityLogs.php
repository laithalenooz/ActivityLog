<?php

namespace LaithAlEnooz\ActivityLog\Console\Commands;

use Illuminate\Console\Command;
use LaithAlEnooz\ActivityLog\Contracts\ActivityLogRepositoryInterface;
use Carbon\Carbon;

class PruneActivityLogs extends Command
{
	protected $signature = 'activitylog:prune {--days= : The number of days to retain activity logs}';

	protected $description = 'Prune activity logs older than the specified number of days';

	protected $repository;

	public function __construct(ActivityLogRepositoryInterface $repository)
	{
		parent::__construct();
		$this->repository = $repository;
	}

	public function handle()
	{
		$days = $this->option('days') ?? config('activitylog.data_retention_days');

		if ($days === null) {
			$this->info('Data retention is disabled. No logs were pruned.');
			return;
		}

		$cutoffDate = Carbon::now()->subDays($days);

		$deleted = $this->repository->prune($cutoffDate);

		$this->info("Pruned {$deleted} activity log(s) older than {$days} day(s).");
	}
}
