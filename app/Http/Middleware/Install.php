<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Install
{
    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        if (!file_exists(base_path('.env')) && !$request->is('install*')) {
            \Auth::guard('web')->logout();
            return redirect()->to('install');
        }

        if (file_exists(base_path('.env')) && $request->is('install*') && !$request->is('install/complete')) {
            throw new NotFoundHttpException;
        }

        return $next($request);
    }
}
