<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Task;
use App\Models\Transaction;
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
            'totalUsers'       => User::whereNot('role', 'superadmin')->count(),
            'staffCount'       => User::where('role', 'staff')->count(),
            'adminCount'       => User::where('role', 'admin')->count(),
            'totalDepartments' => $totalDepartments,
            'totalTasks' => $totalTasks,
            'transactions' => Transaction::latest()->take(10)->get(),
        ]);
    }
}
