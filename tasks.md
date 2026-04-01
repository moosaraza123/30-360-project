# UI Revamp — Remaining Tasks

## Design System
Navy dark: #0f172a | Navy mid: #1e3a5f | Gold: #c9a227 | Gold dark: #a07d18

## ✅ COMPLETED
1. `Modules/DayCountCalculator/resources/assets/sass/app.scss` — Bootstrap vars overridden (primary=navy, secondary=gold)
2. `Modules/DayCountCalculator/resources/assets/sass/components/_theme.scss` — NEW FILE: full utility classes (btn-gold, page-hero, navbar-navy, convention-card, stat-card, results-panel, table-navy, footer-navy, subscription pages, educational, saved cards, comparison table)
3. `Modules/DayCountCalculator/resources/views/layouts/master.blade.php` — Navy navbar with gold brand mark, gold Sign Up button, dark navy footer with subscribe form
4. `Modules/DayCountCalculator/resources/views/calculator/index.blade.php` — page-hero banner, convention-card classes, btn-gold Calculate button, convention-info-card with card-header-navy, tips-card with gold border, badge-gold on recent calcs

---

## ⏳ REMAINING (do in this order)

### 5. `Modules/DayCountCalculator/resources/views/calculator/partials/results.blade.php`
- Wrap in `results-panel` div (no card classes)
- Header: `results-header` div with `results-icon` + white h5
- Three metrics: `metric-box` divs with `metric-value` (gold) + `metric-label`
- Convention badge: `badge-gold`
- Accordion: `steps-accordion` class, `step-number` spans (gold circles)
- Applied steps: `step-applied` class with gold left border (replace green)
- Action buttons: `btn-outline-gold` for Print/Compare/Save/Share
- Modal header: `card-header-navy`, Save button: `btn-gold`
- Keep ALL JS logic unchanged

### 6. `Modules/DayCountCalculator/resources/views/calculator/history.blade.php`
- Replace `<div class="d-flex justify-content-between...mb-4">` header with `page-hero` section (pull outside container)
- "New Calculation" button: `btn-gold`
- Table `<thead>`: add `table-navy` class to table, replace `table-light` thead
- Days badge: `badge-gold` instead of `badge bg-primary`
- Empty state "Get Started" button: `btn-gold`
- Keep ALL JS/modal logic unchanged

### 7. `Modules/DayCountCalculator/resources/views/calculator/saved.blade.php`
- Page hero above container
- "New Calculation" button: `btn-gold`
- Tab active state: gold underline (add inline style or override nav-tabs)
- Favorite star icon: already has `text-warning`, change to `text-gold` class
- Empty state button: `btn-gold`
- Modal header: `card-header-navy`, "Save Changes" button: `btn-gold`
- Keep ALL JS unchanged

### 8. `Modules/DayCountCalculator/resources/views/calculator/partials/saved-list.blade.php`
- Card: replace `card shadow-sm h-100` with `card saved-calc-card h-100` (removes old hover style at bottom)
- Convention badge: `saved-card-convention` span (replaces `badge bg-primary`)
- Favorite button: add `favorite-star` class + `is-favorite` when `$saved->is_favorite`
- Metric boxes: `metric-mini` div with `mini-value` / `mini-label`
- Interest amount: replace `alert-success` with gold-tinted inline style
- Action buttons: `btn-outline-gold` for view/edit, keep `btn-outline-danger` for delete
- Remove the `<style>` block at bottom (handled by _theme.scss)

