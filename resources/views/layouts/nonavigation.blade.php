<nav style="background:#374151; padding:.75rem 1.25rem; display:flex; align-items:center; gap:1rem; box-shadow:0 2px 8px rgba(0,0,0,.2);">
    <img src="{{ asset('images/logo2888_back.png') }}" alt="Logo" style="height:28px; width:auto;">
    <span style="color:#FFD700; font-weight:800; font-size:.88rem;">Lottery2888</span>
    <div style="flex:1;"></div>
    <div style="background:#fee2e2; color:#991b1b; border-radius:8px; padding:5px 14px; font-size:.8rem; font-weight:700;">
        &#9888; Unknown currency — please contact admin
    </div>
    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
        @csrf
        <button type="submit" style="background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2); border-radius:7px; color:#fff; padding:5px 12px; font-size:.78rem; font-weight:700; cursor:pointer;">
            Log Out
        </button>
    </form>
</nav>
