<?php

// app/Http/Controllers/Admin/ProductController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\CurrentTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->search($request->search)
            ->byCategory($request->category)
            ->when($request->stock === 'low', fn ($q) => $q->lowStock())
            ->when($request->stock === 'out', fn ($q) => $q->where('stock', 0))
            ->when($request->status === 'active', fn ($q) => $q->active())
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product->id);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Handle image removal
        if ($request->boolean('remove_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $validated['image'] = null;
        }

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product berhasil diupdate.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product berhasil dihapus.');
    }

    /**
     * Validate product data
     */
    private function validateProduct(Request $request, ?int $productId = null): array
    {
        return $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('tenant_id', CurrentTenant::id())),
            ],
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')
                    ->ignore($productId)
                    ->where(fn ($q) => $q->where('tenant_id', CurrentTenant::id())),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:999999'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_active' => ['boolean'],
        ], [
            'category_id.required' => 'Category wajib dipilih.',
            'category_id.exists' => 'Category tidak valid.',
            'name.required' => 'Nama product wajib diisi.',
            'sku.required' => 'SKU wajib diisi.',
            'sku.unique' => 'SKU sudah digunakan.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh negatif.',
            'stock.required' => 'Stock wajib diisi.',
            'stock.integer' => 'Stock harus berupa bilangan bulat.',
            'stock.min' => 'Stock tidak boleh negatif.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpg, jpeg, png, atau webp.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);
    }
}
