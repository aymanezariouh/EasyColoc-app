@props(['color' => 'gray'])

@php
    $classes = match ($color) {
        'green' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10',
        'indigo' => 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600/10',
        'red' => 'bg-red-50 text-red-700 ring-1 ring-red-600/10',
        'amber' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/10',
        'blue' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/10',
        default => 'bg-gray-50 text-gray-600 ring-1 ring-gray-500/10',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide '.$classes]) }}>
    {{ $slot }}
</span>
