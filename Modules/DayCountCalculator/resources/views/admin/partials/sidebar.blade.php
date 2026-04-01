<aside class="admin-sidebar">
    <div class="admin-sidebar-brand">
        <span style="color:#c9a227;font-weight:900;">30</span><span style="color:rgba(255,255,255,.3);">/</span><span style="font-weight:700;">360</span>
        <span class="ms-1" style="font-size:.7rem;color:rgba(255,255,255,.4);letter-spacing:.08em;text-transform:uppercase;vertical-align:middle;">Admin</span>
    </div>
    <nav class="admin-nav">
        <a class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a class="admin-nav-link {{ request()->routeIs('admin.calculations') ? 'active' : '' }}" href="{{ route('admin.calculations') }}">
            <i class="bi bi-calculator"></i> Calculations
        </a>
        <a class="admin-nav-link {{ request()->routeIs('admin.subscribers') ? 'active' : '' }}" href="{{ route('admin.subscribers') }}">
            <i class="bi bi-people"></i> Subscribers
        </a>
        <hr style="border-color:rgba(255,255,255,.08);margin:.75rem 1.25rem;">
        <a class="admin-nav-link" href="{{ route('admin.export', 'calculations') }}">
            <i class="bi bi-download"></i> Export Calcs
        </a>
        <a class="admin-nav-link" href="{{ route('admin.export', 'subscribers') }}">
            <i class="bi bi-download"></i> Export Subscribers
        </a>
        <hr style="border-color:rgba(255,255,255,.08);margin:.75rem 1.25rem;">
        <a class="admin-nav-link" href="{{ route('calculator.index') }}">
            <i class="bi bi-arrow-left-circle"></i> Back to App
        </a>
    </nav>
    <div class="admin-sidebar-user">
        <div class="d-flex align-items-center gap-2">
            <span style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#c9a227,#a07d18);color:#0f172a;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:.75rem;flex-shrink:0;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <div style="overflow:hidden;">
                <div style="font-size:.8rem;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                <div style="font-size:.7rem;color:rgba(255,255,255,.4);">{{ auth()->user()->role }}</div>
            </div>
        </div>
    </div>
</aside>
