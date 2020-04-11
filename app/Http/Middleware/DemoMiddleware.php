<?php

namespace DBSystem\Http\Middleware;

use Closure;

class DemoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('app.env') === 'demo' &&
            (!$request->isMethod('get') ||
                $request->is('update') ||
                $request->is('tasks/run/*') ||
                $request->is('databases/*/connect') ||
                $request->is('*/download'))) {

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Action not allowed in demo mode'
                ], 403);
            } else {
                return response('Action not allowed in demo mode', 403);
            }
        }

        return $next($request);
    }
}
