<?php

use App\Http\Controllers\DashboardController;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// 1. The Public Informational Website
Route::get('/', function () {
    $branches = Branch::all();
    return Inertia::render('Welcome', [
        'branches' => $branches
    ]);
})->name('home');

// 2. Background API Routes for Real-Time Vue Validation
Route::post('/check-username', function (Request $request) {
    $exists = User::where('username', $request->username)->exists();
    return response()->json(['available' => !$exists]);
});

Route::post('/check-email', function (Request $request) {
    $exists = User::where('email', $request->email)->exists();
    return response()->json(['available' => !$exists]);
});

// 3. The Secure User Dashboard (Requires Login & Email Verification)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 4. Admin Dashboard Placeholders
Route::get('/branch-dashboard', function () {
    return "Branch Manager UI Pending - Connected to: " . Auth::guard('admin')->user()->name;
})->name('branch.dashboard');

Route::get('/hq-dashboard', function () {
    return "HQ Executive UI Pending - Connected to: " . Auth::guard('admin')->user()->name;
})->name('hq.dashboard');

// 5. Authentication Routes
require __DIR__.'/auth.php';