<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Welcome page
     */
    public function welcome(Request $request) {
        if ($request->user()->isCode()) {
            return redirect()->route('vote');
        }

        if ($request->user()->hasAdminRole()) {
            return redirect()->route('admin_dashboard');
        }

        return redirect()->route('badge');
    }
}
