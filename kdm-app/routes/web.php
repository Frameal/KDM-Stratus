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

// Make sure pc is clear and temp accounts are purged
function autoClearExpiredReservations() {
    $expiredSessions = \App\Models\Reservation::where('status', 'active')
        ->where('expires_at', '<=', now())
        ->get();
        
    foreach ($expiredSessions as $session) {
        $session->update(['status' => 'expired']);
        \App\Models\Pc::where('id', $session->pc_id)->update(['status' => 'free']);
    }

    // Auto-Ban Temporary Walk-In Accounts after 20 minutes
    \App\Models\User::where('email', 'LIKE', '%@temp.kdm.local')
        ->where('created_at', '<=', now()->subMinutes(20))
        ->where('is_banned', false)
        ->update([
            'is_banned' => true, 
            'password' => \Illuminate\Support\Facades\Hash::make('LOCKED_ACCOUNT')
        ]);
}


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
    autoClearExpiredReservations();

    // Fetch live global pricing
    $pricing = [
        '15m' => \App\Models\Setting::where('key', 'price_15m')->value('value') ?? '5.00',
        '30m' => \App\Models\Setting::where('key', 'price_30m')->value('value') ?? '10.00',
        '60m' => \App\Models\Setting::where('key', 'price_60m')->value('value') ?? '25.00',
    ];

    $floorplans = [];
    $path = public_path('images/floorplans');
    if (File::exists($path)) {
        foreach (File::files($path) as $file) {
            $floorplans[] = '/images/floorplans/' . $file->getFilename();
        }
    }

    $activeReservation = \App\Models\Reservation::where('user_id', Auth::id())
        ->where('status', 'active')
        ->where('expires_at', '>', now())
        ->with('pc')
        ->first();

    return Inertia::render('Dashboard', [
        'branches' => Branch::all(),
        'pcs' => Pc::all(),
        'floorplans' => $floorplans,
        'activeSession' => $activeReservation, 
        'pricing' => $pricing
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// 4. Profile Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --------------------------------------------------------
// PC STATUS UPDATE API
// --------------------------------------------------------
Route::patch('/pcs/{pc}/status', function (Request $request, App\Models\Pc $pc) {
    $request->validate(['status' => 'required|in:free,occupied,broken,reserved']);
    $oldStatus = $pc->status;
    $pc->update(['status' => $request->status]);

    $branchId = Auth::user()->role === 'branch_manager' ? Auth::user()->branch_id : null;
    \App\Models\AuditLog::create([
        'user_id' => Auth::id(), 'branch_id' => $branchId, 'action' => 'Hardware Status Override',
        'details' => "Changed terminal {$pc->pc_number} status from {$oldStatus} to {$request->status}."
    ]);

    broadcast(new \App\Events\PcStatusUpdated($pc));

    return back();
})->middleware(['auth', 'admin.ip'])->name('pcs.update_status');

// --------------------------------------------------------
// 5. BRANCH MANAGER ROUTES (Secured with admin.ip)
// --------------------------------------------------------
Route::middleware(['auth', 'admin.ip'])->prefix('manager')->name('branch.')->group(function () {
    
    // 1. DASHBOARD & REVENUE
    Route::get('/dashboard', function () {
        autoClearExpiredReservations();
        $branchId = Auth::user()->branch_id;
        if (!$branchId) return redirect('/hq/dashboard');

        $branch = App\Models\Branch::find($branchId);
        $activeReservations = \App\Models\Reservation::where('branch_id', $branchId)->where('status', 'active')->count();

        $stats = [
            'total_pcs' => Pc::where('branch_id', $branchId)->count(),
            'free_pcs' => Pc::where('branch_id', $branchId)->where('status', 'free')->count(),
            'broken_pcs' => Pc::where('branch_id', $branchId)->where('status', 'broken')->count(),
            'active_reservations' => $activeReservations,
            'reports' => \App\Models\Report::where('branch_name', $branch->name)->where('status', '!=', 'resolved')->count(),
        ];

        $shiftRevenue = \App\Models\Reservation::with('user', 'pc')->where('branch_id', $branchId)->whereDate('created_at', \Carbon\Carbon::today())->orderBy('created_at', 'desc')->get();
        $totalRevenue = $shiftRevenue->sum('fee_paid');

        $userIds = \App\Models\Reservation::where('branch_id', $branchId)->pluck('user_id');
        $todayTopUps = \App\Models\Transaction::whereDate('created_at', \Carbon\Carbon::today())
            ->whereIn('user_id', $userIds)
            ->sum('amount');
            
        $todayTopUpData = \App\Models\Transaction::with('user')
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->whereIn('user_id', $userIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/BranchDashboard', [
            'stats' => $stats, 'branch' => $branch, 'shiftRevenue' => $shiftRevenue,
            'totalRevenue' => $totalRevenue, 'todayTopUps' => $todayTopUps,
            'todayTopUpData' => $todayTopUpData
        ]);
    })->name('dashboard');

    // 2. HARDWARE MANAGEMENT
    Route::get('/terminals', function () {
        autoClearExpiredReservations(); 
        if (!Auth::user()->branch_id) return redirect('/hq/dashboard');
        
        $branch = App\Models\Branch::find(Auth::user()->branch_id);
        $pcs = Pc::where('branch_id', Auth::user()->branch_id)->get();
        
        $activeReservations = \App\Models\Reservation::with('user')
            ->where('branch_id', Auth::user()->branch_id)
            ->where('status', 'active')
            ->get();

        $floorplans = [];
        $path = public_path('images/floorplans');
        if (\Illuminate\Support\Facades\File::exists($path)) {
            foreach (\Illuminate\Support\Facades\File::files($path) as $file) {
                $floorplans[] = '/images/floorplans/' . $file->getFilename();
            }
        }

        return Inertia::render('Admin/BranchTerminals', [
            'branch' => $branch, 'pcs' => $pcs, 'reservations' => $activeReservations, 'floorplans' => $floorplans
        ]);
    })->name('terminals');

    Route::post('/terminals', function (Request $request) {
        $request->validate(['pc_number' => 'required|string|max:255']);
        App\Models\Pc::create([
            'branch_id' => Auth::user()->branch_id,
            'pc_number' => $request->pc_number,
            'status' => 'free'
        ]);
        $branch = App\Models\Branch::find(Auth::user()->branch_id);
        $branch->increment('total_pcs');

        \App\Models\AuditLog::create(['user_id' => Auth::id(), 'branch_id' => Auth::user()->branch_id, 'action' => 'Hardware Registration', 'details' => "Registered terminal: {$request->pc_number}"]);

        return back()->with('success', 'Terminal added successfully.');
    })->name('terminals.store');

    Route::delete('/terminals/{pc}', function (App\Models\Pc $pc) {
        if ($pc->branch_id === Auth::user()->branch_id) {
            $pcNumber = $pc->pc_number;
            $pc->delete();
            $branch = App\Models\Branch::find(Auth::user()->branch_id);
            $branch->decrement('total_pcs');

            \App\Models\AuditLog::create(['user_id' => Auth::id(), 'branch_id' => Auth::user()->branch_id, 'action' => 'Hardware Deletion', 'details' => "Permanently deleted terminal: {$pcNumber}"]);
        }
        return back()->with('success', 'Terminal permanently deleted.');
    })->name('terminals.destroy');

    // 3. LIVE RESERVATIONS & CANCELLATIONS
    Route::get('/reservations', function () {
        autoClearExpiredReservations();
        if (!Auth::user()->branch_id) return redirect('/hq/dashboard');
        
        $reservations = \App\Models\Reservation::with(['user', 'pc'])
            ->where('branch_id', Auth::user()->branch_id)
            ->whereIn('status', ['active', 'expired']) 
            ->orderBy('expires_at', 'asc')
            ->get();

        return Inertia::render('Admin/BranchReservations', ['reservations' => $reservations]);
    })->name('reservations');

    Route::post('/reservations/cancel', function (Request $request) {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'refund' => 'required|boolean'
        ]);

        $reservation = \App\Models\Reservation::findOrFail($request->reservation_id);
        
        if ($reservation->branch_id !== Auth::user()->branch_id) abort(403);

        $pc = \App\Models\Pc::find($reservation->pc_id);
        $pc->update(['status' => 'free']);
        
        $status = 'cancelled';
        $user = \App\Models\User::find($reservation->user_id);
        
        if ($request->refund) {
            $user->increment('balance', $reservation->fee_paid);
            $status = 'refunded';
        }

        $reservation->update(['status' => $status]);

        \App\Models\AuditLog::create([
            'user_id' => Auth::id(), 'branch_id' => Auth::user()->branch_id,
            'action' => $request->refund ? 'Cancel & Refund' : 'Cancel Reservation',
            'details' => "Terminated session for @{$user->username} on {$pc->pc_number}."
        ]);

        return back()->with('success', 'Session terminated' . ($request->refund ? ' and refunded.' : '.'));
    })->name('reservations.cancel');

    // 4. RESERVATION HISTORY
    Route::get('/history', function () {
        if (!Auth::user()->branch_id) return redirect('/hq/dashboard');
        
        $history = \App\Models\Reservation::with(['user', 'pc'])
            ->where('branch_id', Auth::user()->branch_id)
            ->where('status', '!=', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/BranchHistory', ['history' => $history]);
    })->name('history');

    // 5. LOCAL FEEDBACK MANAGEMENT
    Route::get('/feedback', function () {
        if (!Auth::user()->branch_id) return redirect('/hq/dashboard');
        $branch = App\Models\Branch::find(Auth::user()->branch_id);
        
        $reports = \App\Models\Report::with('user')->where('branch_name', $branch->name)->orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/BranchFeedback', ['reports' => $reports]);
    })->name('feedback');

    Route::patch('/feedback/{report}/status', function (Request $request, \App\Models\Report $report) {
        $request->validate(['status' => 'required|in:pending,investigating,resolved']);
        $oldStatus = $report->status ?? 'pending';
        $report->update(['status' => $request->status]);

        \App\Models\AuditLog::create(['user_id' => Auth::id(), 'branch_id' => Auth::user()->branch_id, 'action' => 'Ticket Status Override', 'details' => "Updated feedback ticket from {$oldStatus} to {$request->status}."]);

        return back()->with('success', 'Ticket status updated.');
    })->name('feedback.update');

    // 6. LOCAL CUSTOMER MANAGEMENT
    Route::get('/users', function () {
        if (!Auth::user()->branch_id) return redirect('/hq/dashboard');
        
        $customers = User::where('role', 'customer')->orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/BranchUsers', ['customers' => $customers]);
    })->name('users');

    Route::post('/users/{user}/balance', function (Request $request, User $user) {
        $request->validate(['amount' => 'required|numeric', 'type' => 'required|in:add,minus']);
        
        if ($request->type === 'minus') {
            if ($user->balance < $request->amount) return back()->withErrors(['balance' => 'Cannot deduct more than available balance.']);
            $user->decrement('balance', $request->amount);
        } else {
            $user->increment('balance', $request->amount);
            \App\Models\Transaction::create([
                'user_id' => $user->id,
                'reference_id' => 'CASH-' . Auth::user()->branch_id . '-' . time(),
                'amount' => $request->amount,
                'status' => 'paid'
            ]);
        }
        
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(), 'branch_id' => Auth::user()->branch_id, 'action' => 'Ledger Override',
            'details' => ($request->type === 'add' ? 'Injected ₱' : 'Deducted ₱') . number_format($request->amount, 2) . " physically on @{$user->username}"
        ]);
        return back()->with('success', 'Account ledger synchronized.');
    })->name('users.balance');

    Route::patch('/users/{user}/ban', function (User $user) {
        $user->is_banned = !$user->is_banned;
        $user->save();
        
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(), 'branch_id' => Auth::user()->branch_id, 'action' => 'Network Ban Override',
            'details' => ($user->is_banned ? 'Banned @' : 'Lifted ban for @') . $user->username
        ]);
        return back()->with('success', 'Account restriction status toggled.');
    })->name('users.ban');

    // 7. Branch Manager Transaction History
    Route::get('/transactions', function () {
        if (!Auth::user()->branch_id) return redirect('/hq/dashboard');
        
        $reservations = \App\Models\Reservation::with(['user', 'pc'])
            ->where('branch_id', Auth::user()->branch_id)
            ->where('status', '!=', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $userIds = \App\Models\Reservation::where('branch_id', Auth::user()->branch_id)->pluck('user_id');
        $topups = \App\Models\Transaction::with('user')->whereIn('user_id', $userIds)->orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/BranchTransactions', [
            'reservations' => $reservations,
            'topups' => $topups
        ]);
    })->name('transactions');

    // 8. Branch Manager Logs
    Route::get('/logs', function () {
        if (!Auth::user()->branch_id) return redirect('/hq/dashboard');
        $logs = \App\Models\AuditLog::with('user')->where('branch_id', Auth::user()->branch_id)->orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/BranchLogs', ['logs' => $logs]);
    })->name('logs');

    Route::post('/log-export', function (Request $request) {
        \App\Models\AuditLog::create(['user_id' => Auth::id(), 'branch_id' => Auth::user()->branch_id, 'action' => 'Data Export', 'details' => $request->details]);
        return response()->json(['success' => true]);
    })->name('log.export');

    // GENERATE TEMPORARY WALK-IN ACCOUNT
    Route::post('/users/temporary', function () {
        $pin = rand(10000, 99999); 
        $username = 'walkin_' . rand(1000, 9999);
        
        $user = new \App\Models\User();
        $user->forceFill([
            'username' => $username,
            'email' => $username . '@temp.kdm.local',
            'password' => \Illuminate\Support\Facades\Hash::make((string)$pin),
            'role' => 'customer',
            'first_name' => 'Walk-in',
            'last_name' => 'Customer',
            'contact_number' => 'N/A',
            'email_verified_at' => now(), 
            'balance' => 5.00 
        ])->saveQuietly();

        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'reference_id' => 'CASH-' . Auth::user()->branch_id . '-TEMP-' . time(),
            'amount' => 5.00,
            'status' => 'paid'
        ]);

        \App\Models\AuditLog::create([
            'user_id' => Auth::id(), 'branch_id' => Auth::user()->branch_id, 
            'action' => 'Walk-In Creation',
            'details' => "Generated temporary account {$username} and collected ₱5.00."
        ]);

        return response()->json([
            'username' => $username,
            'password' => $pin
        ]);
    })->name('users.temporary');

});

