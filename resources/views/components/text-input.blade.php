@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-200 bg-gray-50 focus:bg-white focus:border-indomie-red focus:ring-indomie-red/20 rounded-xl shadow-sm transition-all duration-200 px-4 py-3']) }}>
