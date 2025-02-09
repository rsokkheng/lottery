
<x-app-layout>
{{--    <x-slot name="header">--}}
{{--        <link rel="stylesheet" href="{{ asset('admin/dist/css/adminlte.min.css') }}">--}}
{{--        @vite(['resources/css/app.css', 'resources/js/app.js'])--}}
        <style>
            .datepicker-days {
                padding-left: 10px !important;
            }
            /*.active {*/
            /*    background-color: yellowgreen;*/
            /*}*/
            .active-bar-tap {
                 background-color: gray !important;
                color: white !important;
            }
        </style>
{{--    </x-slot>--}}
    <div class="flex bg-white rounded-lg px-5 py-5">
            <div class="flex w-auto">
                    <div class="flex-column">
                        <a class="hidden" id="href_show_result" href="{{ route('bet.result-show') }}"></a>
                        <div class="flex py-1 justify-start">
                            <h6 class="font-bold text-center">Today: <span>{{\Carbon\Carbon::today()->format('d/m/Y')}}</span></h6>
                        </div>
                        <div class="flex py-1 justify-start">
                            <h6 class="font-bold">Result Date: <span>{{$data['date_show']}}</span></h6>
                        </div>
                        <div class="flex mt-2 rounded border-2 border-blue-700">
                            <div id="dev_datepicker" data-date="{{$data['date_show']}}" data-date-format="dd/mm/yyyy"></div>
                        </div>
                    </div>
            </div>
            <div class="flex w-full  px-2">
                <input type="hidden" id="hidden_region" value="{{$data['region']['slug']??'mien-nam'}}">
                <div class="flex-column w-full">
                    <ul class="flex flex-wrap font-bold text-lg  text-center text-gray-500 border-b border-gray-400">
                        <li class="me-2">
                            <a href="#" class="inline-block p-2 rounded-t-sm text-blue-800 {{$data['type']===\App\Enums\HelperEnum::MienNamSlug->value ? 'active-bar-tap':''}}" onclick="goShowResult('{{\App\Enums\HelperEnum::MienNamSlug->value}}')">{{__('lang.mien-nam')}}</a>
                        </li>
                        <li class="me-2">
                            <a href="#" class="inline-block p-2 rounded-t-sm text-blue-800 {{$data['type']===\App\Enums\HelperEnum::MienTrungSlug->value ? 'active-bar-tap':''}}" onclick="goShowResult('{{\App\Enums\HelperEnum::MienTrungSlug->value}}')">{{__('lang.mien-trung')}}</a>
                        </li>
                        <li class="me-2">
                            <a href="#" class="inline-block p-2 rounded-t-sm text-blue-800 {{$data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value ? 'active-bar-tap':''}}" onclick="goShowResult('{{\App\Enums\HelperEnum::MienBacDienToanSlug->value}}')">{{__('lang.mien-bac')}}</a>
                        </li>
                    </ul>
                    <div class="flex w-full">
                        <table class="w-full h-[82vh] border-collapse border border-gray-400 rounded-lg text-center">
                                <thead class="bg-gray-500">
                                    <tr>
                                        <td class="border-2 py-3 border-gray-300 text-lg text-white font-bold">
                                            {{ $data['date_show'] }}
                                            <input type="hidden" value="{{$data['date_show']}}" id="date_result" name="date_result" />
                                        </td>
                                        @foreach($data['form_result']['schedule'] as $val)
                                            <td class="border-2 py-3 border-gray-300 text-2xl text-white font-bold">{{ $val['province'] }}</td>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($data['form_result']['result'] as $pKey => $prize)
                                    <tr>
                                        <td class="border border-gray-300 text-black font-bold text-bold text-xl">
                                            {{ $prize['prize_label'] }}
                                        </td>
                                        @foreach($prize['provinces'] as $province)
                                            <td class="p-1 border border-gray-300">
                                                @if($data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value)
                                                    @php $c=1; $r=1; @endphp
                                                    @foreach($province['row_result'] as $key=>$row)
                                                        @if($c == 1)
                                                            <div class="flex w-full justify-content-between">  <!-- open tag dev for new row -->
                                                                @endif
                                                                <div class="flex py-2 px-2 w-full justify-center">
                                                                    <h6 class="font-bold {{$row['tailwind_class']??''}}">{{ $row['winning_number']??'****'}}</h6>
                                                                </div>
                                                                @if($c >= $row['col_count'])
                                                            </div> <!-- close tag dev row -->
                                                            @php $c=0; @endphp
                                                        @endif
                                                        @php $c++; @endphp
                                                    @endforeach
                                                @else
                                                    @foreach($province['row_result'] as $row)
                                                        <div class="flex py-2 px-2 justify-center">
                                                            <h6 class="font-bold {{$row['tailwind_class']??''}}">{{ $row['winning_number']??'****'}}</h6>
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
    <!-- jQuery UI 1.11.4 -->
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