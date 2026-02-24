@props(['padding' => 'p-6'])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm '.$padding]) }}>
    {{ $slot }}
</div>
