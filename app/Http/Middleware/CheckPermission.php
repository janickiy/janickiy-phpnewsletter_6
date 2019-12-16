<?php

namespace App\Http\Middleware;

use Closure;

use App\Http\Start\Helpers;

class CheckPermission
{
    protected $helper;
    /**
     * Creates a new instance of the middleware.
     *
     */
    public function __construct(Helpers $helper)
    {
        $this->helper = $helper;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        if($this->helper->has_permission(\Auth::user('web')->role, $permissions)){
            return $next($request);
        }else{
            abort(403);
        }
    }
}