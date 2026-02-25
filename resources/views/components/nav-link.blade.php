@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3.5 py-2 rounded-xl bg-indigo-50/80 text-sm font-semibold text-indigo-700 transition-all duration-200'
            : 'inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-white/60 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
