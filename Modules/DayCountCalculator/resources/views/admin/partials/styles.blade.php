<style>
/* ──────────────────────────── Admin Shell Layout ──────────────────────── */
.admin-shell { display:flex; min-height:calc(100vh - 60px); background:#f1f5f9; }

.admin-sidebar {
    width:220px; flex-shrink:0; background:#0f172a;
    display:flex; flex-direction:column; padding:1.5rem 0 1rem;
    position:sticky; top:0; height:100vh; overflow-y:auto;
}
.admin-sidebar-brand { padding:0 1.25rem 1.25rem; font-size:1.1rem; font-weight:700; color:#fff; border-bottom:1px solid rgba(255,255,255,.06); margin-bottom:.75rem; }
.admin-nav { flex-grow:1; padding:0 .75rem; }
.admin-nav-link { display:flex; align-items:center; gap:.625rem; padding:.55rem .75rem; border-radius:.5rem; color:rgba(255,255,255,.55); text-decoration:none; font-size:.85rem; font-weight:500; margin-bottom:.125rem; transition:background .15s,color .15s; }
.admin-nav-link:hover { background:rgba(255,255,255,.06); color:rgba(255,255,255,.85); }
.admin-nav-link.active { background:rgba(201,162,39,.15); color:#c9a227; }
.admin-nav-link i { font-size:.95rem; width:1rem; text-align:center; }
.admin-sidebar-user { padding:1rem 1.25rem 0; border-top:1px solid rgba(255,255,255,.06); }

/* ──────────────────────────── Main Content ─────────────────────────────── */
.admin-main { flex:1; padding:1.75rem 1.75rem 3rem; overflow-x:hidden; }
.admin-page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.5rem; }
.admin-page-title { font-size:1.4rem; font-weight:800; color:#0f172a; margin:0; }
.admin-page-subtitle { font-size:.8rem; color:#94a3b8; margin:0; }

/* ──────────────────────────── KPI Cards ────────────────────────────────── */
.kpi-card { background:#fff; border-radius:.875rem; padding:1.125rem 1.25rem; display:flex; align-items:center; gap:1rem; box-shadow:0 1px 3px rgba(15,23,42,.06); }
.kpi-icon { width:48px; height:48px; flex-shrink:0; border-radius:.75rem; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
.kpi-body { min-width:0; }
.kpi-label { font-size:.72rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.2rem; }
.kpi-value { font-size:1.65rem; font-weight:800; color:#0f172a; line-height:1; margin-bottom:.3rem; }
.kpi-sub { font-size:.75rem; }
.kpi-trend-up { color:#10b981; font-weight:600; }
.kpi-trend-down { color:#ef4444; font-weight:600; }

/* ──────────────────────────── Stat Pills ───────────────────────────────── */
.stat-pill { background:#fff; border-radius:.75rem; padding:.875rem 1rem; box-shadow:0 1px 3px rgba(15,23,42,.05); text-align:center; }
.stat-pill-label { font-size:.7rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.25rem; }
.stat-pill-value { font-size:1.3rem; font-weight:800; color:#0f172a; }

/* ──────────────────────────── Admin Cards ──────────────────────────────── */
.admin-card { background:#fff; border-radius:.875rem; box-shadow:0 1px 3px rgba(15,23,42,.06); overflow:hidden; }
.admin-card-header { padding:.875rem 1.25rem; border-bottom:1px solid #f1f5f9; font-size:.85rem; font-weight:700; color:#0f172a; display:flex; align-items:center; justify-content:space-between; }
.admin-card-body { padding:1.125rem 1.25rem; }
.admin-link-btn { font-size:.75rem; font-weight:600; color:#c9a227; text-decoration:none; }
.admin-link-btn:hover { color:#a07d18; }

/* ──────────────────────────── Convention List ──────────────────────────── */
.convention-row { display:flex; align-items:center; gap:.75rem; padding:.6rem 1.25rem; border-bottom:1px solid #f8fafc; }
.convention-row:last-child { border-bottom:none; }
.convention-rank { width:20px; flex-shrink:0; font-size:.72rem; font-weight:800; color:#cbd5e1; text-align:center; }
.convention-name { font-size:.8rem; font-weight:600; color:#334155; }
.convention-count { font-size:.78rem; font-weight:700; color:#c9a227; }
.convention-bar-track { height:4px; background:#f1f5f9; border-radius:2px; }
.convention-bar-fill { height:4px; background:linear-gradient(90deg,#c9a227,#a07d18); border-radius:2px; transition:width .6s ease; }

/* ──────────────────────────── Admin Table ──────────────────────────────── */
.admin-table { width:100%; border-collapse:collapse; }
.admin-table td { padding:.6rem 1.25rem; font-size:.83rem; border-bottom:1px solid #f8fafc; color:#475569; }
.admin-table tr:last-child td { border-bottom:none; }
.admin-table td:last-child { text-align:right; color:#0f172a; font-weight:600; }
.badge-stat { display:inline-block; background:rgba(15,23,42,.06); color:#0f172a; font-size:.75rem; font-weight:700; padding:.15rem .5rem; border-radius:.375rem; }
.badge-stat.badge-success { background:rgba(16,185,129,.1); color:#059669; }
.badge-stat.badge-warn { background:rgba(245,158,11,.1); color:#d97706; }
</style>
