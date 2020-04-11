<?php

namespace DBSystem\Http\Middleware;

use Closure;

class PhpLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (function_exists('ini_get') && function_exists('ini_set')) {
            $_max_execution_time = ini_get('max_execution_time');
            $_memory_limit = ini_get('memory_limit');

            if (settings('max_execution_time') != null && settings('max_execution_time') >= $_max_execution_time) {
                ini_set('max_execution_time', settings('max_execution_time'));
            }

            if (settings('memory_limit') != null && settings('memory_limit') >= intval($_memory_limit)) {
                ini_set('memory_limit', settings('memory_limit') . 'M');
            }
        }

        return $next($request);
    }
}
