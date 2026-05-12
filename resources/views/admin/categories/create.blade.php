@extends('layouts.app')

@section('content')
    <div class="max-w-2xl">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Create Category</h1>
            <p class="text-sm text-slate-600 mt-1">Add a new product category</p>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                {{-- Name Field --}}
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror"
                        placeholder="Enter category name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description Field --}}
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-300 @enderror"
                        placeholder="Enter category description (optional)">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Maximum 1000 characters</p>
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('admin.categories.index') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
