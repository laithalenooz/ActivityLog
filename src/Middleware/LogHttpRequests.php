<?php

namespace LaithAlEnooz\ActivityLog\Middleware;

use Closure;
use Illuminate\Support\Str;
use LaithAlEnooz\ActivityLog\Facades\ActivityLog;

class LogHttpRequests
{
	public function handle($request, Closure $next)
	{
		// Proceed with the request
		$response = $next($request);

		// Check if HTTP request logging is enabled
		if (config('activitylog.log_http_requests', false)) {
			$user = $request->user();
			$logLevel = config('activitylog.http_request_log_level', 'info');

			$properties = [
				'method'    => $request->getMethod(),
				'url'       => $request->fullUrl(),
				'ip'        => $request->ip(),
				'headers'   => $request->headers->all(),
				'request'   => $request->except(['password', 'password_confirmation']),
				'status'    => $response->getStatusCode(),
				'response'  => method_exists($response, 'getContent') ? Str::limit($response->getContent(), 1000) : null,
			];

			ActivityLog::log(
				'http_request',
				'HTTP Request to ' . $request->fullUrl(),
				null,
				$user,
				$properties,
				$logLevel
			);
		}

		return $response;
	}
}