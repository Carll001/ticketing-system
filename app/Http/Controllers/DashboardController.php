<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Task;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics
     */
    public function index()
    {
        $totalUsers = User::where('role', 'staff')->count();
        $totalDepartments = Department::count();
        $totalTasks = Task::count();

        return Inertia::render('Dashboard', [
            'totalUsers' => $totalUsers,
            'totalDepartments' => $totalDepartments,
            'totalTasks' => $totalTasks,
        ]);
    }
}
