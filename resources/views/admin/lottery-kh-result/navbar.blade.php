<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{$data['type']===\App\Enums\HelperEnum::MienNamSlug->value && !($data['url']['check']??false) ? 'active':''}}" href="{{ route('admin.result-kh.index-mien-nam') }}">{{__('lang.mien-nam')}}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$data['type']===\App\Enums\HelperEnum::MienTrungSlug->value && !($data['url']['check']??false) ? 'active':''}}" href="{{ route('admin.result-kh.index-mien-trung') }}">{{__('lang.mien-trung')}}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value && !($data['url']['check']??false) ? 'active':''}}" href="{{ route('admin.result-kh.index-mien-bac') }}">{{__('lang.mien-bac')}}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ isset($data['url']['check']) ? 'active' : '' }}" href="{{ route('admin.result-kh.check-result') }}">Check Result</a>
    </li>
</ul>
