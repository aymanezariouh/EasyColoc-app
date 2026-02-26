@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block w-full rounded-md bg-indigo-50 px-3 py-2 text-left text-sm font-medium text-indigo-700'
    : 'block w-full rounded-md px-3 py-2 text-left text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
