<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Admin dashboard
     */
    public function dashboard(): Response
    {
        return Inertia::render('Admin/Dashboard');
    }
}
