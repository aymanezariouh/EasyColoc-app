<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-700 rounded-xl px-5 py-2.5 text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-400/30 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
