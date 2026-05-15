<x-admin>
    @section('title',__('lang.menu.lottery-result'))
    @include('admin.lottery-kh-result.navbar', ['data' => $data])
    <style>
        .datepicker-days {
            padding-top: 15px !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
    </style>
    <link href="{{ asset('admin/plugins/datepicker/css/bootstrap-datepicker-1-7-1.min.css') }}" rel="stylesheet"/>

    <div class="tab-content">
        <div class="container tab-pane active"><br>
            <div class="card">
                <div class="card-header">
                    @if($data['type']===\App\Enums\HelperEnum::MienNamSlug->value)<h3 class="card-title py-2">Bet result of {{__('lang.mien-nam')}} (VND)</h3>@endif
                    @if($data['type']===\App\Enums\HelperEnum::MienTrungSlug->value)<h3 class="card-title py-2">Bet result of {{__('lang.mien-trung')}} (VND)</h3>@endif
                    @if($data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value)<h3 class="card-title py-2">Bet result of {{__('lang.mien-bac')}} (VND)</h3>@endif

                    <input type="hidden" id="region_value" value="{{$data['type']??''}}"/>
                    <div class="card-tools">
                        <a id="btn-create-result" href="{{ route($data['url']['create']) }}" class="btn btn-md btn-primary">Create</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex w-100 align-items-center">
                        <label class="text-nowrap pr-4">Show by</label>
                        <input type="text" id="selectDateResult" data-date-format="dd/mm/yyyy" class="form-control" placeholder="Select date">
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('js')
        <script>
            $(function(){
                $('#selectDateResult').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    endDate: '+0d',
                });
                $("#selectDateResult").on('change', function(){
                    window.location = $("#btn-create-result").attr('href')+'?date='+$(this).val();
                });
            });
        </script>
    @endsection
</x-admin>
