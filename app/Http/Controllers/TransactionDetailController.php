<?php

namespace App\Http\Controllers;

use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionDetailController extends Controller
{
    // Index - Menampilkan semua detail transaksi
    public function index()
    {
        $transactionDetails = TransactionDetail::with(['transaction', 'toping'])
            ->latest()
            ->paginate(10);

        return view('page.transaction_detail.index', [
            'details' => $transactionDetails
        ]);
    }

    // Report - Untuk halaman cetak
    public function report(Request $request)
    {
        $query = TransactionDetail::with(['transaction', 'toping']);

        // Filter berdasarkan tanggal jika ada
        if ($request->start_date && $request->end_date) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            
            $query->whereHas('transaction', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            });
        }

        $groupedTransactions = $query->get()->groupBy('transaction_id');

        return view('page.transaction_detail.report', [
            'transactions' => $groupedTransactions,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
    }

    // Hapus semua data
    public function destroyAll()
    {
        TransactionDetail::truncate();
        return redirect()->back()->with('success', 'Semua detail transaksi berhasil dihapus');
    }
}