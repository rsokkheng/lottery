<?php

namespace App\View\Components;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Sidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $authUser  = auth()->user();
        $supervisorRoles = ['admin', 'master', 'agent'];

        $memberQuery = User::whereDoesntHave('roles', fn($q) => $q->whereIn('name', $supervisorRoles));
        if ($authUser && !$authUser->hasRole('admin')) {
            if ($authUser->hasRole('master')) {
                $memberQuery->where('master_id', $authUser->id);
            } elseif ($authUser->hasRole('agent')) {
                $memberQuery->where('manager_id', $authUser->id);
            }
        }
        $userCount = $memberQuery->count();
        view()->share('userCount', $userCount);
        
        $RoleCount = Role::count();
        view()->share('RoleCount',$RoleCount);
        
        $PermissionCount = Permission::count();
        view()->share('PermissionCount',$PermissionCount);
        
      
        view()->share('ProductCount');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar');
    }
}
