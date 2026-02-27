@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block w-full rounded-lg bg-indigo-50 px-3 py-2 text-left text-sm font-medium text-indigo-700'
    : 'block w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
