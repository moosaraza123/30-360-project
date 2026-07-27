@extends('layouts.platform')

@section('title', 'Terms of Use')
@section('meta_description', 'Terms of use for Hisabi — the Gulf finance calculators platform.')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">Terms of Use</h1>
    <p class="mt-1 text-sm text-ink-faint">Last updated: {{ config('legal.updated', '2026-07-24') }}</p>

    <div class="card prose-sm mt-6 space-y-5 p-6 leading-relaxed text-ink-soft sm:p-8">
        <section>
            <h2 class="text-base font-bold text-ink">1. The service</h2>
            <p class="mt-1.5">
                Hisabi provides free online financial calculators for informational purposes. By using the site
                you accept these terms.
            </p>
        </section>

        <section>
            <h2 class="text-base font-bold text-ink">2. No professional advice</h2>
            <p class="mt-1.5">
                Results are estimates based on published laws, rates, and standard formulas at the stated
                "last reviewed" date. They are <strong>not</strong> legal, tax, financial, or religious advice,
                and they do not replace the official calculators or determinations of any government authority
                (e.g. MOHRE, GOSI, ZATCA, GPSSA). Always verify significant decisions with the relevant
                authority or a qualified professional.
            </p>
        </section>

        <section>
            <h2 class="text-base font-bold text-ink">3. Accuracy</h2>
            <p class="mt-1.5">
                We work to keep calculations accurate and cite the sources each calculator implements. Laws and
                rates change; we do not guarantee that every value is current at all times. Use of results is at
                your own risk, and we accept no liability for decisions made based on them.
            </p>
        </section>

        <section>
            <h2 class="text-base font-bold text-ink">4. Accounts</h2>
            <p class="mt-1.5">
                You are responsible for your account credentials. We may suspend accounts used for abuse,
                scraping, or attempts to disrupt the service. You can delete your account at any time.
            </p>
        </section>

        <section>
            <h2 class="text-base font-bold text-ink">5. Intellectual property</h2>
            <p class="mt-1.5">
                The site's design, content, and software are owned by Hisabi. You may link to any page freely.
                Embedding or white-label use of the calculators requires written permission —
                contact <a href="mailto:hello@hisabi.com" class="text-brand underline">hello@hisabi.com</a>.
            </p>
        </section>

        <section>
            <h2 class="text-base font-bold text-ink">6. Changes</h2>
            <p class="mt-1.5">
                We may update these terms; the "last updated" date reflects the current version. Continued use
                after changes constitutes acceptance.
            </p>
        </section>
    </div>
</div>
@endsection
