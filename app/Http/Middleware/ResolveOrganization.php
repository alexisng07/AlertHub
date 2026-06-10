<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Organization;

class ResolveOrganization
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Missing bearer token.'], 401);
        }

        $organization = Organization::query()
            ->where('api_token', $token)
            ->first();

        if (!$organization) {
            return response()->json(['message' => 'Invalid API token.'], 401);
        }

        $request->attributes->set('organization', $organization);

        return $next($request);
    }
}
