<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();

        // Fetch permissions for the user's role (assuming roleId is stored in the user's table)
        $permissions = DB::table('role_has_permissions')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->join('user_roles', 'role_has_permissions.role_id', '=', 'user_roles.roleId')
            ->where('user_roles.userId', $user->id)
            ->pluck('permissions.name'); // Get all permission names for the user's role

        // Check if the user has the required permission
        if (!$permissions->contains($permission)) {
            return redirect()->route('no.permission'); // You can redirect to a custom page
        }

        // If permission exists, proceed with the request
        return $next($request);
    }
}
