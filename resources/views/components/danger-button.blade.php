<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-sm shadow-red-200 hover:shadow-md hover:shadow-red-200 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
