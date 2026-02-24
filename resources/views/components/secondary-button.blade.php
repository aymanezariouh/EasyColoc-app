<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg px-4 py-2 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 disabled:opacity-60']) }}>
    {{ $slot }}
</button>
