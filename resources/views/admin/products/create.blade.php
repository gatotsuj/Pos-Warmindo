@extends('layouts.app')

@section('title', 'Create Product')

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.products.index') }}" class="hover:text-blue-600">Products</a>
            <span>/</span>
            <span>Create</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Create New Product</h2>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" x-data="productForm()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Info --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>

                    <div class="space-y-4">
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                placeholder="Enter product name"
                                required
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SKU --}}
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">
                                SKU <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="sku"
                                id="sku"
                                value="{{ old('sku') }}"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('sku') border-red-500 @enderror"
                                placeholder="e.g., PRD-001"
                                required
                            >
                            @error('sku')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea
                                name="description"
                                id="description"
                                rows="4"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                                placeholder="Enter product description (optional)"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Pricing & Stock --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pricing & Stock</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Price --}}
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                Price (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                <input
                                    type="number"
                                    name="price"
                                    id="price"
                                    value="{{ old('price') }}"
                                    class="w-full pl-12 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-500 @enderror"
                                    placeholder="0"
                                    min="0"
                                    step="100"
                                    required
                                >
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Stock --}}
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                                Stock <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="stock"
                                id="stock"
                                value="{{ old('stock', 0) }}"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stock') border-red-500 @enderror"
                                placeholder="0"
                                min="0"
                                required
                            >
                            @error('stock')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Category & Status --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Organization</h3>

                    <div class="space-y-4">
                        {{-- Category --}}
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="category_id"
                                id="category_id"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror"
                                required
                            >
                                <option value="">Select category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">Active (visible in POS)</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Image --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Product Image</h3>

                    <div>
                        {{-- Preview --}}
                        <div class="mb-4">
                            <div
                                class="aspect-square bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center"
                                x-show="!imagePreview"
                            >
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <img
                                x-show="imagePreview"
                                :src="imagePreview"
                                class="aspect-square w-full object-cover rounded-lg"
                                style="display: none;"
                            >
                        </div>

                        {{-- Upload Input --}}
                        <input
                            type="file"
                            name="image"
                            id="image"
                            accept="image/jpeg,image/png,image/webp"
                            @change="previewImage($event)"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        >
                        @error('image')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">JPG, PNG or WebP. Max 2MB.</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="space-y-3">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Save Product
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="block w-full px-4 py-2 text-center text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        function productForm() {
            return {
                imagePreview: null,
                previewImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.imagePreview = URL.createObjectURL(file);
                    }
                }
            }
        }
    </script>
@endsection