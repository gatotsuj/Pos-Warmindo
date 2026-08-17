<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    protected StockMovementRepositoryInterface $stockMovementRepo;
    protected ProductRepositoryInterface $productRepo;

    public function __construct(
        StockMovementRepositoryInterface $stockMovementRepo,
        ProductRepositoryInterface $productRepo
    ) {
        $this->stockMovementRepo = $stockMovementRepo;
        $this->productRepo = $productRepo;
    }

    /**
     * Halaman daftar riwayat pergerakan stok.
     */
    public function index(Request $request): View
    {
        $movements = $this->stockMovementRepo->paginateFiltered($request->all(), 20)->withQueryString();
        $products  = $this->productRepo->allOrderedByName();

        return view('admin.stock.index', compact('movements', 'products'));
    }

    /**
     * Form tambah stok masuk untuk satu produk.
     */
    public function create(Request $request): View
    {
        $products = $this->productRepo->allOrderedByName();
        $selected = $request->product_id ? $this->productRepo->find($request->product_id) : null;

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

        $product = $this->productRepo->findOrFail($validated['product_id']);

        $movement = $this->stockMovementRepo->recordMovement(
            $product,
            $validated['quantity'],
            'in',
            'MANUAL-' . now()->format('YmdHis'),
            $validated['notes']
        );

        return redirect()
            ->route('admin.stock.index')
            ->with('success', "Stok {$product->name} berhasil ditambah +{$validated['quantity']} unit (Total: {$movement->stock_after}).");
    }
}
