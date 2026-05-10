<?php

use App\Http\Controllers\ProfileController;
use App\Models\Branch;
use App\Models\Pc;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

// 1. The Public Informational Website
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'branches' => \App\Models\Branch::withCount([
            'pcs', 
            'pcs as free_pcs' => function ($query) {
                $query->where('status', 'free');
            }
        ])->get()
    ]);
})->name('home');

// 2. Background API Routes
Route::post('/check-username', function (Request $request) {
    return response()->json(['available' => !User::where('username', $request->username)->exists()]);
});

Route::post('/check-email', function (Request $request) {
    return response()->json(['available' => !User::where('email', $request->email)->exists()]);
});

// 3. Customer Dashboard
Route::get('/dashboard', function () {
    $floorplans = [];
    $path = public_path('images/floorplans');
    
    if (File::exists($path)) {
        foreach (File::files($path) as $file) {
            $floorplans[] = '/images/floorplans/' . $file->getFilename();
        }
    }

    return Inertia::render('Dashboard', [
        'branches' => Branch::all(),
        'pcs' => Pc::all(),
        'floorplans' => $floorplans
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// 4. Profile Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --------------------------------------------------------
// PC STATUS UPDATE API (Used by both HQ and Managers)
// --------------------------------------------------------
Route::patch('/pcs/{pc}/status', function (Request $request, App\Models\Pc $pc) {
    $request->validate(['status' => 'required|in:free,occupied,broken,reserved']);
    $pc->update(['status' => $request->status]);
    return back();
})->middleware('auth')->name('pcs.update_status');

// --------------------------------------------------------
// 5. BRANCH MANAGER ROUTES
// --------------------------------------------------------
Route::middleware(['auth'])->prefix('manager')->name('branch.')->group(function () {
    Route::get('/dashboard', function () {
        $branchId = Auth::user()->branch_id;
        if (!$branchId) return redirect('/hq/dashboard'); // FAIL-SAFE FOR HQ ADMINS

        $stats = [
            'total_pcs' => Pc::where('branch_id', $branchId)->count(),
            'free_pcs' => Pc::where('branch_id', $branchId)->where('status', 'free')->count(),
            'broken_pcs' => Pc::where('branch_id', $branchId)->where('status', 'broken')->count(),
            'reports' => \App\Models\Report::where('branch_name', Branch::find($branchId)->name)->count(),
        ];
        return Inertia::render('Admin/BranchDashboard', ['stats' => $stats, 'branch' => Branch::find($branchId)]);
    })->name('dashboard');

    Route::get('/terminals', function () {
        if (!Auth::user()->branch_id) return redirect('/hq/dashboard');
        $pcs = Pc::where('branch_id', Auth::user()->branch_id)->get();
        return Inertia::render('Admin/BranchTerminals', ['pcs' => $pcs]);
    })->name('terminals');

    Route::get('/terminals', function () {
        if (!Auth::user()->branch_id) return redirect('/hq/dashboard');
        $pcs = App\Models\Pc::where('branch_id', Auth::user()->branch_id)->get();
        return Inertia::render('Admin/BranchTerminals', ['pcs' => $pcs]);
    })->name('terminals');

    // NEW: Add a PC
    Route::post('/terminals', function (Request $request) {
        $request->validate(['pc_number' => 'required|string|max:255']);
        
        App\Models\Pc::create([
            'branch_id' => Auth::user()->branch_id,
            'pc_number' => $request->pc_number,
            'status' => 'free'
        ]);
        
        // Also update the total_pcs count in the branches table
        $branch = App\Models\Branch::find(Auth::user()->branch_id);
        $branch->increment('total_pcs');

        return back()->with('success', 'Terminal added successfully.');
    })->name('terminals.store');

    // NEW: Delete a PC
    Route::delete('/terminals/{pc}', function (App\Models\Pc $pc) {
        // Ensure the manager actually owns this PC before deleting
        if ($pc->branch_id === Auth::user()->branch_id) {
            $pc->delete();
            
            $branch = App\Models\Branch::find(Auth::user()->branch_id);
            $branch->decrement('total_pcs');
        }
        return back()->with('success', 'Terminal removed.');
    })->name('terminals.destroy');

Route::get('/reservations', function () {
        if (!Auth::user()->branch_id) return redirect('/hq/dashboard');
        
        // We will pass an empty array for now since we haven't built the Reservation booking engine yet!
        // When we do, this will fetch live reservations where branch_id = Auth::user()->branch_id
        return Inertia::render('Admin/BranchReservations', ['reservations' => []]);
    })->name('reservations');

    Route::get('/feedback', function () {
        if (!Auth::user()->branch_id) return redirect('/hq/dashboard');
        
        // Fetch LIVE reports explicitly for this specific branch
        $branch = App\Models\Branch::find(Auth::user()->branch_id);
        $reports = \App\Models\Report::where('branch_name', $branch->name)->orderBy('created_at', 'desc')->get();
        
        return Inertia::render('Admin/BranchFeedback', ['reports' => $reports]);
    })->name('feedback');
});

// --------------------------------------------------------
// 6. HQ / EXECUTIVE ADMIN ROUTES
// --------------------------------------------------------
Route::middleware(['auth'])->prefix('hq')->name('hq.')->group(function () {
    Route::get('/dashboard', function () {
        $stats = [
            'total_branches' => Branch::count(),
            'total_pcs' => Pc::count(),
            'occupied_pcs' => Pc::where('status', 'occupied')->count(),
            'total_reports' => \App\Models\Report::count(),
        ];
        return Inertia::render('Admin/HQDashboard', ['stats' => $stats]);
    })->name('dashboard');

    Route::get('/branches', function (Request $request) {
        $branches = Branch::all();
        $selectedBranchId = $request->query('branch_id', $branches->first()->id);
        
        $selectedBranch = Branch::find($selectedBranchId);
        $pcs = Pc::where('branch_id', $selectedBranchId)->get();
        $reports = \App\Models\Report::where('branch_name', $selectedBranch->name)->get();

        return Inertia::render('Admin/HQBranches', [
            'branches' => $branches,
            'selectedBranch' => $selectedBranch,
            'pcs' => $pcs,
            'reports' => $reports
        ]);
    })->name('branches');

    // LIVE DATA FOR ENTERPRISE USERS
    Route::get('/users', function () {
        return Inertia::render('Admin/HQUsers', [
            'customers' => User::where('role', 'customer')->get()
        ]); 
    })->name('users');

    // LIVE DATA FOR GLOBAL REPORTS
    Route::get('/reports', function () {
        return Inertia::render('Admin/HQReports', [
            'reports' => \App\Models\Report::orderBy('created_at', 'desc')->get()
        ]); 
    })->name('reports');
});
// --------------------------------------------------------
// UTILITIES & AUTHENTICATION
// --------------------------------------------------------

Route::get('/force-logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
});

Route::get('/auth/google', [\App\Http\Controllers\Auth\OAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\OAuthController::class, 'handleGoogleCallback']);

Route::get('/mfa/setup', [\App\Http\Controllers\Auth\MfaController::class, 'setup'])->name('mfa.setup');
Route::post('/mfa/enable', [\App\Http\Controllers\Auth\MfaController::class, 'enable'])->name('mfa.enable');
Route::post('/mfa/verify', [\App\Http\Controllers\Auth\MfaController::class, 'verifyLogin'])->name('mfa.verify');

Route::post('/reports', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'branch_name' => 'required|string',
        'concern_type' => 'required|string',
        'details' => 'required|string',
    ]);

    \App\Models\Report::create([
        'user_id' => \Illuminate\Support\Facades\Auth::id(),
        'branch_name' => $request->branch_name,
        'concern_type' => $request->concern_type,
        'details' => $request->details,
        'is_anonymous' => $request->is_anonymous,
    ]);

    return back()->with('success', 'Report transmitted successfully.');
})->name('reports.store');

require __DIR__.'/auth.php';