// --------------------------------------------------------
// 6. HQ / EXECUTIVE ADMIN ROUTES (Secured with admin.ip)
// --------------------------------------------------------
Route::middleware(['auth', 'admin.ip'])->prefix('hq')->name('hq.')->group(function () {
    
    // 1. GLOBAL DASHBOARD
    Route::get('/dashboard', function () {
        autoClearExpiredReservations();

        $stats = [
            'total_branches' => Branch::count(),
            'total_pcs' => Pc::count(),
            'occupied_pcs' => Pc::whereIn('status', ['occupied', 'reserved'])->count(),
            'total_reports' => \App\Models\Report::where('status', '!=', 'resolved')->count(),
        ];
        
        $todayRevenue = \App\Models\Reservation::whereDate('created_at', \Carbon\Carbon::today())
            ->where('status', '!=', 'refunded')
            ->sum('fee_paid');
            
        $todayTopUps = \App\Models\Transaction::whereDate('created_at', \Carbon\Carbon::today())
            ->where('status', 'paid')
            ->sum('amount');

        return Inertia::render('Admin/HQDashboard', [
            'stats' => $stats,
            'todayRevenue' => $todayRevenue,
            'todayTopUps' => $todayTopUps,
            'branches' => Branch::all()
        ]);
    })->name('dashboard');

    // 2. BRANCH OPERATIONS
    Route::get('/branches', function (Request $request) {
        autoClearExpiredReservations();
        
        $branches = Branch::all();
        $selectedBranchId = $request->query('branch_id', $branches->first()->id ?? null);
        
        if (!$selectedBranchId) return Inertia::render('Admin/HQBranches', ['branches' => []]);

        $selectedBranch = Branch::find($selectedBranchId);
        $manager = User::where('role', 'branch_manager')->where('branch_id', $selectedBranchId)->first();
        $pcs = Pc::where('branch_id', $selectedBranchId)->get();
        $reports = \App\Models\Report::with('user')->where('branch_name', $selectedBranch->name)->orderBy('created_at', 'desc')->get();
        
        $activeReservations = \App\Models\Reservation::with('user')
            ->where('branch_id', $selectedBranchId)
            ->where('status', 'active')
            ->get();
            
        $floorplans = [];
        $path = public_path('images/floorplans');
        if (\Illuminate\Support\Facades\File::exists($path)) {
            foreach (\Illuminate\Support\Facades\File::files($path) as $file) {
                $floorplans[] = '/images/floorplans/' . $file->getFilename();
            }
        }

        return Inertia::render('Admin/HQBranches', [
            'branches' => $branches,
            'selectedBranch' => $selectedBranch,
            'manager' => $manager,
            'pcs' => $pcs,
            'reports' => $reports,
            'reservations' => $activeReservations,
            'floorplans' => $floorplans
        ]);
    })->name('branches');

    Route::post('/branches', function (Request $request) {
        $request->validate([
            'name' => 'required|string|unique:branches,name',
            'address' => 'required|string',
            'initial_pcs' => 'required|integer|min:1',
            'manager_username' => 'required|string|unique:users,username',
            'manager_email' => 'required|email|unique:users,email',
            'manager_password' => 'required|string|min:8',
            'schema_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $branch = Branch::create([
            'name' => $request->name,
            'address' => $request->address,
            'total_pcs' => $request->initial_pcs
        ]);

        if ($request->hasFile('schema_image')) {
            $file = $request->file('schema_image');
            $filename = strtolower(str_replace(' ', '_', $request->name)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/floorplans'), $filename);
        }

        \App\Models\User::create([
            'username' => $request->manager_username,
            'email' => $request->manager_email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->manager_password),
            'role' => 'branch_manager',
            'branch_id' => $branch->id,
            'first_name' => 'Branch',
            'last_name' => 'Manager',
            'contact_number' => 'N/A'
        ]);

        for ($i = 1; $i <= $request->initial_pcs; $i++) {
            Pc::create([
                'branch_id' => $branch->id,
                'pc_number' => 'PC-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 'free'
            ]);
        }

        \App\Models\AuditLog::create(['user_id' => Auth::id(), 'branch_id' => null, 'action' => 'Node Deployment', 'details' => "Initialized new branch: {$request->name}"]);
        return back()->with('success', 'Branch, Schematics, and Manager Account deployed successfully.');
    })->name('branches.store');

    Route::post('/branches/{branch}/edit', function (Request $request, Branch $branch) {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'schema_image' => 'nullable|image|max:5120',
            'manager_username' => 'nullable|string',
            'manager_password' => 'nullable|string|min:8',
            'add_pcs' => 'nullable|integer|min:0'
        ]);
        
        if ($request->name !== $branch->name) \App\Models\Report::where('branch_name', $branch->name)->update(['branch_name' => $request->name]);
        $branch->update(['name' => $request->name, 'address' => $request->address]);

        if ($request->hasFile('schema_image')) {
            $file = $request->file('schema_image');
            $filename = strtolower(str_replace(' ', '_', $request->name)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/floorplans'), $filename);
        }

        $manager = User::where('role', 'branch_manager')->where('branch_id', $branch->id)->first();
        if ($manager && $request->manager_username) {
            $manager->username = $request->manager_username;
            if ($request->manager_password) $manager->password = \Illuminate\Support\Facades\Hash::make($request->manager_password);
            $manager->save();
        }

        if ($request->add_pcs > 0) {
            $currentCount = $branch->total_pcs;
            for ($i = 1; $i <= $request->add_pcs; $i++) {
                Pc::create(['branch_id' => $branch->id, 'pc_number' => 'PC-' . str_pad($currentCount + $i, 2, '0', STR_PAD_LEFT), 'status' => 'free']);
            }
            $branch->increment('total_pcs', $request->add_pcs);
        }

        \App\Models\AuditLog::create(['user_id' => Auth::id(), 'branch_id' => null, 'action' => 'Metadata Update', 'details' => "Updated settings for branch: {$branch->name}"]);
        return back()->with('success', 'Branch ecosystem fully synchronized.');
    })->name('branches.full_update');

    Route::delete('/branches/{branch}', function (Branch $branch) {
        $branchName = $branch->name;
        Pc::where('branch_id', $branch->id)->delete();
        User::where('role', 'branch_manager')->where('branch_id', $branch->id)->delete();
        $branch->delete();
        
        \App\Models\AuditLog::create(['user_id' => Auth::id(), 'branch_id' => null, 'action' => 'Node Deletion', 'details' => "Permanently purged branch: {$branchName}"]);
        return redirect()->route('hq.branches')->with('success', 'Branch and all localized hardware purged.');
    })->name('branches.destroy');

    // 3. ENTERPRISE USER MANAGEMENT
    Route::get('/users', function () {
        $customers = User::where('role', 'customer')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                $lastReservation = \App\Models\Reservation::with('branch')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                $user->last_branch = $lastReservation ? $lastReservation->branch->name : 'No History';
                return $user;
            });

        return Inertia::render('Admin/HQUsers', ['customers' => $customers]); 
    })->name('users');
    
    Route::post('/users/{user}/balance', function (Request $request, User $user) {
        $request->validate(['amount' => 'required|numeric', 'type' => 'required|in:add,minus']);
        
        if ($request->type === 'minus') {
            if ($user->balance < $request->amount) return back()->withErrors(['balance' => 'Cannot deduct more than available balance.']);
            $user->decrement('balance', $request->amount);
        } else {
            $user->increment('balance', $request->amount);
        }

        \App\Models\AuditLog::create([
            'user_id' => Auth::id(), 'branch_id' => null, 'action' => 'Ledger Override',
            'details' => ($request->type === 'add' ? 'Injected ₱' : 'Seized ₱') . number_format($request->amount, 2) . " on @{$user->username}"
        ]);
        return back()->with('success', 'Account ledger synchronized.');
    })->name('users.balance');

    Route::patch('/users/{user}/ban', function (User $user) {
        $user->is_banned = !$user->is_banned;
        $user->save();
        
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(), 'branch_id' => null, 'action' => 'Network Ban Override',
            'details' => ($user->is_banned ? 'Banned @' : 'Lifted ban for @') . $user->username
        ]);
        return back()->with('success', 'Account restriction status toggled.');
    })->name('users.ban');

    // 4. GLOBAL REPORTS
    Route::get('/reports', function (Request $request) {
        $query = \App\Models\Report::with('user');

        if ($request->filled('branch')) $query->where('branch_name', $request->branch);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type')) $query->where('concern_type', $request->type);
        if ($request->filled('search')) $query->where('details', 'LIKE', '%' . $request->search . '%');

        return Inertia::render('Admin/HQReports', [
            'reports' => $query->orderBy('created_at', 'desc')->get(),
            'branches' => Branch::all()
        ]); 
    })->name('reports');

    // 5. GLOBAL PRICING CONTROLS
    Route::get('/pricing', function () {
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        return Inertia::render('Admin/HQPricing', ['settings' => $settings]);
    })->name('pricing');

    Route::post('/pricing', function (Request $request) {
        $request->validate([
            'price_15m' => 'required|numeric|min:1',
            'price_30m' => 'required|numeric|min:1',
            'price_60m' => 'required|numeric|min:1',
        ]);

        \App\Models\Setting::where('key', 'price_15m')->update(['value' => $request->price_15m]);
        \App\Models\Setting::where('key', 'price_30m')->update(['value' => $request->price_30m]);
        \App\Models\Setting::where('key', 'price_60m')->update(['value' => $request->price_60m]);

        \App\Models\AuditLog::create(['user_id' => Auth::id(), 'branch_id' => null, 'action' => 'Pricing Matrix Sync', 'details' => "Updated global rates to {$request->price_15m} / {$request->price_30m} / {$request->price_60m}"]);
        return back()->with('success', 'Global pricing variables synchronized across network.');
    })->name('pricing.update');

    // 6. GCS BACKUPS (Simulated)
    Route::get('/backups', function () {
        $logs = [
            ['id' => 1, 'type' => 'Automated Backup', 'status' => 'Success', 'size' => '42 MB', 'date' => now()->subDays(1)->format('M d, Y H:i')],
            ['id' => 2, 'type' => 'Automated Backup', 'status' => 'Success', 'size' => '41 MB', 'date' => now()->subDays(2)->format('M d, Y H:i')],
            ['id' => 3, 'type' => 'Manual Snapshot', 'status' => 'Success', 'size' => '40 MB', 'date' => now()->subDays(3)->format('M d, Y H:i')],
        ];
        return Inertia::render('Admin/HQBackups', ['logs' => $logs]);
    })->name('backups');

    Route::post('/backups/trigger', function () {
        sleep(2); 
        \App\Models\AuditLog::create(['user_id' => Auth::id(), 'branch_id' => null, 'action' => 'GCS Backup Trigger', 'details' => "Forced manual database dump to Google Cloud Storage"]);
        return back()->with('success', 'Manual SQL Snapshot secured to Google Cloud Storage.');
    })->name('backups.trigger');

    // HQ Transaction History
    Route::get('/transactions', function (Request $request) {
        $branchFilter = $request->query('branch', null);
        
        $reservationsQuery = \App\Models\Reservation::with(['user', 'pc', 'branch'])->where('status', '!=', 'active')->orderBy('created_at', 'desc');
        if ($branchFilter) $reservationsQuery->where('branch_id', $branchFilter);

        $topupsQuery = \App\Models\Transaction::with('user')->orderBy('created_at', 'desc');
        if ($branchFilter) {
            $userIds = \App\Models\Reservation::where('branch_id', $branchFilter)->pluck('user_id');
            $topupsQuery->whereIn('user_id', $userIds);
        }

        return Inertia::render('Admin/HQTransactions', [
            'reservations' => $reservationsQuery->get(),
            'topups' => $topupsQuery->get(),
            'branches' => Branch::all()
        ]);
    })->name('transactions');

    // 8. GLOBAL AUDIT LOGS
    Route::get('/logs', function (Request $request) {
        $branchFilter = $request->query('branch', null);
        $logsQuery = \App\Models\AuditLog::with(['user', 'branch'])->orderBy('created_at', 'desc');
        if ($branchFilter) $logsQuery->where('branch_id', $branchFilter);
        
        return Inertia::render('Admin/HQLogs', [
            'logs' => $logsQuery->get(), 'branches' => Branch::all()
        ]);
    })->name('logs');

    Route::post('/log-export', function (Request $request) {
        \App\Models\AuditLog::create(['user_id' => Auth::id(), 'branch_id' => null, 'action' => 'Data Export', 'details' => $request->details]);
        return response()->json(['success' => true]);
    })->name('log.export');

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

    $todayCount = \App\Models\Report::where('user_id', \Illuminate\Support\Facades\Auth::id())
        ->whereDate('created_at', \Carbon\Carbon::today())
        ->count();

    if ($todayCount >= 3) {
        return back()->withErrors(['report' => 'Anti-Spam: You have reached the maximum limit of 3 reports per day.']);
    }

    \App\Models\Report::create([
        'user_id' => \Illuminate\Support\Facades\Auth::id(),
        'branch_name' => $request->branch_name,
        'concern_type' => $request->concern_type,
        'details' => $request->details,
        'is_anonymous' => $request->is_anonymous ?? false,
    ]);

    return back()->with('success', 'Report transmitted successfully.');
})->name('reports.store');

