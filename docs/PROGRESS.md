# Project Progress Log — GCC Finance Calculators Platform (formerly 30/360 Calculator)

*Last updated: 2026-07-24 · Companion doc: [MVP-PLAN.md](MVP-PLAN.md) (monetization roadmap)*

> **STRATEGY PIVOT (2026-07-24, user-confirmed):** this codebase becomes a **GCC finance
> calculators platform** — UAE + KSA only, Arabic + English (RTL). The day-count
> calculator is the first module, not the brand. 30360calculator.com was NOT purchased;
> platform domain TBD (shortlist checked: gulfcalculator.com, hisabi.com, khaleejcalc.com
> all showed no DNS). Build order: gratuity UAE/KSA → VAT (15%/5%) → **zakat by Dec 2026**
> (Ramadan ~Feb 2027 traffic) → salary/loan (lead-gen) → sukuk suite. Target $1–2k/mo at
> 12–18 months via GCC ads + lead-gen + B2B. Next structural work: Arabic i18n + RTL
> layer, then gratuity module. TenderIQ (separate repo) noted as the faster-revenue asset.

This file is the session-to-session continuation point. Read it top-to-bottom before
resuming work.

---

> **PLATFORM BUILD COMPLETE (2026-07-24, second work session):** Phases A–C of
> PLATFORM-PLAN.md implemented in one pass. New: Tailwind design system (ink/teal/gold
> tokens, IBM Plex Sans + Arabic), `layouts/platform.blade.php` shell, public bilingual
> homepage at `/` and `/ar` (dashboard moved to `/dashboard`), SetLocale middleware
> (/ar prefix → RTL Arabic, ~100 translated strings in lang/ar.json), and the
> **GulfCalculators module**: Gratuity UAE (Art. 51 + 2yr cap), Gratuity KSA
> (Arts. 84–85 + resignation fractions), VAT UAE/KSA, Zakat (85g-gold nisab, config
> defaults in Modules/GulfCalculators/config/config.php — update gold prices
> periodically). All day-count pages converted from Bootstrap to the design system
> (no inline styles / alert() / Bootstrap modals — Alpine toasts+modals). Sitemap
> covers everything EN+AR with hreflang. **Suite: 99 tests / 257 assertions green.**
> Remaining before deploy: buy hisabi.com, Arabic native-speaker copy review,
> privacy/terms pages, admin panel still on old Bootstrap master layout (fine),
> zakat gold-price API (roadmap).

## 1. Current state (TL;DR)

- Full end-to-end audit done; **all critical/high issues fixed**.
- Test suite: **63 tests, 171 assertions, all passing** (`vendor/bin/phpunit`).
- App runs locally: `php artisan serve` → http://127.0.0.1:8000 (calculator at `/calculator`).
- Monetization workstream 1 of 4 (**Ad + SEO infrastructure**) is **complete**.
- **Nothing committed yet** — all work sits as uncommitted changes on `main` (~55 files).

## 2. Local environment

| Thing | Value |
|---|---|
| PHP | 8.3 (machine) — `composer.lock` was regenerated for 8.3/Symfony 7; original lock was built on PHP 8.4 |
| App DB | MySQL `devlocal` (migrated + seeded: 100 calculations, 50 subscribers) |
| Test DB | MySQL `devlocal_testing` — **RefreshDatabase wipes it every run; never point phpunit at devlocal** |
| DB credentials | in `.env` (root user; local only) |
| Super admin | Seeded via env-driven `SuperAdminSeeder`; credentials in `.env` lines 69–71 (`SUPER_ADMIN_*`) |
| Mail | **Mailpit** — binary at `~/.local/bin/mailpit`; start with `mailpit --smtp 127.0.0.1:1025 --listen 127.0.0.1:8025`; inbox UI at http://localhost:8025; `.env` has `MAIL_MAILER=smtp`, `MAIL_HOST=127.0.0.1`, `MAIL_PORT=1025`. (Fallback: set `MAIL_MAILER=log` → `storage/logs/laravel.log`) |
| Frontend | Built (`npm run build`); bootstrap-icons now bundled |

## 3. Audit findings → what was fixed (2026-07-24)

### Security (all fixed)
- **Hardcoded super-admin credential** in `SuperAdminSeeder` → now env-driven
  (`SUPER_ADMIN_EMAIL/PASSWORD/NAME`). ⚠️ OPEN ITEM: old password still in git
  history; rotate anywhere reused + consider `git filter-repo` scrub before sharing repo.
- **IDOR** on `POST /calculator/save/{id}` → ownership check (user or guest session)
  in `CalculatorController@save`; 5 regression tests.
- `role` removed from `User::$fillable` (privilege-escalation hardening).
- Email verification enabled (`User implements MustVerifyEmail`) — was silently dead.
- Throttles: `/subscribe` + `/subscribe/resubscribe` (5/min), `/comparison/export` (10/min).
- Subscribe endpoint no longer leaks who is subscribed (non-enumerating response).
- Unsubscribe fixed: token now survives verification (was nulled → links never worked),
  `hash_equals` comparison, unsubscribe link added to verification email.
- Dead `auth:sanctum` API scaffold removed (`routes/api.php` emptied, stub controller deleted).

### Day-count math (all fixed, all covered by known-answer unit tests)
- **30E/360**: was using last-day-of-month + a bogus February/EOM-checkbox rule →
  now pure ISDA 4.16(g): only 31→30 on both dates.
