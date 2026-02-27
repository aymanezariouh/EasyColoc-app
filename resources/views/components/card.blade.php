@props(['padding' => 'p-6'])

<div {{ $attributes->merge(['class' => "ui-card {$padding}"]) }}>
    {{ $slot }}
</div>
