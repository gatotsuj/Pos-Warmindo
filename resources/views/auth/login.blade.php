<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-black text-slate-800">Masuk ke Akun Anda</h2>
        <p class="text-sm text-slate-500 mt-2 font-medium">Silakan masukkan email dan kata sandi Anda</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" class="!mb-0" />
                @if (Route::has('password.request'))
                    <a class="text-sm font-bold text-indomie-red hover:text-red-700 hover:underline transition-colors focus:outline-none" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block mt-2 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indomie-red shadow-sm focus:ring-indomie-red/20 transition-all duration-200" name="remember">
                <span class="ms-2 text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <div>
            <x-primary-button class="w-full justify-center text-sm py-3.5">
                {{ __('Masuk Sekarang') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
