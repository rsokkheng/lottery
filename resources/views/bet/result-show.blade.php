
<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="{{ asset('admin/dist/css/adminlte.min.css') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .datepicker-days {
                padding-left: 10px !important;
            }
            /*.active {*/
            /*    background-color: yellowgreen;*/
            /*}*/
        </style>
    </x-slot>
    <div class="row bg-white rounded-lg px-5 vh-100">
            <div class="col-2">
                <div class="row py-1 justify-content-end">
                    <div class="w-auto">
                        <div class="d-flex py-1 justify-content-start" >
                            <h6 class="text-bold text-center">Today: <span>{{\Carbon\Carbon::today()->format('d/m/Y')}}</span></h6>
                        </div>
                        <div class="d-flex py-1 justify-content-start">
                            <h6 class="text-bold">Result Date: <span>{{$data['date_show']}}</span></h6>
                        </div>
                        <div class="rounded border-2 border-primary">
                            <div id="dev_datepicker" data-date="{{$data['date_show']}}" data-date-format="dd/mm/yyyy"></div>
                        </div>
                    </div>
                    <a style="display: none" id="href_show_result" href="{{ route('bet.result-show') }}"></a>
                </div>
            </div>
            <div class="col-10">
                <input type="hidden" id="hidden_region" value="{{$data['region']['slug']??'mien-nam'}}">
                <ul class="nav nav-tabs text-primary text-bold">
                    <li class="nav-item">
                        <a class="nav-link cursor-pointer {{$data['type']===\App\Enums\HelperEnum::MienNamSlug->value ? 'active bg-secondary':''}}" onclick="goShowResult('{{\App\Enums\HelperEnum::MienNamSlug->value}}')">{{__('lang.mien-nam')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link cursor-pointer {{$data['type']===\App\Enums\HelperEnum::MienTrungSlug->value ? 'active bg-secondary':''}}" onclick="goShowResult('{{\App\Enums\HelperEnum::MienTrungSlug->value}}')">{{__('lang.mien-trung')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link cursor-pointer {{$data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value ? 'active bg-secondary':''}}" onclick="goShowResult('{{\App\Enums\HelperEnum::MienBacDienToanSlug->value}}')">{{__('lang.mien-bac')}}</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active">
                            <table class="table table-bordered rounded-lg text-center table-striped" style="width: 100%">
                                <thead class="bg-secondary">
                                <tr>
                                    <td class="text-white text-bold">
                                        {{ $data['date_show'] }}
                                        <input type="hidden" value="{{$data['date_show']}}" id="date_result" name="date_result" />
                                    </td>
                                    @foreach($data['form_result']['schedule'] as $val)
                                        <td class="text-white text-lg text-bold">{{ $val['province'] }}</td>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data['form_result']['result'] as $pKey => $prize)
                                    <tr>
                                        <td class="text-black text-bold w-25 text-lg">
                                            {{ $prize['prize_label'] }}
                                        </td>
                                        @foreach($prize['provinces'] as $province)
                                            <td class="text-primary">
                                                @if($data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value)
                                                    @php $c=1; $r=1; @endphp
                                                    @foreach($province['row_result'] as $key=>$row)
                                                            @if($c == 1)
                                                                <div class="row">  <!-- open tag dev for new row -->
                                                            @endif
                                                            <div class="col">
                                                                <h6 class="text-bold {{$row['class']??''}}">{{ $row['winning_number']??'****'}}</h6>
                                                            </div>
                                                            @if($c >= $row['col_count'])
                                                                </div> <!-- close tag dev row -->
                                                                @php $c=0; @endphp
                                                            @endif
                                                            @php $c++; @endphp
                                                    @endforeach
                                                @else
                                                    @foreach($province['row_result'] as $row)
                                                        <div class="p-1 d-flex justify-content-center">
                                                            <h6 class="text-bold {{$row['class']??''}}">{{ $row['winning_number']??'****'}}</h6>
                                                        </div>
                                                    @endforeach
                                                @endif

                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
    <!-- jQuery -->
    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
{{--    <!-- jQuery UI 1.11.4 -->--}}
    <script src="{{ asset('admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/datepicker/js-1-7-1/datepicker.min.js') }}"></script>

    <script src="{{ asset('admin/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/daterangepicker/daterangepicker.js') }}"></script>

<script>
    $(function(){

        $('#dev_datepicker').datepicker({
            format: 'dd/mm/yyyy', // Customize the date format as needed
            todayHighlight: true,
            endDate: '+0d',
            autoclose: true
        });
        $('#dev_datepicker').on('changeDate', function() {
            let getDate = $('#dev_datepicker').datepicker('getFormattedDate');
            window.location = $("#href_show_result").attr('href')+'?date='+getDate+'&region='+$("#hidden_region").val();
        });

    });

    function goShowResult(region){
        let getDate = $('#dev_datepicker').datepicker('getFormattedDate');
        window.location = $("#href_show_result").attr('href')+'?date='+getDate+'&region='+region;
    }
</script>
</x-app-layout>