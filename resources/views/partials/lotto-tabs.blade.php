{{--
    Lotto system switcher tabs — shown to admin/master only.
    Usage: @include('partials.lotto-tabs', ['routeKey' => 'daily-manager', 'params' => ['date' => $date, 'com_id' => $company_id]])
    routeKey: 'daily-manager' | 'monthly-tracking'
--}}
@if($isAdmin ?? false)
@php
    $sys      = session('bet_system', 'vietnam');   // 'vietnam' | 'khmer'
    $cur      = strtolower(session('currency', 'VND')); // 'vnd' | 'usd'
    $activeKey = $sys . '_' . $cur;

    $rk = $routeKey ?? 'daily-manager';
    $p  = $params ?? [];

    $tabs = [
        [
            'key'    => 'vietnam_vnd',
            'label'  => '🇻🇳 Vietnam · VND',
            'route'  => 'reports.' . $rk,
            'bg'     => '#b45309',
            'border' => '#92400e',
        ],
        [
            'key'    => 'vietnam_usd',
            'label'  => '🇻🇳 Vietnam · USD',
            'route'  => 'bet-usd.reports.' . $rk,
            'bg'     => '#059669',
            'border' => '#047857',
        ],
        [
            'key'    => 'khmer_vnd',
            'label'  => '🇰🇭 Cambodia · VND',
            'route'  => 'bet-kh-vnd.reports.' . $rk,
            'bg'     => '#92400e',
            'border' => '#78350f',
        ],
        [
            'key'    => 'khmer_usd',
            'label'  => '🇰🇭 Cambodia · USD',
            'route'  => 'bet-kh-usd.reports.' . $rk,
            'bg'     => '#7c3aed',
            'border' => '#6d28d9',
        ],
    ];
@endphp
<div style="display:flex;gap:6px;flex-wrap:wrap;padding:0 0 14px;">
    @foreach($tabs as $tab)
        @php
            $isActive = $activeKey === $tab['key'];
            $url      = route($tab['route'], $p);
        @endphp
        <a href="{{ $url }}"
           style="display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:20px;
                  font-size:.78rem;font-weight:700;text-decoration:none;letter-spacing:.02em;
                  white-space:nowrap;transition:opacity .15s;
                  background:{{ $isActive ? $tab['bg'] : 'transparent' }};
                  color:{{ $isActive ? '#fff' : $tab['bg'] }};
                  border:2px solid {{ $tab['border'] }};
                  {{ $isActive ? '' : 'opacity:.8;' }}">
            {{ $tab['label'] }}
            @if($isActive)
                <span style="display:inline-block;width:6px;height:6px;background:#fff;border-radius:50%;opacity:.8;"></span>
            @endif
        </a>
    @endforeach
</div>
@endif
