<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indomie-red to-red-700 border border-transparent rounded-xl font-black text-sm text-white uppercase tracking-widest shadow-lg shadow-indomie-red/20 hover:from-red-600 hover:to-red-800 hover:shadow-xl hover:-translate-y-0.5 focus:bg-red-700 active:bg-red-800 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-indomie-red focus:ring-offset-2 transition-all duration-300 ease-out']) }}>
    {{ $slot }}
</button>
