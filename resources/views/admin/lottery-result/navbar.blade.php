<style>
/* ── Vietnam Lotto theme ── */
:root { --vn-red: #DA251D; --vn-gold: #FFCD00; }

.vn-navbar-brand {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    font-weight: 700;
    color: var(--vn-red);
    letter-spacing: .3px;
    padding: 8px 14px 8px 0;
    border-right: 2px solid #f0d0d0;
    margin-right: 4px;
    white-space: nowrap;
}
.vn-navbar-brand .vn-flag {
    font-size: 20px;
    line-height: 1;
}

.vn-nav-tabs { border-bottom: 2px solid var(--vn-red) !important; }
.vn-nav-tabs .nav-link { color: #555; border: none; border-radius: 0; padding: 8px 16px; font-size: 13px; }
.vn-nav-tabs .nav-link:hover { color: var(--vn-red); background: #fff5f5; }
.vn-nav-tabs .nav-link.active {
    color: #fff !important;
    background: var(--vn-red) !important;
    border: none;
    font-weight: 600;
}
.vn-nav-tabs .nav-item.ms-auto .nav-link.active {
    background: #555 !important;
}
</style>

<ul class="nav vn-nav-tabs d-flex align-items-center flex-wrap">
    <li class="nav-item">
        <span class="vn-navbar-brand">
            <span class="vn-flag">🇻🇳</span> Lotto Vietnam
        </span>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $data['type']===\App\Enums\HelperEnum::MienNamSlug->value && !(isset($data['url']['check'])) ? 'active' : '' }}"
           href="{{ route('admin.result.index-mien-nam') }}">{{ __('lang.mien-nam') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $data['type']===\App\Enums\HelperEnum::MienTrungSlug->value && !(isset($data['url']['check'])) ? 'active' : '' }}"
           href="{{ route('admin.result.index-mien-trung') }}">{{ __('lang.mien-trung') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value && !(isset($data['url']['check'])) ? 'active' : '' }}"
           href="{{ route('admin.result.index-mien-bac') }}">{{ __('lang.mien-bac') }}</a>
    </li>
    <li class="nav-item ms-auto">
        <a class="nav-link {{ (isset($data['url']['check']) && $data['url']['check'] === 'admin.result.check-result') ? 'active' : '' }}"
           href="{{ route('admin.result.check-result') }}">Check Result</a>
    </li>
</ul>