Route::get('/register/recovery', function () {
    if (!session('recovery_code_plain')) return redirect('/dashboard');
    return Inertia::render('Auth/RecoveryCode', [
        'recoveryCode' => session('recovery_code_plain')
    ]);
})->middleware('auth')->name('register.recovery');

Route::post('/recover-via-code', function (Request $request) {
    $request->validate([
        'identifier' => 'required|string',
        'recovery_code' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::where('username', $request->identifier)
        ->orWhere('email', $request->identifier)->first();

    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->recovery_code, $user->recovery_code)) {
        return back()->withErrors(['recovery_code' => 'Invalid account identifier or recovery code.']);
    }

    $user->update([
        'password' => \Illuminate\Support\Facades\Hash::make($request->password)
    ]);

    return redirect('/login')->with('status', 'Account recovered successfully. You may now log in with your new password.');
})->name('password.recover.code');

// --------------------------------------------------------
// PAYMONGO CHECKOUT GENERATOR (UPGRADED SESSIONS API)
// --------------------------------------------------------
Route::post('/topup/generate', function (Illuminate\Http\Request $request) {
    try {
        $request->validate(['amount' => 'required|integer|min:1']);

        $response = Illuminate\Support\Facades\Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => [
                            [
                                'name' => 'KDM Stratus Access Time',
                                'amount' => (int) ($request->amount * 100),
                                'currency' => 'PHP',
                                'quantity' => 1
                            ]
                        ],
                        'payment_method_types' => ['qrph', 'gcash', 'paymaya'],
                        'success_url' => route('dashboard'), 
                        'cancel_url' => route('dashboard'),
                        'description' => 'Terminal Top-Up',
                    ]
                ]
            ]);

        $data = $response->json();

        if ($response->failed()) {
            return response()->json(['error' => 'PayMongo API Error', 'details' => $data], 500);
        }

        if (isset($data['data']['id'])) {
            \App\Models\Transaction::create([
                'user_id' => Auth::id(),
                'reference_id' => $data['data']['id'],
                'amount' => $request->amount,
                'status' => 'pending'
            ]);

            return response()->json(['checkout_url' => $data['data']['attributes']['checkout_url']]);
        }

        return response()->json(['error' => 'Unknown PayMongo response', 'details' => $data], 500);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Laravel PHP Error', 'message' => $e->getMessage()], 500);
    }
})->middleware('auth')->name('topup.generate');

