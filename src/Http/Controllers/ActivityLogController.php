<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LaithAlEnooz\ActivityLog\Models\MongoActivityLog;

class ActivityLogController extends Controller
{
	public function index(Request $request)
	{
		// Start a new query on the MongoActivityLog model
		$query = MongoActivityLog::query();

		// Apply filters based on request inputs
		if ($request->filled('log_name')) {
			$query->whereIn('log_name', $request->input('log_name'));
		}

		if ($request->filled('causer_name')) {
			$causerName = $request->input('causer_name');
			$query->where('causer.name', 'like', '%' . $causerName . '%');
		}

		if ($request->filled('date_from')) {
			$query->where('created_at', '>=', $request->input('date_from'));
		}

		if ($request->filled('date_to')) {
			$query->where('created_at', '<=', $request->input('date_to'));
		}

		// Order by latest
		$query->orderBy('created_at', 'desc');

		// Paginate the results (15 per page)
		$logs = $query->paginate(100);

		// Fetch unique log names for the filter dropdown
		$logNames = MongoActivityLog::groupBy('log_name')->pluck('log_name');

		return view('activity_logs.index', compact('logs', 'logNames'));
	}
}
