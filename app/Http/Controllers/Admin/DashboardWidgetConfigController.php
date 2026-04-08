<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardWidgetConfig;
use App\Models\Role;
use App\Services\DashboardWidgetService;
use Illuminate\Http\Request;

class DashboardWidgetConfigController extends Controller
{
    public function index()
    {
        $roles = Role::whereNotIn('title', ['student','Teacher'])->orderBy('title')->get();
        return view('admin.dashboard-widgets.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        $widgets = DashboardWidgetService::getWidgetConfigForRole($role->id);
        // $widgets = DashboardWidgetConfig::where('role_id',$role->id)->get();
        $allWidgets = DashboardWidgetService::getAllWidgets();
        // return $widgets;
        return view('admin.dashboard-widgets.edit', compact('role', 'widgets', 'allWidgets'));
    }

    public function update(Request $request, Role $role)
    {
        $widgetVisibility = $request->input('widgets', []);
        
        // return $widgetVisibility;

        DashboardWidgetService::saveWidgetConfigForRole($role->id, $widgetVisibility);
        
        return redirect()->back()
            // ->route('admin.dashboard-widgets.index')
            ->with('success', 'Dashboard widgets updated successfully for ' . $role->title);
    }
}