// --------------------------------------------------------
// CUSTOMER RESERVATION SYSTEM
// --------------------------------------------------------
Route::post('/reserve', function (Illuminate\Http\Request $request) {
    $request->validate([
        'pc_id' => 'required|exists:pcs,id',
        'duration' => 'required|in:15,30,60',
    ]);

    $costs = [
        15 => (float) (\App\Models\Setting::where('key', 'price_15m')->value('value') ?? 5.00),
        30 => (float) (\App\Models\Setting::where('key', 'price_30m')->value('value') ?? 10.00),
        60 => (float) (\App\Models\Setting::where('key', 'price_60m')->value('value') ?? 25.00),
    ];
    $cost = $costs[$request->duration];

    $user = Auth::user();
    $pc = \App\Models\Pc::findOrFail($request->pc_id);

    if ($user->balance < $cost) return back()->withErrors(['reservation' => 'Insufficient funds.']);
    if ($pc->status !== 'free') return back()->withErrors(['reservation' => 'This PC is no longer available.']);

    $pc->update(['status' => 'reserved']);
    $user->decrement('balance', $cost);

    \App\Models\Reservation::create([
        'user_id' => $user->id,
        'pc_id' => $pc->id,
        'branch_id' => $pc->branch_id,
        'fee_paid' => $cost,
        'duration_minutes' => $request->duration,
        'expires_at' => now()->addMinutes($request->duration),
        'status' => 'active'
    ]);

    return back()->with('success', 'Hardware secured successfully.');
})->middleware('auth')->name('reserve.store');

