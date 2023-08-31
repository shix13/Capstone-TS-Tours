<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Auth\EmployeeController; // Import the EmployeeController
use Illuminate\Support\Facades\Auth;
class ManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle($request, Closure $next)
    {
        $user = Auth::user(); // Get the authenticated user
        
        if ($user instanceof EmployeeController && $user->accountType === 'Manager') {
            return $next($request);
        }

        return response()->view('404_manager', [], Response::HTTP_NOT_FOUND);
    }
}
