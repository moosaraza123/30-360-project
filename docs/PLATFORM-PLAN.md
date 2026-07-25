# Hisabi.com — Platform Transformation & Redesign Plan

*Created 2026-07-24 · Brand: **Hisabi** (حسابي — "my calculation") · Markets: UAE + KSA · Languages: English + Arabic (RTL)*

## Vision

One trusted bilingual finance-calculators platform for the Gulf. Every calculator:
accurate, source-cited, instant, beautiful on mobile. The 30/360 day-count suite becomes
the "Professional" section; consumer calculators (gratuity, VAT, zakat, salary, loans)
drive the traffic.

---

## Phase A — Design system & foundation (Week 1)

**A1. Consolidate to ONE CSS framework: Tailwind.**
Currently the module runs Bootstrap 5 + hundreds of inline styles while the base app runs
Tailwind — two frameworks shipped, no system. Tailwind wins because: RTL via logical
properties (`ms-`/`me-`), tree-shaking, dark-mode support, and modern component patterns.
Bootstrap JS (dropdowns/modals) replaced by Alpine.js (already installed).

**A2. Design tokens (single source, `tailwind.config.js`):**
- **Palette — "trust" direction:** ink navy `#0B1526` (text/headers), surface white +
  `#F7F9FB`, primary **deep teal-emerald** `#0E7C66` (actions, focus — reads "Gulf +
  finance + Islamic-finance adjacent"), accent gold `#C9A227` used *sparingly* (highlights,
  brand mark only), semantic green/red for results/errors. Light theme primary; dark later.
- **Type:** IBM Plex Sans (EN) + **IBM Plex Sans Arabic** (AR) — one type family designed
  for both scripts; numeric tables use tabular figures. Self-host via @fontsource (no
  external CDN dependency).
- **Shape:** 12px radius cards, soft single-layer shadows, 1px hairline borders
  (`#E5EAF0`), generous whitespace. No gradients-everywhere, no glassmorphism.

**A3. Trust layer (what makes it feel credible — this is content + UI):**
- Every calculator page: "Based on <official source>" citation block (MOHRE Art. 51,
  ZATCA VAT law, ISDA 2006), **last-reviewed date**, and a methodology accordion.
- About page, privacy policy, contact — real pages, linked in footer.
- Results shown with step-by-step breakdown (we already have this engine pattern) — the
  #1 differentiator vs blog-embedded calculators.
- No ads above the calculator; clean sponsorship-free result area.

**A4. i18n + RTL scaffolding:**
- Laravel localization: `lang/en/`, `lang/ar/` JSON+PHP; locale routes `/{locale?}` with
  `ar` → `dir="rtl"` on `<html>`, mirrored layout via Tailwind logical utilities.
- `hreflang` alternates in head + localized sitemap entries.
- Language switcher in nav (persists via session/cookie).
- Numbers: Western digits both locales (GCC convention for finance), currency labels
  localized (AED/د.إ, SAR/ر.س).

**A5. New app shell:** navbar (logo, calculator categories, language switch, auth),
platform homepage (hero + calculator category cards + trust strip), footer (sources,
legal, newsletter). Kills the stock Laravel welcome page.

## Phase B — Redesign existing pages on the new system (Week 2)

Rebuild in Tailwind, removing all inline styles and `alert()`/`location.reload()` UX:
1. Day-count calculator page (form → inline animated result panel, no reload)
2. Comparison tool (results table + chart restyled)
3. Educational pages (readable article layout — these are the SEO pages)
4. History/Saved (cards, inline actions, toasts instead of alert())
5. Auth pages (Breeze forms on brand)
6. Admin: minimal reskin only (internal tool — lowest priority)
7. Transactional emails: already navy/gold — recolor to new palette (small)

**Definition of done per page:** mobile-first, RTL-verified, Lighthouse ≥90
performance/accessibility, zero inline styles, zero Bootstrap classes.

## Phase C — New calculator modules (Weeks 3–6)

Each module = Feature class (calculation) + config entry + bilingual page + FAQ JSON-LD
+ unit tests with official worked examples. Order:

| # | Module | Notes | Target |
|---|---|---|---|
| C1 | **Gratuity UAE** (MOHRE, limited/unlimited pre-2022 + new labor law) | Flagship; EN+AR | W3 |
| C2 | **Gratuity KSA** (end-of-service per Saudi labor law Art. 84/85) | Shares UI with C1 | W4 |
| C3 | **VAT UAE (5%) + KSA (15%)** add/remove VAT | Trivial math, quick SEO win | W4 |
| C4 | **Zakat** (nisab via live gold/silver API, cash/gold/business assets) | **HARD DEADLINE: live by Dec 2026** (Ramadan ~Feb 2027) | W5–6 |
| C5 | Salary breakdown UAE/KSA + murabaha/loan calculator | Feeds lead-gen | Post-launch |

## Phase D — Launch (as soon as B done — don't wait for all of C)

1. **Buy hisabi.com** (RISK: deferring purchase — recheck availability weekly)
2. Deploy per [DEPLOY.md](DEPLOY.md) (update APP_URL/MAIL_FROM to hisabi.com)
3. Search Console (EN+AR sitemaps), Bing, uptime monitor
4. Privacy/terms live (AdSense prerequisite), guest-IP retention job
5. Content cadence starts: 2 posts/week alternating EN/AR, gratuity cluster first

## Sequencing summary

Week 1: A (system+shell+i18n) → Week 2: B (redesign) → **Deploy end of W2 with day-count
only** → W3–6: C modules shipping onto a live, indexing domain.

## Open decisions / user actions
- [ ] Buy hisabi.com (recommended NOW; user deferred)
- [ ] Keyword Planner validation run (list in session notes / PROGRESS)
- [ ] Approve design direction (mockup to be produced before Phase B starts)
- [ ] Arabic copy review: AI-drafted → needs native-speaker pass before indexing AR pages
