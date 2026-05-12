@extends('layouts.app')

@section('content')
    <div class="max-w-2xl">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Edit User</h1>
            <p class="text-sm text-slate-600 mt-1">Update user information</p>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Name Field --}}
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
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
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
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
                    <select id="role" name="role" required {{ auth()->id() === $user->id ? 'disabled' : '' }}
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('role') border-red-300 @enderror {{ auth()->id() === $user->id ? 'bg-slate-50' : '' }}">
                        <option value="">Select Role</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="cashier" {{ old('role', $user->role) === 'cashier' ? 'selected' : '' }}>Cashier
                        </option>
                    </select>
                    @if (auth()->id() === $user->id)
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <p class="mt-1 text-xs text-amber-600">You cannot change your own role</p>
                    @else
                        <p class="mt-1 text-xs text-slate-500">Admin: Full access • Cashier: POS only</p>
                    @endif
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Section --}}
                <div class="mb-6 pt-6 border-t border-slate-200">
                    <h3 class="text-sm font-medium text-slate-700 mb-4">Change Password</h3>

                    {{-- New Password Field --}}
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                            New Password
                        </label>
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-300 @enderror"
                            placeholder="Leave blank to keep current password">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-slate-500">Minimum 8 characters</p>
                    </div>

                    {{-- Password Confirmation Field --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                            Confirm New Password
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Re-enter new password">
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
