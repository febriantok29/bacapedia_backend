<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class PanelAuth
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Session::has('user_id')) {
            return redirect('/login');
        }

        if ($roles && !in_array(Session::get('user_role'), $roles)) {
            abort(403);
        }

        return $next($request);
    }
}
