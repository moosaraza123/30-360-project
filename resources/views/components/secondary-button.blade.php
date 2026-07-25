<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg border border-line bg-white px-5 py-2.5 text-sm font-semibold text-ink transition hover:border-brand hover:text-brand focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
