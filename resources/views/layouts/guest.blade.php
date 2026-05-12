<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Warmindo POS') }} - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased relative min-h-screen bg-slate-50 overflow-hidden">
        {{-- Animated Background Gradient --}}
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-indomie-yellow/20 via-white to-indomie-red/10"></div>
        
        {{-- Product Image Pattern --}}
        <div class="absolute inset-0 z-0 opacity-10" 
             style="background-image: url('{{ asset('images/login-bg.png') }}'); background-size: 250px; background-repeat: repeat; transform: rotate(-10deg) scale(1.5);">
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10 backdrop-blur-[2px]">
            <div class="mb-8 text-center">
                <a href="/" class="inline-flex flex-col items-center gap-3 group">
                    <div class="w-20 h-20 bg-indomie-red text-white flex items-center justify-center rounded-2xl shadow-xl shadow-indomie-red/20 transform -rotate-3 group-hover:rotate-0 transition-transform duration-300 ease-out">
                        <span class="text-3xl">🍜</span>
                    </div>
                    <span class="text-3xl font-black tracking-tight text-gray-800">WARMINDO<span class="text-indomie-red">POS</span></span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-white/90 backdrop-blur-xl shadow-2xl border border-white overflow-hidden sm:rounded-3xl relative">
                <!-- Decorative top border -->
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-indomie-yellow via-indomie-red to-indomie-green"></div>
                
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-sm text-gray-500 font-medium">
                &copy; {{ date('Y') }} Warmindo POS.
            </div>
        </div>
    </body>
</html>
