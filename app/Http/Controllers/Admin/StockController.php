<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Halaman daftar riwayat pergerakan stok.
     */
    public function index(Request $request): View
    {
        $query = StockMovement::with(['product', 'user'])
            ->latest();

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $movements = $query->paginate(20)->withQueryString();
        $products  = Product::orderBy('name')->get();

        return view('admin.stock.index', compact('movements', 'products'));
    }

    /**
     * Form tambah stok masuk untuk satu produk.
     */
    public function create(Request $request): View
    {
        $products = Product::orderBy('name')->get();
        $selected = $request->product_id ? Product::find($request->product_id) : null;

        return view('admin.stock.create', compact('products', 'selected'));
    }

    /**
     * Simpan penambahan stok.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
            'notes'      => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $stockBefore = $product->stock;
        $product->increment('stock', $validated['quantity']);
        $stockAfter = $product->fresh()->stock;

        StockMovement::create([
            'product_id'   => $product->id,
            'user_id'      => auth()->id(),
            'type'         => 'in',
            'quantity'     => $validated['quantity'],
            'stock_before' => $stockBefore,
            'stock_after'  => $stockAfter,
            'reference'    => 'MANUAL-' . now()->format('YmdHis'),
            'notes'        => $validated['notes'],
        ]);

        return redirect()
            ->route('admin.stock.index')
            ->with('success', "Stok {$product->name} berhasil ditambah +{$validated['quantity']} unit (Total: {$stockAfter}).");
    }
}
