<?php

namespace App\Http\Controllers;

use App\Models\Toping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TopingController extends Controller
{
    /**
     * Fungsi pembantu untuk menghitung price_buy dan membulatkan ke kelipatan 1000 atau 500
     */
    private function calculatePriceBuy($price)
    {
        $margin = $price * 0.25; // 25% dari harga beli
        $total = $price + $margin; // Harga jual awal
        $rounded = round($total / 500) * 500; // Round ke kelipatan 500
        return $rounded;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $search = request('search');
            $entries = request('entries', 5);

            $topings = Toping::with('category')
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                })
                ->paginate($entries)
                ->withQueryString();

            $categories = \App\Models\Category::all();

            return view('page.toping.index', [
                'topings' => $topings,
                'categories' => $categories,
                'search' => $search,
                'entries' => $entries
            ]);
        } catch (\Exception $e) {
            return redirect()->route('error.index')->with('error_message', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:topings|max:255',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $data = [
                'name' => Str::title($request->input('name')), // Ubah ke title case
                'category_id' => $request->input('category_id'),
                'price' => $request->input('price'),
                'price_buy' => $this->calculatePriceBuy($request->input('price')),
                'stock' => $request->input('stock'),
            ];

            if ($request->hasFile('image')) {
                $directory = public_path('toping_images');
                if (!file_exists($directory)) {
                    if (!mkdir($directory, 0755, true)) {
                        throw new \Exception('Gagal membuat direktori toping_images. Periksa izin direktori public.');
                    }
                }

                if (!is_writable($directory)) {
                    throw new \Exception('Direktori toping_images tidak dapat ditulis. Periksa izin direktori.');
                }

                $image = $request->file('image');
                $imageName = time() . '_' . preg_replace('/[^A-Za-z0-9\-\_\.]/', '', $image->getClientOriginalName());
                $imagePath = 'toping_images/' . $imageName;

                if (!$image->move($directory, $imageName)) {
                    throw new \Exception('Gagal menyimpan gambar. Periksa izin direktori atau ukuran file.');
                }

                $data['image'] = $imagePath;
            }

            Toping::create($data);

            return redirect()
                ->route('toping.index')
                ->with('success', 'Data topping berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->route('error.index')
                ->with('error_message', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required|max:255|unique:topings,name,' . $id,
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $data = [
                'name' => Str::title($request->input('name')), // Ubah ke title case
                'category_id' => $request->input('category_id'),
                'price' => $request->input('price'),
                'price_buy' => $this->calculatePriceBuy($request->input('price')),
                'stock' => $request->input('stock'),
            ];

            $toping = Toping::findOrFail($id);

            if ($request->hasFile('image')) {
                if ($toping->image && file_exists(public_path($toping->image))) {
                    unlink(public_path($toping->image));
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = 'toping_images/' . $imageName;
                $image->move(public_path('toping_images'), $imageName);

                $data['image'] = $imagePath;
            }
            $toping->update($data);

            return redirect()
                ->route('toping.index')
                ->with('success', 'Data topping berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->route('error.index')
                ->with('error_message', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $toping = Toping::findOrFail($id);

            if ($toping->stock > 0) {
                return redirect()
                    ->route('error.index')
                    ->with('error_message', 'Error: Tidak dapat menghapus toping yang masih memiliki stok.');
            }

            if ($toping->image) {
                Storage::disk('public')->delete($toping->image);
            }

            $toping->delete();
            return redirect()
                ->route('toping.index')
                ->with('success', 'Data topping berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('error.index')
                ->with('error_message', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Check if topping name exists
     */
    public function checkName($name, Request $request)
    {
        try {
            $id = $request->query('id');
            $exists = Toping::where('name', $name)
                ->when($id, function ($query) use ($id) {
                    $query->where('id', '!=', $id);
                })
                ->exists();

            return response()->json(['exists' => $exists]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle real-time search via AJAX
     */
    public function search(Request $request)
    {
        try {
            $search = $request->query('search');
            $entries = $request->query('entries', 5);

            $topings = Toping::with('category')
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                })
                ->paginate($entries)
                ->withQueryString();

            return response()->json($topings);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}   