Route::post('/reserve/cancel', function (Illuminate\Http\Request $request) {
    $request->validate(['reservation_id' => 'required|exists:reservations,id']);

    $reservation = \App\Models\Reservation::where('id', $request->reservation_id)
        ->where('user_id', Auth::id())
        ->where('status', 'active')
        ->firstOrFail();

    \App\Models\Pc::where('id', $reservation->pc_id)->update(['status' => 'free']);
    $reservation->update(['status' => 'cancelled']);

    return back()->with('success', 'Reservation manually cancelled.');
})->middleware('auth')->name('reserve.cancel');


Route::post('/reserve/expire', function (Illuminate\Http\Request $request) {
    $request->validate(['reservation_id' => 'required|exists:reservations,id']);
    
    $reservation = \App\Models\Reservation::find($request->reservation_id);

    if ($reservation && $reservation->status === 'active' && $reservation->expires_at <= now()->addSeconds(60)) {
        $reservation->update(['status' => 'expired']);
        \App\Models\Pc::where('id', $reservation->pc_id)->update(['status' => 'free']);
    }
    
    return response()->json(['status' => 'success']);
})->middleware('auth')->name('reserve.expire');



// --------------------------------------------------------
// HARDWARE TIMER API (For the Python Client Scripts)
// --------------------------------------------------------
Route::get('/api/terminal-check/{pc_number}', function ($pc_number) {
    $pc = \App\Models\Pc::where('pc_number', $pc_number)->first();
    
    if (!$pc) return response()->json(['action' => 'lock', 'reason' => 'PC Not Found']);

    if ($pc->status === 'reserved' || $pc->status === 'occupied') {
        $reservation = \App\Models\Reservation::where('pc_id', $pc->id)
            ->where('status', 'active')
            ->first();

        if ($reservation && $reservation->expires_at > now()) {
            return response()->json([
                'action' => 'unlock',
                'expires_at' => $reservation->expires_at,
                'user' => $reservation->user->username
            ]);
        }

        if ($reservation && $reservation->expires_at <= now()) {
            $reservation->update(['status' => 'expired']);
            $pc->update(['status' => 'free']);
        }
    }

    return response()->json(['action' => 'lock', 'reason' => 'No active time']);
});

