<style>
/* ── Cambodia Lotto theme ── */
:root { --kh-blue: #032EA1; --kh-red: #E00025; }

.kh-navbar-brand {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    font-weight: 700;
    color: var(--kh-blue);
    letter-spacing: .3px;
    padding: 8px 14px 8px 0;
    border-right: 2px solid #c5cfed;
    margin-right: 4px;
    white-space: nowrap;
}
.kh-navbar-brand .kh-flag {
    font-size: 20px;
    line-height: 1;
}

.kh-nav-tabs { border-bottom: 2px solid var(--kh-blue) !important; }
.kh-nav-tabs .nav-link { color: #555; border: none; border-radius: 0; padding: 8px 16px; font-size: 13px; }
.kh-nav-tabs .nav-link:hover { color: var(--kh-blue); background: #f0f3ff; }
.kh-nav-tabs .nav-link.active {
    color: #fff !important;
    background: var(--kh-blue) !important;
    border: none;
    font-weight: 600;
}
.kh-nav-tabs .nav-item:last-child .nav-link.active {
    background: #555 !important;
}
</style>

<ul class="nav kh-nav-tabs d-flex align-items-center flex-wrap">
    <li class="nav-item">
        <span class="kh-navbar-brand">
            <span class="kh-flag">🇰🇭</span> Lotto Cambodia
        </span>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $data['type']===\App\Enums\HelperEnum::MienNamSlug->value && !($data['url']['check']??false) ? 'active' : '' }}"
           href="{{ route('admin.result-kh.index-mien-nam') }}">{{ __('lang.mien-nam') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $data['type']===\App\Enums\HelperEnum::MienTrungSlug->value && !($data['url']['check']??false) ? 'active' : '' }}"
           href="{{ route('admin.result-kh.index-mien-trung') }}">{{ __('lang.mien-trung') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value && !($data['url']['check']??false) ? 'active' : '' }}"
           href="{{ route('admin.result-kh.index-mien-bac') }}">{{ __('lang.mien-bac') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ isset($data['url']['check']) ? 'active' : '' }}"
           href="{{ route('admin.result-kh.check-result') }}">Check Result</a>
    </li>
</ul>
