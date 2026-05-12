@extends('layouts.app')

@section('content')
    <div class="max-w-2xl">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Create User</h1>
            <p class="text-sm text-slate-600 mt-1">Add a new user to the system</p>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                {{-- Name Field --}}
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror"
                        placeholder="Enter user name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email Field --}}
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-300 @enderror"
                        placeholder="user@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role Field --}}
                <div class="mb-6">
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-2">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('role') border-red-300 @enderror">
                        <option value="">Select Role</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Admin: Full access • Cashier: POS only</p>
                </div>

                {{-- Password Field --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-300 @enderror"
                        placeholder="Minimum 8 characters">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Confirmation Field --}}
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Re-enter password">
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
