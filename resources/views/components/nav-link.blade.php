@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-lg bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700 transition'
            : 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
