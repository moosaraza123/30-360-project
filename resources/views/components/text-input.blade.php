@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-lg border-line text-ink shadow-sm focus:border-brand focus:ring-brand']) }}>
