<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\UserLog;
use App\Services\User\UserTracking;
use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;


class IpBlock
{
    private $userTracking;
    public function __construct(UserTracking $userTracking)
    {
        $this->userTracking = $userTracking;

    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
   {

    // Check if the number of locked users equals the total user count
    if (User::where('locked', '1')->count() == User::count()) {
        return response()->view('error.not_fount'); // You can return a view here or redirect
    }

    // Ensure we have a valid response
    $response = $next($request);


    return $response;
 }






}
