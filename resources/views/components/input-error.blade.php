@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1 mt-1.5']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center gap-1.5">
                <svg class="h-3.5 w-3.5 flex-shrink-0 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                {{ $message }}
            </li>
        @endforeach
    </ul>
@endif