// --------------------------------------------------------
// HARDWARE CLIENT API (For Python Desktop App)
// --------------------------------------------------------

Route::post('/api/terminal/login', function (Illuminate\Http\Request $request) {
    $request->validate([
        'pc_number' => 'required|string',
        'username' => 'required|string',
        'password' => 'required|string'
    ]);

    $user = \App\Models\User::where('username', $request->username)->first();
    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return response()->json(['success' => false, 'message' => 'Invalid credentials.']);
    }

    $pc = \App\Models\Pc::where('pc_number', $request->pc_number)->first();
    if (!$pc || $pc->status !== 'free') {
        return response()->json(['success' => false, 'message' => 'Terminal unavailable.']);
    }

    $hourlyRate = (float) (\App\Models\Setting::where('key', 'price_60m')->value('value') ?? 25.00);
    $ratePerMinute = $hourlyRate / 60;

    if ($user->balance < $ratePerMinute) {
        return response()->json(['success' => false, 'message' => 'Insufficient wallet balance.']);
    }

    $maxMinutes = floor($user->balance / $ratePerMinute);
    $pc->update(['status' => 'occupied']);

    $reservation = \App\Models\Reservation::create([
        'user_id' => $user->id,
        'pc_id' => $pc->id,
        'branch_id' => $pc->branch_id,
        'fee_paid' => 0, 
        'duration_minutes' => $maxMinutes,
        'expires_at' => now()->addMinutes($maxMinutes),
        'status' => 'active'
    ]);

    return response()->json([
        'success' => true, 
        'expires_at' => $reservation->expires_at,
        'user' => $user->username
    ]);
});

