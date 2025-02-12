<x-admin>
{{--    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">--}}
    @section('title',__('lang.menu.lottery-result'))
    @include('admin.lottery-result.navbar', ['data' => $data])
    <style>
        .datepicker-days {
            padding-top: 15px !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

    </style>
    <link href="{{ asset('admin/plugins/datepicker/css/bootstrap-datepicker-1-7-1.min.css') }}" rel="stylesheet"/>

    <!-- Tab panes -->
    <div class="tab-content">
        <div id="element-id-{{\App\Enums\HelperEnum::MienNamSlug->value }}" class="container tab-pane active"><br>
            <div class="card">
                <div class="card-header">
                    @if($data['type']===\App\Enums\HelperEnum::MienNamSlug->value)<h3 class="card-title py-2">Bet result of {{__('lang.mien-nam')}}</h3>@endif
                    @if($data['type']===\App\Enums\HelperEnum::MienTrungSlug->value)<h3 class="card-title py-2">Bet result of {{__('lang.mien-trung')}}</h3>@endif
                    @if($data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value)<h3 class="card-title py-2">Bet result of {{__('lang.mien-bac')}}</h3>@endif

                    <input type="hidden" id="region_value" value="{{$data['type']??''}}"/>
                    <div class="card-tools">
                        <a id="btn-create-result" href="{{ route($data['url']['create']) }}" class="btn btn-md btn-primary">Create</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex w-100 align-items-center">
                        <label class="text-nowrap pr-4">Show by</label>
                        <input type="text" id="selectDateResult" data-date-format="dd/mm/yyyy"  class="form-control" placeholder="Select date">
                    </div>

                </div>
            </div>
        </div>
    </div>


    @section('js')
{{--        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>--}}
{{--        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>--}}
{{--        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>--}}
        <script>
            $(function(){
                $('#selectDateResult').datepicker({
                    format: 'dd/mm/yyyy', // Customize the date format as needed
                    autoclose: true,
                    todayHighlight: true,
                    endDate: '+0d',
                });
                $("#selectDateResult").on('change', function(){
                    window.location = $("#btn-create-result").attr('href')+'?date='+$(this).val();
                })
            });
        </script>

    @endsection
</x-admin>
