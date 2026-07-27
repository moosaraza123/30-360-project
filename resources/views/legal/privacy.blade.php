@extends('layouts.platform')

@section('title', 'Privacy Policy')
@section('meta_description', 'How Hisabi handles your data: what we collect, why, how long we keep it, and your rights.')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">Privacy Policy</h1>
    <p class="mt-1 text-sm text-ink-faint">Last updated: {{ config('legal.updated', '2026-07-24') }}</p>

    <div class="card prose-sm mt-6 space-y-5 p-6 leading-relaxed text-ink-soft sm:p-8">
        <section>
            <h2 class="text-base font-bold text-ink">What we collect</h2>
            <p class="mt-1.5">
                <strong>Calculator inputs</strong> (amounts, dates, selections) are processed to show your result.
                Day-count calculations are stored with an anonymous session identifier so your history works;
                the Gulf calculators (gratuity, VAT, zakat, salary, loan) do not store your inputs.
                <strong>Account data</strong> (name, email, and optional profile fields) is stored when you register.
                <strong>Newsletter</strong>: your email, only after you confirm via the verification link (double opt-in).
                <strong>Technical data</strong>: like most websites we process IP addresses and browser information
                in server logs for security and abuse prevention.
            </p>
        </section>

        <section>
            <h2 class="text-base font-bold text-ink">What we don't do</h2>
            <p class="mt-1.5">
                We do not sell your personal data. We do not require an account to use any calculator.
                Calculator results are informational and are not shared with third parties.
            </p>
        </section>

        <section>
            <h2 class="text-base font-bold text-ink">Retention</h2>
            <p class="mt-1.5">
                IP addresses attached to anonymous calculation records are deleted automatically after 90 days.
                Account data is kept until you delete your account (Profile → Delete Account), which permanently
                removes your data. You can unsubscribe from the newsletter at any time via the link in every email.
            </p>
        </section>

        <section>
            <h2 class="text-base font-bold text-ink">Cookies &amp; advertising</h2>
            <p class="mt-1.5">
                We use strictly necessary cookies for sessions and security. We use Google Analytics to understand
                aggregate usage, and we may show ads via Google AdSense; Google may use cookies to personalize ads —
                see <a href="https://policies.google.com/technologies/ads" target="_blank" rel="noopener" class="text-brand underline">Google's advertising policies</a>
                and their <a href="https://adssettings.google.com" target="_blank" rel="noopener" class="text-brand underline">Ads Settings</a> to opt out of personalization.
            </p>
        </section>

        <section>
            <h2 class="text-base font-bold text-ink">Your rights</h2>
            <p class="mt-1.5">
                You may request access to, correction of, or deletion of your personal data at any time by
                contacting us at <a href="mailto:privacy@hisabi.com" class="text-brand underline">privacy@hisabi.com</a>.
            </p>
        </section>

        <section>
            <h2 class="text-base font-bold text-ink">Disclaimer</h2>
            <p class="mt-1.5">
                All calculators are provided for guidance only and do not constitute legal, tax, financial,
                or religious advice. Verify important figures with the relevant official authority or a
                qualified professional.
            </p>
        </section>
    </div>
</div>
@endsection