Route::get('/api/terminal/sync/{pc_number}', function ($pc_number) {
    $pc = \App\Models\Pc::where('pc_number', $pc_number)->first();
    if (!$pc) return response()->json(['action' => 'lock']);

    if ($pc->status === 'occupied' || $pc->status === 'reserved') {
        $reservation = \App\Models\Reservation::where('pc_id', $pc->id)->where('status', 'active')->first();
        
        if ($reservation && $reservation->expires_at > now()) {
            return response()->json(['action' => 'unlock', 'expires_at' => $reservation->expires_at]);
        }

        if ($reservation && $reservation->expires_at <= now()) {
            $user = \App\Models\User::find($reservation->user_id);
            $user->update(['balance' => 0]); 
            
            $reservation->update(['status' => 'completed', 'fee_paid' => ($reservation->duration_minutes * (($hourlyRate ?? 25)/60))]);
            $pc->update(['status' => 'free']);
        }
    }
    return response()->json(['action' => 'lock']);
});

Route::post('/api/terminal/logout', function (Illuminate\Http\Request $request) {
    $pc = \App\Models\Pc::where('pc_number', $request->pc_number)->first();
    if ($pc) {
        $reservation = \App\Models\Reservation::where('pc_id', $pc->id)->where('status', 'active')->first();
        if ($reservation) {
            $minutesUsed = now()->diffInMinutes($reservation->created_at);
            if ($minutesUsed < 1) $minutesUsed = 1; 

            $hourlyRate = (float) (\App\Models\Setting::where('key', 'price_60m')->value('value') ?? 25.00);
            $ratePerMinute = $hourlyRate / 60;
            $cost = $minutesUsed * $ratePerMinute;

            $user = \App\Models\User::find($reservation->user_id);
            if ($user) {
                $user->decrement('balance', $cost);
            }

            $reservation->update(['status' => 'completed', 'fee_paid' => $cost, 'duration_minutes' => $minutesUsed]);
        }
        $pc->update(['status' => 'free']);
    }
    return response()->json(['success' => true]);
});

