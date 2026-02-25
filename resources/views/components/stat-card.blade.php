@props(['label', 'value', 'icon' => null, 'color' => 'indigo'])

@php
    $iconColors = match ($color) {
        'emerald', 'green' => 'bg-emerald-100 text-emerald-600',
        'red' => 'bg-red-100 text-red-600',
        'amber' => 'bg-amber-100 text-amber-600',
        'blue' => 'bg-blue-100 text-blue-600',
        default => 'bg-indigo-100 text-indigo-600',
    };
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-start gap-4 hover:shadow-md transition-shadow duration-200">
    @if ($icon)
        <div class="flex-shrink-0 w-12 h-12 rounded-xl {{ $iconColors }} flex items-center justify-center">
            {!! $icon !!}
        </div>
    @endif
    <div class="min-w-0 flex-1">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $label }}</p>
        <p class="mt-1 text-2xl font-bold text-gray-900 tracking-tight">{{ $value }}</p>
    </div>
</div>