- **30E/360 ISDA**: added missing maturity exception (D2 = last-day-of-Feb NOT rolled
  when it's the termination date), driven by new `end_date_is_maturity` DTO field +
  form checkbox.
- **30/360 US**: `apply_eom_adjustment` checkbox now applies real NASD EOM
  end-of-February rules (was ignored entirely).
- **Actual/Actual**: multi-year split now caps segments at Jan 1 with integer day
  counts (was `endOfYear()` + Carbon 3 float `diffInDays` → `213.9999997` artifacts);
  honestly relabeled "Calendar-Year Split" (it is NOT ICMA; true ICMA needs coupon
  frequency — roadmap item).
- **Actual/Actual ISDA**: O(days) day-walk loop → O(years) segment arithmetic.
- **Actual/365 naming**: feature stamped `Actual/365` while everything else used
  `Actual/365 Fixed` → canonical name everywhere + data migration
  (`2026_07_24_000001_rename_actual_365_convention_type`) fixing stored rows.
- All Actual features: `(int)` + `startOfDay()` guards around Carbon 3 float diffs.

### Crash/broken-feature fixes
- Comparison non-AJAX path rendered nonexistent view → endpoint is JSON-only now.
- CSV export read undefined `days_range` stat key → computed `max_days - min_days`.
- Educational pages 404'd for names with `/` → route allows slashes AND slug URLs (see §5).
- Bootstrap Icons never loaded (every `bi bi-*` icon blank) → npm dep + import in module JS.
- Breeze tests aligned with app behavior (redirect to calculator, custom reset
  notification class, guarded `/`).

### Test suite (was ZERO module tests)
- `phpunit.xml`: added `Modules/*/tests` testsuite + module coverage source + dedicated
  test DB env.
- `Modules/DayCountCalculator/tests/Unit/DayCountConventionsTest.php` — 21 known-answer
  tests for all 9 conventions (vectors hand-derived from ISDA 2006 / NASD definitions).
- `tests/Feature/SaveCalculationAuthorizationTest.php` — IDOR regressions.
- `tests/Feature/SubscriptionFlowTest.php` — verify/unsubscribe flow.
- `tests/Feature/SeoInfrastructureTest.php` — slugs, sitemap URL health, JSON-LD,
  AdSense gating (see §5).

## 4. Monetization plan

Full plan in [MVP-PLAN.md](MVP-PLAN.md). Summary: niche passive asset, realistic
$100–1k/mo at 12 months. Streams in activation order: ads → affiliates → Pro tier
(bulk CSV/Excel, $29) → calculation API → B2B/Arabic-sukuk angle (biggest upside).
**Traffic (content + SEO) is the bottleneck and the critical path.**

User chose to build workstream **"Ad + SEO infrastructure" first — DONE (see §5).**
Remaining build-able workstreams, in the order proposed:
1. **Lead magnet + email loop** — auto-generated "Day Count Cheat Sheet" PDF (DomPDF,
   from the real engine) delivered after email verification; signup prompts on results.
2. **Pro features** — bulk CSV upload calc, real Excel export (maatwebsite/excel is
   installed but unused), white-label PDF; gate behind login now, paywall later
   (Paddle/Lemon Squeezy — needs user to create account).
3. **API v1** — token + metering, list on RapidAPI.

## 5. Ad + SEO infrastructure (completed this session)

- **Convention registry** moved to `Modules/DayCountCalculator/config/config.php`
  (`daycountcalculator.conventions`) — single source of truth with new `slug` and
  `related` fields. Consumers updated: both controllers, both FormRequests
  (`Rule::in` from config), educational view (related links), sitemap.
- **Slug URLs**: `/calculator/learn/{slug}` (e.g. `actual-365-fixed`) are canonical;
  legacy name URLs still resolve. CRITICAL bug fixed: sitemap previously listed slug
  URLs that all 404'd.
- **Sitemap**: URLs generated from config; `lastmod` = view `filemtime` (was always
  `now()`); test asserts every sitemap URL returns 200.
- **AdSense**: `ADSENSE_CLIENT` env (empty = ad-free). When set: Auto Ads script in
  `layouts/master.blade.php` head + `/ads.txt` route with the publisher ID.
- **Structured data**: FAQPage (3 Q&As from convention data) + BreadcrumbList JSON-LD
  on every educational page.

## 6. Open items / next actions

**Only the user can do:**
1. Rotate the old exposed password anywhere it was reused; decide on git-history scrub.
2. **Buy domain: `30360calculator.com` (DECIDED 2026-07-24**, was unregistered; buy at
   Namecheap; optional: also 30360calc.com as 301 redirect). Then VPS (DigitalOcean
   $6 droplet) and deploy (`APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`,
   `APP_URL=https://30360calculator.com`), submit sitemap to Search Console.
   Note: `MAIL_FROM_ADDRESS` already matches this domain.
3. AdSense account → put client ID in `.env`.
4. Payment account (Paddle/Lemon Squeezy) when Pro tier gets built.
5. Decide: commit current work? (Suggested: yes, as a clean series on `main`.)

**Next coding sessions (pick from §4):**
- Lead magnet PDF + email delivery loop.
- Pro: bulk CSV + real Excel export.
- Phase-1 launch items: privacy page, guest-IP retention cleanup job (PDPL), GA/ads
  consent, production mail provider config.

**Deferred tech debt** (documented in MVP-PLAN §6): duplicated `match` feature factory
(still in 2 controllers), dead frontend JS/vite configs, inline styles, `alert()`/reload
UX, `addslashes()` XSS-ish escaping in `saved-list.blade.php`, unused `spatie/laravel-sitemap`
dependency, educational pages' hardcoded worked examples.

## 7. How to resume

```bash
php artisan serve                 # app at http://127.0.0.1:8000
vendor/bin/phpunit                # must stay green (63 tests)
mysql -u root -p devlocal         # inspect app data (password: see .env)
```
Read §6, pick the next workstream, keep this file updated at the end of each session.
