<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Closure;

class CheckPageAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $routeName = Route::currentRouteName();

        // If route has no name, we cannot check it against adminpages usually
        if (!$routeName) {
            return $next($request);
        }

        // Cache admin pages lookup for 1 hour to avoid repeated queries
        $cacheKey = 'adminpage_route_' . $routeName;
        $page = cache()->remember($cacheKey, 3600, function () use ($routeName) {
            return DB::table('adminpages')->where('route', $routeName)->first();
        });

        // If the route is not in adminpages, we assume it's not subject to this specific sidebar permission logic
        if (!$page) {
            return $next($request);
        }

        $user = Auth::guard('web')->user();
        if (!$user) {
            // Should be handled by 'auth' middleware usually, but if not:
            return redirect()->route('login.index');
        }

        // Cache team member lookup in session for performance
        $sessionKey = 'user_' . $user->id . '_permissions';

        // Get cached permissions or fetch fresh
        $permissions = session()->get($sessionKey);

        if ($permissions === null) {
            // Check if user is a team member (restricted access)
            $teamMember = DB::table('teammember')->where('userId', $user->id)->first();

            if ($teamMember) {
                // Fetch all accessible page IDs for this role
                $accessiblePageIds = DB::table('rolepages')
                    ->where('teamRoleId', $teamMember->teamRoleId)
                    ->pluck('adminPageId')
                    ->toArray();

                $permissions = [
                    'is_team_member' => true,
                    'accessible_pages' => $accessiblePageIds
                ];
            } else {
                // Not a team member (Super Admin)
                $permissions = [
                    'is_team_member' => false,
                    'accessible_pages' => []
                ];
            }

            // Cache in session
            session()->put($sessionKey, $permissions);
        }

        // Check access based on cached permissions
        if ($permissions['is_team_member']) {
            if (!in_array($page->id, $permissions['accessible_pages'])) {
                // If the user does NOT have access, redirect to no-permission
                return redirect()->route('no.permission');
            }
        }
        // If NOT a team member (e.g. Super Admin), they have full access

        return $next($request);
    }
}
