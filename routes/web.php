<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\PaymentProviderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TopingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionDetailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\userInterfaceController;
use App\Models\PaymentProvider;
use App\Models\Table;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Rute Welcome
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Rute Beranda (Dashboard)
Route::get('/beranda', function () {
    $availableTables = Table::where('status', 'available')->count();
    $occupiedTables = Table::where('status', 'occupied')->count();
    $totalTables = Table::count();
    $totalUsers = User::count();

    $dailyTransactions = Transaction::whereDate('created_at', Carbon::today())->count();
    $weeklyTransactions = Transaction::whereBetween('created_at', [
        Carbon::now()->startOfWeek(),
        Carbon::now()->endOfWeek()
    ])->count();
    $monthlyTransactions = Transaction::whereMonth('created_at', Carbon::now()->month)->count();

    return view('beranda', compact(
        'availableTables',
        'occupiedTables',
        'totalTables',
        'totalUsers',
        'dailyTransactions',
        'weeklyTransactions',
        'monthlyTransactions'
    ));
})->middleware(['auth', 'verified'])->name('beranda');

// Rute Autentikasi
Auth::routes(['verify' => true]);
require __DIR__ . '/auth.php';

// Rute yang Memerlukan Autentikasi
Route::middleware('auth')->group(function () {
    // Rute Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Resource
    Route::resource('category', CategoryController::class);
    Route::resource('table', TableController::class);
    Route::resource('toping', TopingController::class);
    Route::resource('transaction', TransactionController::class);
    Route::resource('transaction_details', TransactionDetailController::class);
    Route::resource('payment_providers', PaymentProviderController::class);
    Route::resource('userInterface', userInterfaceController::class);
    Route::resource('error', ErrorController::class);

    // Rute Khusus Kategori
    Route::get('/category/check-name/{name}', [CategoryController::class, 'checkName'])->name('category.checkName');
    Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::get('/category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/category/{id}', [CategoryController::class, 'update'])->name('category.update');

    // Rute Khusus Tabel
    Route::get('/tables', function () {
        return response()->json(Table::all());
    });
    Route::get('/table/check-number/{number}', [TableController::class, 'checkNumber'])->name('table.checkNumber');
    Route::put('/table/{id}', [TableController::class, 'update'])->name('table.update');
    Route::delete('/table/{id}', [TableController::class, 'destroy'])->name('table.destroy');

    // Rute Khusus Toping
    Route::get('/toping/check-name/{name}', [TopingController::class, 'checkName'])->name('toping.checkName');

    // Rute Khusus Transaksi
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::put('/transaction/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('transaction.status');
    Route::put('/transaction/{id}/process', [TransactionController::class, 'process'])->name('transaction.process');
    Route::get('/check-status/{transaction}', [TransactionController::class, 'checkStatus'])->name('transaction.checkStatus');
    Route::get('/transaction/report', [TransactionController::class, 'report'])->name('transaction.report');
    Route::get('/transaction/print-all', [TransactionController::class, 'printAll'])->name('transaction.print.all');
    Route::get('/transaction/{id}/print', [TransactionController::class, 'print'])->name('transaction.print');
    Route::get('/transaksi/print/{transaction}', [TransactionController::class, 'print'])->name('transaksi.print');
    Route::post('/confirm-payment', [TransactionController::class, 'confirmPayment'])->name('confirm.payment');

    // Rute Detail Transaksi
    Route::get('/transactions/{transaction}/details', [TransactionDetailController::class, 'show'])->name('transaction_details.show');
    Route::get('/transaction-details/report', [TransactionDetailController::class, 'report'])->name('transaction_details.report');
    Route::delete('/transaction-details/clear-all', [TransactionDetailController::class, 'destroyAll'])->name('transaction_details.destroyAll');

    // Rute Payment Provider
    Route::put('/payment_providers/{payment_provider}/toggle-status', [PaymentProviderController::class, 'toggleStatus'])->name('payment_providers.toggle-status');

    // Rute User Interface
    Route::get('/user_interface', [userInterfaceController::class, 'index'])->name('userInterface.index');
    Route::post('/confirm-payment', [userInterfaceController::class, 'confirmPayment'])->name('payment.confirm');
});