### 9. `Modules/DayCountCalculator/resources/views/comparison/index.blade.php`
- Add `page-hero` section before container
- Card title/desc: remove, now in hero
- "Compare All" button: `btn-gold btn-lg w-100`
- Interest optional section: same `rounded-3 p-3` style as calculator index
- Convention checkboxes: add `comparison-convention-check` class to inputs
- In `displayComparisonResults()` JS function:
  - Results card header: change `bg-success` → inline navy gradient style
  - Table `<thead>`: add navy gradient header (inline style or class `comparison-results-table`)
  - Stat boxes: replace `bg-light` with navy-stat inline styles (bg #f8fafc, border #e2e8f0, value in gold)
  - Chart card: remove `bg-light border-0`, use plain card
  - Export buttons: PDF=`btn-outline-danger`, Excel=`btn-outline-gold` (or keep as-is, minor)
- Keep ALL JS/fetch/chart logic unchanged

### 10. `Modules/DayCountCalculator/resources/views/admin/dashboard.blade.php`
- Page header: `page-hero` (or inline navy hero div)
- 4 stat cards: replace with `stat-card` structure:
  ```html
  <div class="stat-card">
    <div class="stat-card-header"><i class="bi bi-calculator stat-icon"></i> LABEL</div>
    <div class="stat-card-body"><div class="stat-number">{{ value }}</div><div class="stat-label">description</div></div>
  </div>
  ```
- Chart card headers: replace `bg-white` with `card-header-navy`
- Popular conventions badge: `badge-gold`
- "View All" buttons: `btn-outline-gold btn-sm`
- Quick Actions buttons: `btn-gold` for Export, `btn-outline-gold` for others
- Keep ALL Chart.js scripts unchanged (just update chart colors below)
- Chart colors: change blue `rgb(59,130,246)` → `#c9a227` (gold), green → `#10b981` (keep)

### 11. `Modules/DayCountCalculator/resources/views/admin/calculations.blade.php`
- Page header: navy hero inline div (no full page-hero, just a styled header bar)
- "Export Data" button: `btn-gold`
- "Back" button: `btn-outline-gold`  
- 4 stat cards: `stat-card` structure (same as admin dashboard)
- Chart card headers: `card-header-navy`
- Insights card: change `border-info` → gold left border, icon gold
- Table thead: `table-navy` class
- Rank badges: `badge-gold`
- Keep ALL Chart.js scripts unchanged

### 12. `Modules/DayCountCalculator/resources/views/admin/subscribers.blade.php`
- Same pattern as calculations.blade.php
- 6 stat cards → `stat-card` structure
- "Export Data" button: `btn-gold`
- Chart headers: `card-header-navy`
- Subscriber table thead: `table-navy`
- Status badges: keep green/warning/secondary — they're semantic
- Insights card: gold left border
- Keep ALL Chart.js scripts unchanged

### 13. Subscription pages (all 5 files):
- `subscription/verify-success.blade.php`: icon → gold (`icon-gold` class from _theme), border-success → gold border, "Start Calculating" → `btn-gold`, "Try Comparison" → `btn-outline-gold`
- `subscription/verify-failed.blade.php`: error icon stays red, "Back" btn → `btn-outline-gold`
- `subscription/already-verified.blade.php`: info icon → gold, btn → `btn-gold`
- `subscription/unsubscribe-success.blade.php`: muted icon, resubscribe btn → `btn-gold`
- `subscription/unsubscribe-failed.blade.php`: error stays red, "Back" btn → `btn-outline-gold`
- All: wrap in `status-page` div, replace old Bootstrap card structure with cleaner centered layout from _theme.scss

### 14. `Modules/DayCountCalculator/resources/views/educational/convention.blade.php`
- Add `educational-page` class to main container
- Breadcrumb: active item gets `text-gold fw-semibold`
- H1: `convention-hero-heading` class
- Alias alert: replace `alert-info` with `alias-badge` span inline
- Formula card: replace `bg-light p-3 rounded` code block with `formula-display` div
- Use cases badges: `use-case-badge` spans instead of plain `<li>` in card
- Example calc card header: change `bg-success` → navy gradient (`card-header-navy`)
- "Try it yourself" alert: replace `alert-info` with gold-tinted box
- Quick Action sidebar: "Use Calculator" → `btn-gold`, "Compare" → `btn-outline-gold`
- Key Points card: `tips-card` class
- Related conventions list-group items: gold hover (add `list-group-item-action` style override)

---

## LAYER 4 — Main App (Tailwind/Breeze pages)

### 15. `resources/views/layouts/navigation.blade.php`
- Full replace: navy gradient nav (matching module navbar style but using Tailwind/inline styles)
- Logo: same "30/360 Calculator" brand mark in gold
- Links: Dashboard → Calculator (route calculator.index), white text, gold on active
- User dropdown: white trigger text, navy dropdown panel
- Mobile menu: navy background
- Keep Alpine.js `x-data="{ open: false }"` logic

### 16. `resources/views/layouts/app.blade.php`
- Replace Figtree font with Inter
- Add `<style>` block with CSS custom properties (--navy-dark, --gold, etc.)
- Body background: keep gray-100 (it's #f4f4f5, close enough)

### 17. `resources/views/dashboard.blade.php`
- Full replace: remove "You're logged in!" card
- Navy hero greeting: "Welcome back, {{ Auth::user()->name }}" (name in gold)
- 3 quick-action cards below: Calculator / Compare / History
  - Each: white card, navy icon circle (SVG icon), h6 title, muted description, gold "Open →" link
- Note: dashboard route is accessible to auth+verified users; calculator.index has no verified requirement

### 18. `resources/views/components/primary-button.blade.php`
- Change class from `bg-gray-800` palette → gold gradient
- `style` attribute or update class: `bg-gradient-to-br from-yellow-500 to-yellow-700 text-slate-900 font-bold`
- Or simpler: add inline style override in the merge

### 19. `resources/views/profile/edit.blade.php` (read first — likely uses x-app-layout)
- Section headings: navy color class
- Already uses white cards with Tailwind — mostly fine as-is
- Primary buttons will auto-update from task 18

---

## Notes for next session
- All JS logic must be preserved exactly as-is — only CSS classes and HTML structure for visual elements
- The SCSS `_theme.scss` already defines all needed classes — reference it when unsure what class to use
- Run `npm run build` or `npm run dev` after changes to recompile SCSS
- Module views use Bootstrap classes + custom theme classes; main app views use Tailwind

