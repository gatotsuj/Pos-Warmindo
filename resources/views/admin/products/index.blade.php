@extends('layouts.app')

@section('title', 'Products')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Products</h2>
            <p class="text-gray-600 mt-1">Manage your product inventory</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Product
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <form action="{{ route('admin.products.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Search --}}
                    <div>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search name or SKU..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    {{-- Category Filter --}}
                    <div>
                        <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Stock Filter --}}
                    <div>
                        <select name="stock" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Stock</option>
                            <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>Low Stock (≤10)</option>
                            <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'category', 'stock', 'status']))
                            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Total Products</p>
            <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Product::count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Active</p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\Product::active()->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Low Stock</p>
            <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Product::lowStock()->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Out of Stock</p>
            <p class="text-2xl font-bold text-red-600">{{ \App\Models\Product::where('stock', 0)->count() }}</p>
        </div>
    </div>

    {{-- Products Grid --}}
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow">
                    {{-- Image --}}
                    <div class="relative aspect-square bg-gray-100">
                        <img
                            src="{{ $product->image_url }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover"
                        >

                        {{-- Status Badge --}}
                        @if(!$product->is_active)
                            <span class="absolute top-2 left-2 px-2 py-1 bg-gray-800 text-white text-xs font-medium rounded">
                                Inactive
                            </span>
                        @endif

                        {{-- Stock Badge --}}
                        @if($product->stock <= 0)
                            <span class="absolute top-2 right-2 px-2 py-1 bg-red-600 text-white text-xs font-medium rounded">
                                Out of Stock
                            </span>
                        @elseif($product->isLowStock())
                            <span class="absolute top-2 right-2 px-2 py-1 bg-yellow-500 text-white text-xs font-medium rounded">
                                Low Stock
                            </span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="font-semibold text-gray-800 line-clamp-1">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $product->sku }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mb-3">
                            <span class="text-lg font-bold text-blue-600">{{ $product->formatted_price }}</span>
                            <span class="text-sm text-gray-500">Stock: {{ $product->stock }}</span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">
                                {{ $product->category->name }}
                            </span>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="text-blue-600 hover:text-blue-800">
                                    Edit
                                </a>

                                {{-- Delete --}}
                                <div x-data="{ showConfirm: false }">
                                    <button @click="showConfirm = true" class="text-red-600 hover:text-red-800">
                                        Delete
                                    </button>

                                    {{-- Delete Modal --}}
                                    <div x-show="showConfirm"
                                         x-transition
                                         class="fixed inset-0 z-50 flex items-center justify-center"
                                         style="display: none;">
                                        <div class="fixed inset-0 bg-black opacity-50" @click="showConfirm = false"></div>
                                        <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm mx-4 relative z-10">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Product</h3>
                                            <p class="text-gray-600 mb-4">
                                                Delete "<strong>{{ $product->name }}</strong>"? This action cannot be undone.
                                            </p>
                                            <div class="flex justify-end gap-3">
                                                <button @click="showConfirm = false" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                                                    Cancel
                                                </button>
                                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No products found</h3>
            <p class="mt-2 text-gray-500">
                @if(request()->hasAny(['search', 'category', 'stock']))
                    No products match your filters. Try adjusting your search.
                @else
                    Get started by adding your first product.
                @endif
            </p>
            @if(!request()->hasAny(['search', 'category', 'stock']))
                <div class="mt-6">
                    <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Product
                    </a>
                </div>
            @endif
        </div>
    @endif
@endsection
