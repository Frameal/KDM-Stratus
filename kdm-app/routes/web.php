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
// 1. The Public Informational Website
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        // Tally up total PCs and count only the 'free' ones
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

// 3. Customer Dashboard (With Auto-Image Scanner)
Route::get('/dashboard', function () {
    $floorplans = [];
    $path = public_path('images/floorplans');
    
    // Scans the folder and grabs EVERY image inside it automatically
    if (File::exists($path)) {
        foreach (File::files($path) as $file) {
            $floorplans[] = '/images/floorplans/' . $file->getFilename();
        }
    }

    return Inertia::render('Dashboard', [
        'branches' => Branch::all(),
        'pcs' => Pc::all(),
        'floorplans' => $floorplans // Sent to Vue
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// 4. Profile Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 5. RBAC Admin Dashboards
Route::get('/branch-dashboard', function () {
    // Passes the specific branch data to the manager
    $branch = Branch::find(Auth::user()->branch_id);
    return Inertia::render('Admin/BranchDashboard', ['branch' => $branch]);
})->middleware('auth')->name('branch.dashboard');

Route::get('/hq-dashboard', function () {
    return Inertia::render('Admin/HQDashboard');
})->middleware('auth')->name('hq.dashboard');

// 6. Emergency Escape Hatch
Route::get('/force-logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
});

// Narpim's Separated Route Logic
Route::get('/hq-branches', function () {
    return Inertia::render('Admin/HQBranches'); // You will create this Vue file later!
})->middleware('auth')->name('hq.branches');

Route::get('/hq-users', function () {
    return Inertia::render('Admin/HQUsers'); // You will create this Vue file later!
})->middleware('auth')->name('hq.users');

// Imus Manager Separated Route Logic
Route::get('/branch-terminals', function () {
    return Inertia::render('Admin/BranchTerminals'); // You will create this Vue file later!
})->middleware('auth')->name('branch.terminals');

require __DIR__.'/auth.php';