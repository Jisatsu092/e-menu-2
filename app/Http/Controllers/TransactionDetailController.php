<?php

namespace App\Http\Controllers;

use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionDetailController extends Controller
{
    public function index(Request $request)
    {
        $query = TransactionDetail::with(['transaction', 'toping'])->latest();

        // Filter berdasarkan tanggal jika ada
        if ($request->start_date && $request->end_date) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            
            $query->whereHas('transaction', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            });
        }

        // Ambil data dan kelompokkan berdasarkan transaction_id
        $groupedTransactions = $query->get()->groupBy('transaction_id');

        // Untuk paginasi, kita perlu menangani secara manual
        $perPage = 10;
        $page = $request->get('page', 1);
        $total = $groupedTransactions->count();
        $groupedTransactions = $groupedTransactions->slice(($page - 1) * $perPage, $perPage);

        // Buat koleksi paginasi manual
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

    // Method report tetap sama
    public function report(Request $request)
    {
        $query = TransactionDetail::with(['transaction', 'toping']);

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

    public function destroyAll()
    {
        TransactionDetail::truncate();
        return redirect()->back()->with('success', 'Semua detail transaksi berhasil dihapus');
    }
}