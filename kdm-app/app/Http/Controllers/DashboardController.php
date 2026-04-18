<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Pc;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all branches
        $branches = Branch::all();
        
        // Fetch all PCs
        $pcs = Pc::all();

        // Inertia automatically passes the authenticated user data globally, 
        // so we just pass the branches and PCs here.
        return Inertia::render('Kiosk/Index', [
            'branches' => $branches,
            'pcs' => $pcs
        ]);
    }
}