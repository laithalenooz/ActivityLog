<?php

namespace LaithAlEnooz\ActivityLog\Contracts;

interface ActivityLogRepositoryInterface
{
	public function log(array $data);
	public function getLogs(array $criteria = []);
}