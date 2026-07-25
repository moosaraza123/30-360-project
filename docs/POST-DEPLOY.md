# Hisabi — Post-Deployment Playbook

*Prereq: site live at hisabi.com per [DEPLOY.md](DEPLOY.md). Owner tasks marked 👤, Claude tasks 🤖.*

## Day 0–2 — Verify & register (2–3 hours total)

1. 🤖 Smoke-test production: every EN+AR page 200, forms calculate, register→verify
   email arrives (Resend), SSL valid, `APP_DEBUG=false` confirmed via `php artisan about`.
2. 👤🤖 **Google Search Console**: verify domain property (DNS TXT), submit
   `https://hisabi.com/sitemap.xml`. Same for **Bing Webmaster** (imports from GSC, 5 min).
3. 🤖 Uptime monitor (UptimeRobot free) on `/` and `/zakat-calculator`; alerts to your email.
4. 🤖 Analytics: GA4 property (or Plausible) + goals: calculation performed, email signup.
5. 👤 Rotate: confirm the old seeder password isn't reused anywhere; server SSH is key-only.

## Week 1–2 — Eligibility & baseline

6. 🤖 Privacy policy + Terms pages (AdSense hard requirement) + cookie notice for GA.
7. 🤖 Guest-IP retention cleanup job (PDPL hygiene) + queue the verification emails.
8. 👤 Arabic native-speaker review pass of all AR pages (fiver/colleague; ~2 hours of
   their time). Blocks serious AR indexing until done.
9. 🤖 Request indexing in GSC for the 5 priority pages (EN+AR) once AR copy approved.

## Week 2–8 — The content engine (this decides everything)

10. 👤(+🤖 drafts) **2 posts/week**, gratuity cluster first — target long-tails:
    - "gratuity calculation UAE resignation" / "unlimited contract gratuity"
    - "end of service Saudi Arabia calculator example" (AR versions of each)
    - Every article embeds the live calculator + worked example from the real engine.
11. 🤖 Internal linking: every article → calculator → related articles.
12. 👤 LinkedIn distribution: 1–2 posts/week (bilingual finance content performs well,
    near-zero competition). This is also the white-label lead channel.
13. 👤 At ~500–1,000 sessions/month: **apply for AdSense**. On approval:
    set `ADSENSE_CLIENT` in `.env` → ads live, zero code changes.

## Month 2–4 — Monetization layers

14. 👤 Affiliate applications: UAE/KSA loan & credit comparison programs, CFA/FRM prep
    (for the professional section). 🤖 placement slots in content once approved.
15. 🤖 **Zakat page hard deadline: fully live + indexed by early December 2026**
    (Ramadan ~Feb 2027 = 10–20× zakat search volume). Wire live gold-price API
    (metals API + daily cache) before the spike.
16. 🤖 Salary/WPS + loan calculator modules (lead-gen surface).

## Ongoing cadence

- **Weekly (15 min)**: GSC queries/impressions, uptime, error log scan (`storage/logs`).
- **Monthly (1 hr)**: KPI review — organic sessions, calc completions, email signups
  (target ≥2% of sessions), RPM once ads live; update zakat gold defaults if API not
  yet wired; `composer update` security patches; DB backup restore-test quarterly.
- **Quarterly**: law-review pass — confirm MOHRE/HRSD/ZATCA rules unchanged; update
  the `reviewed` dates in module config (this is a real E-E-A-T signal).

## Decision gates (from MVP-PLAN, restated)

- Month 6: <2k sessions/mo despite consistent content → cut content spend to
  maintenance, keep site as slow-burn asset, revisit angle.
- Traffic >5k/mo: green-light Pro tier build (bulk CSV, real Excel, Paddle checkout).
- First white-label inquiry: prioritize embed/widget productization.

## KPI targets (honest)

| Milestone | Sessions/mo | Email list | Revenue/mo |
|---|---|---|---|
| Month 3 | 300–1,000 | 50+ | ~$0 |
| Month 6 | 2,000–5,000 | 300+ | $50–200 |
| Month 12 | 8,000–20,000 | 1,500+ | $400–1,000 |
| Month 18 | 20,000–40,000 | 4,000+ | $800–1,800+ |
