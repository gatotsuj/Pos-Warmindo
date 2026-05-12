@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.products.index') }}" class="hover:text-blue-600">Products</a>
            <span>/</span>
            <span>Edit</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Edit Product</h2>
    </div>

    {{-- Form --}}
    <form
        action="{{ route('admin.products.update', $product) }}"
        method="POST"
        enctype="multipart/form-data"
        x-data="productForm('{{ $product->image ? asset('storage/'.$product->image) : '' }}')"
    >
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Basic Info --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>

                    <div class="space-y-4">
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $product->name) }}"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                required
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SKU --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                SKU <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="sku"
                                value="{{ old('sku', $product->sku) }}"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('sku') border-red-500 @enderror"
                                required
                            >
                            @error('sku')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea
                                name="description"
                                rows="4"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                            >{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pricing & Stock --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pricing & Stock</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Price --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Price (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="price"
                                value="{{ old('price', $product->price) }}"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                min="0"
                                step="100"
                                required
                            >
                        </div>

                        {{-- Stock --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Stock <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="stock"
                                value="{{ old('stock', $product->stock) }}"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                min="0"
                                required
                            >
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Organization --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Organization</h3>

                    {{-- Category --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="category_id"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                            required
                        >
                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded"
                        >
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                {{-- Image --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Product Image</h3>

                    <div class="mb-4">
                        <img
                            x-show="imagePreview"
                            :src="imagePreview"
                            class="aspect-square w-full object-cover rounded-lg"
                        >
                    </div>

                    <input
                        type="file"
                        name="image"
                        accept="image/jpeg,image/png,image/webp"
                        @change="previewImage"
                        class="w-full text-sm text-gray-500"
                    >
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-lg shadow p-6 space-y-3">
                    <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Update Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="block text-center bg-gray-200 py-2 rounded-lg">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>

    <script>
        function productForm(existingImage) {
            return {
                imagePreview: existingImage,
                previewImage(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.imagePreview = URL.createObjectURL(file);
                    }
                }
            }
        }
    </script>
@endsection
