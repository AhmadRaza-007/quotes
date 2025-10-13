<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ApiKey;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key') ?: $request->query('api_key');

        if (!$apiKey) {
            return response()->json([
                'error' => 'API key required',
                'message' => 'Please provide a valid API key in the X-API-Key header or api_key query parameter'
            ], 401);
        }

        $keyRecord = ApiKey::where('key', $apiKey)->active()->first();

        if (!$keyRecord) {
            return response()->json([
                'error' => 'Invalid API key',
                'message' => 'The provided API key is invalid or expired'
            ], 401);
        }

        // Update last used timestamp
        $keyRecord->update(['last_used_at' => now()]);

        // Attach the user to the request for easy access
        $request->merge(['api_key_user' => $keyRecord->user]);

        return $next($request);
    }
}
