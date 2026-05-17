<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminIp
{
    public function handle(Request $request, Closure $next): Response
    {
        // Get the allowed IPs from the .env file (comma separated)
        $allowedIpsString = env('ALLOWED_ADMIN_IPS', '');
        
        // If the .env variable is empty, bypass the security (Great for local testing)
        if (empty($allowedIpsString)) {
            return $next($request);
        }

        $allowedIps = array_map('trim', explode(',', $allowedIpsString));

        // If the user's IP is not in the whitelist, immediately kill the connection
        if (!in_array($request->ip(), $allowedIps)) {
            abort(403, 'NETWORK SECURITY LOCKOUT: Your IP address is not authorized for HQ/Manager access.');
        }

        return $next($request);
    }
}