// --------------------------------------------------------
// PAYMONGO WEBHOOK RECEIVER (Background Balance Credit)
// --------------------------------------------------------
Route::post('/api/paymongo/webhook', function (Illuminate\Http\Request $request) {
    // SAFETY BUFFER: Pause execution for exactly 1.5 seconds.
    // This gives the server plenty of time to finish writing the pending transaction row.
    usleep(1500000); 

    $payload = $request->json()->all();
    
    if (!isset($payload['data']['attributes']['type'])) {
        return response()->json(['status' => 'missing type'], 200);
    }

    $eventType = $payload['data']['attributes']['type'];
    $checkoutSessionId = null;

    if ($eventType === 'checkout_session.payment.paid') {
        $checkoutSessionId = $payload['data']['attributes']['data']['id'] ?? null;
    } elseif ($eventType === 'payment.paid') {
        $checkoutSessionId = $payload['data']['attributes']['data']['object']['external_reference'] ?? null;
    }

    if ($checkoutSessionId) {
        // Query the database for the pending transaction
        $transaction = \App\Models\Transaction::where('reference_id', $checkoutSessionId)
            ->where('status', 'pending')
            ->first();

        if ($transaction) {
            // 1. Permanently update the transaction row status to paid
            $transaction->update(['status' => 'paid']);

            // 2. Hydrate the user's wallet balance
            $user = \App\Models\User::find($transaction->user_id);
            if ($user) {
                $user->increment('balance', $transaction->amount);
                
                // Establish log entry details for audit trails
                \App\Models\AuditLog::create([
                    'user_id' => $user->id,
                    'branch_id' => null,
                    'action' => 'PayMongo Top-Up Success',
                    'details' => "Credited ₱" . number_format($transaction->amount, 2) . " via digital gateway."
                ]);
                
                // Update diagnostic tracker
                $debugInfo = ['parsed_session_id' => $checkoutSessionId, 'transaction_found' => true, 'status' => 'success'];
                file_put_contents(storage_path('logs/paymongo_diagnostic.json'), json_encode($debugInfo));
                
                return response()->json(['status' => 'success'], 200);
            }
        }
    }

    $debugInfo = ['parsed_session_id' => $checkoutSessionId, 'transaction_found' => false, 'status' => 'ignored_or_failed'];
    file_put_contents(storage_path('logs/paymongo_diagnostic.json'), json_encode($debugInfo));
    return response()->json(['status' => 'ignored'], 200);
});


require __DIR__.'/auth.php';