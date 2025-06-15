<?php

namespace App\Http\Controllers;

use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TransactionDetailController extends Controller
{
    /**
     * Menampilkan daftar detail transaksi.
     * Admin dapat melihat semua transaksi, pengguna lain hanya melihat transaksi milik mereka.
     */
    public function index(Request $request)
    {
        // Buat query dengan relasi dan urutkan dari yang terbaru
        $query = TransactionDetail::with(['transaction', 'toping'])->latest();

        // Filter berdasarkan user_id untuk non-admin
        if (!Gate::allows('role-A')) {
            $userId = Auth::id();
            $query->whereHas('transaction', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        // Filter berdasarkan rentang tanggal jika ada
        if ($request->start_date && $request->end_date) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            
            $query->whereHas('transaction', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
                // Terapkan filter user_id untuk non-admin
                if (!Gate::allows('role-A')) {
                    $q->where('user_id', Auth::id());
                }
            });
        }

        // Kelompokkan transaksi berdasarkan transaction_id
        $groupedTransactions = $query->get()->groupBy('transaction_id');

        // Paginasi manual
        $perPage = 10;
        $page = $request->get('page', 1);
        $total = $groupedTransactions->count();
        $groupedTransactions = $groupedTransactions->slice(($page - 1) * $perPage, $perPage);

        // Buat koleksi paginasi
        $details = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedTransactions,
            $total,
            $perPage,
            $page,
            ['path' => route('transaction_details.index')]
        );

        return view('page.transaction_detail.index', [
            'details' => $details
        ]);
    }

    /**
     * Menampilkan laporan detail transaksi.
     * Admin dapat melihat semua transaksi, pengguna lain hanya melihat transaksi milik mereka.
     */
    public function report(Request $request)
    {
        // Buat query dengan relasi
        $query = TransactionDetail::with(['transaction', 'toping']);

        // Filter berdasarkan user_id untuk non-admin
        if (!Gate::allows('role-A')) {
            $userId = Auth::id();
            $query->whereHas('transaction', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        // Filter berdasarkan rentang tanggal jika ada
        if ($request->start_date && $request->end_date) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            
            $query->whereHas('transaction', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
                // Terapkan filter user_id untuk non-admin
                if (!Gate::allows('role-A')) {
                    $q->where('user_id', Auth::id());
                }
            });
        }

        // Kelompokkan transaksi berdasarkan transaction_id
        $groupedTransactions = $query->get()->groupBy('transaction_id');

        return view('page.transaction_detail.report', [
            'transactions' => $groupedTransactions,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
    }

    /**
     * Menghapus semua detail transaksi.
     * Admin dapat menghapus semua, pengguna lain hanya menghapus milik mereka.
     */
    public function destroyAll()
    {
        // Filter berdasarkan user_id untuk non-admin
        if (!Gate::allows('role-A')) {
            $userId = Auth::id();
            TransactionDetail::whereHas('transaction', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->delete();
        } else {
            // Admin dapat menghapus semua detail transaksi
            TransactionDetail::truncate();
        }

        return redirect()->back()->with('success', 'Semua detail transaksi berhasil dihapus');
    }
}