<?php

namespace App\Http\Middleware;

use Closure;

use App\Http\Start\Helpers;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class License
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
    public function handle($request, Closure $next)
    {
        if ($this->helper->check_license() == false && in_array($_SERVER['REMOTE_ADDR'], ['::1', '127.0.0.1']) == false && $request->is('expired') == false){
            return redirect()->route('admin.expired.index');
        }

        if ($this->helper->check_license() == true && $request->is('expired')) {
            throw new NotFoundHttpException;
        }

        return $next($request);
    }
}
