@props(['padding' => 'p-6'])

<div {{ $attributes->merge(['class' => "rounded-xl border border-gray-200 bg-white shadow-sm {$padding}"]) }}>
    {{ $slot }}
</div>
