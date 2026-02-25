@props(['padding' => 'p-6 sm:p-7'])

<div {{ $attributes->merge(['class' => "card-premium rounded-2xl {$padding}"]) }}>
    {{ $slot }}
</div>
