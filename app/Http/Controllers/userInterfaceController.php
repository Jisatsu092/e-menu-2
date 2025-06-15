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
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class userInterfaceController extends Controller
{
    public function index()
    {
        try {
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
        } catch (\Exception $e) {
            return redirect()->route('error.index')
                ->with('error_message', 'Error: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'table_id' => 'required|exists:tables,id',
            ]);

            $table = Table::findOrFail($request->table_id);
            if ($table->status === 'occupied') {
                return response()->json([
                    'success' => false,
                    'message' => 'Meja sudah terisi'
                ], 422);
            }

            $table->update([
                'status' => 'occupied',
                'occupied_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Table status updated']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validasi gagal'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirmPayment(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'provider_id' => 'required|exists:payment_providers,id',
                'order_data' => 'required|json',
            ]);

            // Parse and validate order data
            $orderData = json_decode($request->input('order_data'), true);
            if (!is_array($orderData) || !isset($orderData['table_id'], $orderData['items'], $orderData['total_price'])) {
                throw new \Exception('Struktur data pesanan tidak valid');
            }

            if (!is_array($orderData['items']) || empty($orderData['items'])) {
                throw new \Exception('Pesanan harus memiliki setidaknya satu item');
            }

            DB::beginTransaction();

            // Validate items and calculate total
            $totalPrice = 0;
            $allItems = [];
            $firstPersonSettings = null;

            foreach ($orderData['items'] as $personIndex => $person) {
                if (!isset($person['person'], $person['items'], $person['spiciness_level'], $person['bowl_size'])) {
                    throw new \Exception("Data untuk " . (isset($person['person']) ? $person['person'] : 'Orang ' . ($personIndex + 1)) . " tidak lengkap");
                }

                if (!empty($person['items'])) {
                    if (empty($person['spiciness_level']) || empty($person['bowl_size'])) {
                        throw new \Exception("Harap pilih tingkat kepedasan dan ukuran mangkuk untuk " . (isset($person['person']) ? $person['person'] : 'Orang ' . ($personIndex + 1)));
                    }

                    // Store first person's settings for transaction
                    if ($firstPersonSettings === null) {
                        $firstPersonSettings = [
                            'spiciness_level' => $person['spiciness_level'],
                            'bowl_size' => $person['bowl_size']
                        ];
                    }

                    foreach ($person['items'] as $item) {
                        if (!isset($item['id'], $item['quantity'], $item['price'])) {
                            throw new \Exception('Item pesanan tidak valid');
                        }

                        $toping = Toping::findOrFail($item['id']);
                        if ($toping->stock < $item['quantity']) {
                            throw new \Exception("Stok {$toping->name} tidak cukup. Tersisa: {$toping->stock}");
                        }

                        // Use price_buy from database for consistency
                        $subtotal = $toping->price_buy * $item['quantity'];
                        $totalPrice += $subtotal;

                        $allItems[] = [
                            'id' => $item['id'],
                            'quantity' => $item['quantity'],
                            'price' => $toping->price_buy,
                            'subtotal' => $subtotal
                        ];
                    }
                }
            }

            if (empty($allItems)) {
                throw new \Exception('Pesanan harus memiliki setidaknya satu topping');
            }

            // Verify total price
            if (abs($totalPrice - $orderData['total_price']) > 0.01) {
                throw new \Exception('Total harga tidak sesuai. Dihitung: Rp' . number_format($totalPrice, 0, ',', '.') . ', Diterima: Rp' . number_format($orderData['total_price'], 0, ',', '.'));
            }

            // Generate unique transaction code
            $transactionCode = 'TRX-' . strtoupper(Str::random(8));

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'table_id' => $orderData['table_id'] === 'takeaway' ? null : $orderData['table_id'],
                'code' => $transactionCode,
                'spiciness_level' => $firstPersonSettings['spiciness_level'],
                'bowl_size' => $firstPersonSettings['bowl_size'],
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_provider_id' => $request->provider_id,
            ]);

            // Update table status
            if ($orderData['table_id'] !== 'takeaway') {
                $table = Table::findOrFail($orderData['table_id']);
                if ($table->status === 'occupied') {
                    throw new \Exception('Meja sudah terisi oleh pelanggan lain');
                }
                $table->update([
                    'status' => 'occupied',
                    'occupied_at' => now()
                ]);
            }

            // Create transaction details and update stock
            foreach ($allItems as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'toping_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
                Toping::findOrFail($item['id'])->decrement('stock', $item['quantity']);
            }

            // Handle payment proof
            if ($request->hasFile('payment_proof')) {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validasi gagal: ' . implode(', ', Arr::flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
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

    public function getTables()
    {
        try {
            $tables = Table::select('id', 'number', 'status', 'occupied_at')->orderBy('number')->get();
            return response()->json($tables);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching tables: ' . $e->getMessage()], 500);
        }
    }

    public function getTransactionStatus($transactionId)
    {
        try {
            $transaction = Transaction::findOrFail($transactionId);
            return response()->json(['status' => $transaction->status]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching status: ' . $e->getMessage()], 500);
        }
    }

    public function printTransaction($transactionId)
    {
        try {
            $transaction = Transaction::with('details.toping', 'table', 'paymentProvider', 'user')->findOrFail($transactionId);
            $html = view('transactions.print', compact('transaction'))->render();
            return response($html);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error generating print view: ' . $e->getMessage()], 500);
        }
    }
}