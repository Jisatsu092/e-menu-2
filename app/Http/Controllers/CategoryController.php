<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori dengan pagination dan pencarian.
     */
    public function index()
    {
        try {
            $entries = request()->input('entries', 5);
            $search = request()->input('search');
            
            $categories = Category::when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })->paginate($entries);

            return view('page.category.index', compact('categories', 'entries', 'search'));
        } catch (\Exception $e) {
            return redirect()->route('error.index')->with('error_message', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories|max:255',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->route('category.index')->withErrors($validator)->withInput();
        }

        try {
            Category::create($request->all());
            if ($request->ajax()) {
                return response()->json(['success' => 'Kategori berhasil ditambahkan.']);
            }
            return redirect()->route('category.index')->with('success', 'Kategori berhasil ditambahkan.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data.'], 500);
            }
            return redirect()->route('error.index')->with('error_message', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data kategori yang ada.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:categories,name,' . $id,
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->route('category.index')->withErrors($validator)->withInput();
        }

        try {
            $category = Category::findOrFail($id);
            $category->update($request->all());
            if ($request->ajax()) {
                return response()->json(['success' => 'Kategori berhasil diperbarui.']);
            }
            return redirect()->route('category.index')->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Terjadi kesalahan saat memperbarui data.'], 500);
            }
            return redirect()->route('error.index')->with('error_message', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus kategori dari database.
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return redirect()->route('category.index')->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('error.index')->with('error_message', 'Error: ' . $e->getMessage());
        }
    }
}