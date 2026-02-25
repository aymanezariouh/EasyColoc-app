@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition']) }}>
