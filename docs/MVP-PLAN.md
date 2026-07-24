# Day Count Calculator — MVP & Monetization Plan

*Owner: Moosa · Drafted 2026-07-24 · Status: post-hardening, pre-launch*

## 1. What this product is (and honestly, what it isn't)

A free, professional-grade day count convention calculator (9 conventions, step-by-step
breakdowns, comparison tool, PDF/CSV export) aimed at fixed-income students, junior
analysts, treasury/ops staff, and fintech developers.

**Honest market read:** "day count convention calculator" is a narrow, low-volume,
high-intent niche. This will not become a large business by itself. It CAN become a
reliable passive-income niche asset ($100–$1,000/month range within 12 months if the
content plan is executed), and it is a credible seed for a broader "fixed-income tools"
suite where the real upside lives (bulk/API/B2B, Arabic-language market).

**Unfair advantages to exploit:**
- Step-by-step breakdowns (competitors show only the result) → better content, better SEO.
- Regional angle: Arabic-language financial calculators and **sukuk/GCC conventions**
  are dramatically underserved. Nobody owns this SERP in Arabic.

## 2. Revenue model (stacked, in order of activation)

| # | Stream | Activation gate | Realistic monthly value at 12 mo |
|---|--------|-----------------|----------------------------------|
| 1 | Display ads (AdSense → upgrade to Ezoic/Raptive later) | ~1k organic sessions/mo | $20–150 |
| 2 | Affiliate placements (CFA/FRM prep courses, finance data tools, brokers) | Content live | $0–300 (lumpy) |
| 3 | **Pro one-time license ($29) or $5/mo**: bulk CSV calculations, true Excel export, branded/white-label PDF, unlimited saved portfolios | Traffic + Stripe/Paddle | $50–400 |
| 4 | **Calculation API** (metered key, e.g. $9/mo for 10k calls; list on RapidAPI) | Small dev effort | $0–200 |
| 5 | B2B embed/white-label widget for edu sites, brokers, GCC banks' learning portals | Outbound, later | $0 → potentially the biggest |
| 6 | Email list (2k+ subs): sponsorship/newsletter | 12+ months | later |

Costs: VPS $6–12/mo, domain ~$12/yr, transactional email free tier (Resend/SES),
Plausible or self-hosted analytics. Total burn ≈ **$10–15/month** — everything above
that is margin.

## 3. Phased plan

### Phase 0 — Trust & correctness (DONE 2026-07-24)
- Hardcoded super-admin credential removed from source (env-driven seeder).
  **Still required: rotate the exposed password + scrub git history; raise with
  security/compliance.**
- IDOR fixed (calculation ownership enforced on save).
- Day-count math corrected (30E/360, 30E/360 ISDA maturity exception, 30/360 US EOM
  rules, Act/Act calendar split, Actual/365 Fixed naming) — 21 known-answer unit tests.
- Email verification enabled, subscribe/unsubscribe flow fixed + throttled, icons fixed.
- 56-test suite green on MySQL (`devlocal_testing`).

### Phase 1 — Launch-ready (Week 1–2)
1. Domain + deploy (any $6 VPS + Caddy/nginx; queue worker via systemd; `APP_DEBUG=false`,
   `SESSION_SECURE_COOKIE=true`).
2. Real mail: Resend/SES + SPF/DKIM (log driver currently).
3. Privacy page + cookie/GA consent + data-retention job for guest `ip_address`
   (PDPL hygiene; scheduler currently has no cleanup task).
4. SEO base: fix sitemap `lastmod` (currently always `now()`), per-convention meta,
   FAQ/HowTo schema on educational pages, submit to Search Console.
5. Analytics events: calculation completed, comparison run, export, email signup.

### Phase 2 — Traffic engine (Week 3–12) ← *this decides whether money ever arrives*
- 2 articles/week targeting long-tail: "30/360 vs actual/360 loan interest",
  "accrued interest calculation example", "day count convention cheat sheet",
  "actual/actual ISDA vs ICMA", per-convention worked examples (reuse the
  step-by-step engine output as content).
- Add adjacent calculators to widen the keyword net (each = new landing page):
  accrued interest, date difference/business days, simple loan interest.
- Free linkable asset: downloadable one-page "Day Count Conventions Cheat Sheet" (PDF)
  gated by email → grows the list.
- **Arabic versions of the calculator + top 5 articles** (differentiator; low competition).
- Gate: AdSense application at ~1k sessions/mo.

### Phase 3 — Pro tier (Month 3–4, only after ≥3k sessions/mo)
- Bulk CSV upload (compute 1,000s of rows) — the single most requested "pro" feature
  in this category and trivially built on the existing Feature classes.
- Real Excel export (maatwebsite/excel is already a dependency; currently stubbed).
- White-label PDF (logo upload) for advisors/lecturers.
- Checkout via Paddle/Lemon Squeezy (merchant-of-record → no tax headache).
- Price test: $29 lifetime vs $5/mo. Expect 0.5–2% of engaged users.

### Phase 4 — API + B2B (Month 5+)
- `/api/v1/calculate` with API keys + metering (route file already reserved).
- List on RapidAPI; write "day count API" dev docs (SEO for developers).
- Outbound: 10 GCC fintech/edu targets/month for the embeddable widget.

## 4. KPIs (weekly dashboard — admin panel already exists)
- Organic sessions; calculation completions; email signups (rate ≥2% of sessions);
- Later: Pro conversion, API keys issued, RPM.

## 5. Kill/pivot criteria (be honest with yourself)
- If after 6 months of consistent content: <2k sessions/mo → stop content spend,
  keep site as $0-effort ad asset, reuse engine for the Arabic/sukuk pivot.
- If Pro conversion <0.3% after 3 months → drop subscriptions, keep one-time license.

## 6. Deferred tech debt (tracked, not blocking launch)
- Consolidate the triple-duplicated feature `match` into a registry + interface.
- Remove dead frontend code (unused bundled calculator JS, Flatpickr, module vite config,
  `vite-module-loader.js`); extract duplicated modal/chart JS from Blade views.
- Remove or use `spatie/laravel-sitemap`; educational pages' hardcoded examples should be
  generated from the engine to prevent drift.
- Replace `alert()/confirm()/location.reload()` UX with inline feedback.
- `addslashes()` in `saved-list.blade.php` onclick handlers → use data-attributes.

---
*Note: pricing, ad, and revenue figures are planning estimates, not commitments. Anything
customer-facing (privacy policy, paid tier terms, regional compliance) needs review by a
qualified person before going live.*
