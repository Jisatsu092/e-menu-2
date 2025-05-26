<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PaymentProvider;
use App\Models\Table;
use App\Models\Toping;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class userInterfaceController extends Controller
{
    public function index()
    {
        $topings = Toping::all();
        $paymentProviders = PaymentProvider::all();
        $categories = Category::all();
        $tables = Table::select('id', 'number', 'status', 'occupied_at')->orderBy('number')->get();
        return view('page.user_interface.index', [
            'topings' => $topings,
            'categories' => $categories,
            'tables' => $tables,
            'paymentProviders' => $paymentProviders
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
        ]);

        $table = Table::findOrFail($request->table_id);
        $table->update([
            'status' => 'occupied',
            'occupied_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Table status updated']);
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'provider_id' => 'required|exists:payment_providers,id',
            'order_data' => 'required|json',
        ]);

        try {
            DB::beginTransaction();

            // Parse order data
            $orderData = json_decode($request->input('order_data'), true);

            // Validate order data
            if (!isset($orderData['table_id'], $orderData['spiciness_level'], $orderData['bowl_size'], $orderData['items'])) {
                throw new \Exception('Invalid order data');
            }

            // Validasi bahwa ada setidaknya satu topping
            if (empty($orderData['items'])) {
                throw new \Exception('Pesanan harus memiliki setidaknya satu topping.');
            }

            // Hitung total_price berdasarkan price_buy
            $totalPrice = 0;
            foreach ($orderData['items'] as $item) {
                $toping = Toping::findOrFail($item['id']);
                if ($toping->stock < $item['quantity']) {
                    throw new \Exception("Stok {$toping->name} tidak cukup.");
                }
                $totalPrice += $toping->price_buy * $item['quantity'];
            }

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'table_id' => $orderData['table_id'] === 'takeaway' ? null : $orderData['table_id'],
                'spiciness_level' => $orderData['spiciness_level'],
                'bowl_size' => $orderData['bowl_size'],
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_provider_id' => $request->provider_id,
            ]);

            // Update table status and occupied_at
            if ($orderData['table_id'] !== 'takeaway') {
                $table = Table::findOrFail($orderData['table_id']);
                $occupiedAt = isset($orderData['occupied_at']) && Carbon::parse($orderData['occupied_at'])->isValid()
                    ? Carbon::parse($orderData['occupied_at'])
                    : now();
                $table->update([
                    'status' => 'occupied',
                    'occupied_at' => $occupiedAt
                ]);
            }

            // Create transaction details
            foreach ($orderData['items'] as $item) {
                $toping = Toping::findOrFail($item['id']);
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'toping_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $toping->price_buy * $item['quantity'],
                ]);

                // Update stock
                $toping->decrement('stock', $item['quantity']);
            }

            // Handle payment proof
            if ($request->hasFile('payment_proof')) {
                // Pastikan direktori payment_proofs ada
                if (!file_exists(public_path('payment_proofs'))) {
                    mkdir(public_path('payment_proofs'), 0755, true);
                }
    
                // Hapus payment proof lama jika ada
                if ($transaction->payment_proof && file_exists(public_path($transaction->payment_proof))) {
                    unlink(public_path($transaction->payment_proof));
                }
    
                // Simpan payment proof baru
                $proof = $request->file('payment_proof');
                $proofName = time() . '_' . $proof->getClientOriginalName();
                $proofPath = 'payment_proofs/' . $proofName;
                $proof->move(public_path('payment_proofs'), $proofName);
    
                $transaction->update(['payment_proof' => $proofPath]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'transactionId' => $transaction->id,
                'status' => $transaction->status
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completeTransaction($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            if ($transaction->table) {
                $transaction->table->update([
                    'status' => 'available',
                    'occupied_at' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Table status updated to available'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}