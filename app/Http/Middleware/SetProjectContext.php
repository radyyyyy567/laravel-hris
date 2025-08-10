<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetProjectContext
{
    public function handle(Request $request, Closure $next)
    {
        // Initialize global project variable if not set
        if (!isset($GLOBALS['project_global'])) {
            // Check URL parameter first
            if ($request->has('project') && $request->get('project') !== '') {
                $GLOBALS['project_global'] = $request->get('project');
                session(['selected_project_id' => $request->get('project')]);
            }
            // Check session
            elseif (session()->has('selected_project_id')) {
                $GLOBALS['project_global'] = session('selected_project_id');
            }
            // Default to empty (All Projects)
            else {
                $GLOBALS['project_global'] = '';
            }
        }

        return $next($request);
    }
}