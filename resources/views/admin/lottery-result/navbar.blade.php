<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{$data['type']===\App\Enums\HelperEnum::MienNamSlug->value ? 'active':''}}" href="{{ route('admin.result.index-mien-nam') }}">{{__('lang.mien-nam')}}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$data['type']===\App\Enums\HelperEnum::MienTrungSlug->value ? 'active':''}}" href="{{ route('admin.result.index-mien-trung') }}">{{__('lang.mien-trung')}}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value ? 'active':''}}" href="{{ route('admin.result.index-mien-bac') }}">{{__('lang.mien-bac')}}</a>
    </li>
</ul>