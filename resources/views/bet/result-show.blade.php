<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/css/bootstrap-datepicker-1-7-1.min.css') }}" rel="stylesheet"/>
    <style>
        .datepicker-days {
            padding-left: 10px !important;
        }
        .active-bar-tap {
            /* background-color: deepskyblue !important;*/
            /*color: white !important;*/
        }
        
        /* Custom responsive styles */
        .results-container {
            min-height: calc(100vh - 200px);
            max-height: calc(100vh - 150px);
        }
        
        .results-table {
            min-height: 60vh;
        }
        
        /* Auto-sizing numbers based on screen */
        .winning-number {
            font-size: clamp(0.8rem, 2.5vw, 1.5rem);
            font-weight: bold;
            line-height: 1.2;
        }
        
        .prize-label {
            font-size: clamp(0.7rem, 2vw, 1.2rem);
            font-weight: 600;
        }
        
        .province-name {
            font-size: clamp(0.9rem, 3vw, 1.8rem);
            font-weight: bold;
            line-height: 1.1;
        }
        
        .province-code {
            font-size: clamp(0.6rem, 1.8vw, 1rem);
            line-height: 1.2;
        }
        
        /* Responsive table cells */
        .result-cell {
            min-width: clamp(80px, 12vw, 150px);
            padding: clamp(4px, 1vw, 12px);
        }
        
        .prize-cell {
            min-width: clamp(60px, 8vw, 120px);
            padding: clamp(8px, 1.5vw, 16px);
        }
        
        @media (max-width: 1024px) {
            .winning-number {
                font-size: clamp(0.7rem, 3vw, 1.3rem);
            }
            .province-name {
                font-size: clamp(0.8rem, 3.5vw, 1.5rem);
            }
        }
        
        @media (max-width: 768px) {
            .results-container {
                min-height: calc(100vh - 250px);
                max-height: calc(100vh - 200px);
            }
            
            .results-table {
                min-height: 50vh;
            }
            
            .winning-number {
                font-size: clamp(0.65rem, 3.5vw, 1.1rem);
            }
            
            .province-name {
                font-size: clamp(0.7rem, 4vw, 1.2rem);
            }
            
            .result-cell {
                min-width: clamp(60px, 15vw, 100px);
            }
        }
        
        @media (max-width: 640px) {
            .results-container {
                min-height: calc(100vh - 280px);
                max-height: calc(100vh - 220px);
            }
            
            .winning-number {
                font-size: clamp(0.6rem, 4vw, 1rem);
            }
            
            .province-name {
                font-size: clamp(0.65rem, 4.5vw, 1rem);
            }
            
            .province-code {
                font-size: clamp(0.5rem, 2.5vw, 0.8rem);
            }
        }
        
        /* Ultra small screens */
        @media (max-width: 480px) {
            .winning-number {
                font-size: clamp(0.55rem, 4.5vw, 0.9rem);
            }
            
            .result-cell {
                min-width: clamp(50px, 18vw, 80px);
                padding: clamp(2px, 0.8vw, 8px);
            }
        }
    </style>

    <div class="flex flex-col lg:flex-row bg-white rounded-lg p-3 sm:p-5 gap-4 h-screen overflow-hidden">
        <!-- Left Panel - Date Picker -->
        <div class="flex-shrink-0 w-full lg:w-auto">
            <div class="flex flex-col space-y-3">
                <a class="hidden" id="href_show_result" href="{{ route('bet.result-show') }}"></a>
                
                <div class="text-center lg:text-left">
                    <h6 class="font-bold text-sm sm:text-base">
                        Today: <span>{{\Carbon\Carbon::today()->format('d/m/Y')}}</span>
                    </h6>
                </div>
                
                <div class="text-center lg:text-left">
                    <h6 class="font-bold text-sm sm:text-base">
                        Result Date: <span>{{$data['date_show']}}</span>
                    </h6>
                </div>
                
                <div class="flex justify-center lg:justify-start">
                    <div class="rounded border-2 border-blue-400">
                        <div id="dev_datepicker" data-date="{{$data['date_show']}}" data-date-format="dd/mm/yyyy"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Results -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <input type="hidden" id="hidden_region" value="{{$data['region']['slug']??'mien-nam'}}">
            
            <!-- Region Tabs -->
            <div class="flex-shrink-0 mb-4">
                <ul class="flex flex-row font-bold text-base sm:text-lg text-center text-gray-500 overflow-x-auto">
                    <li class="flex-shrink-0 me-2">
                        <a class="inline-block cursor-pointer px-2 py-1 sm:p-2 rounded-t-sm text-blue-800 whitespace-nowrap {{$data['type']===\App\Enums\HelperEnum::MienNamSlug->value ? 'active-bar-tap bg-blue-500 text-white font-bold border-t border-l border-r border-blue-600':''}}" 
                           onclick="goShowResult('{{\App\Enums\HelperEnum::MienNamSlug->value}}')">
                            {{__('lang.mien-nam')}}
                        </a>
                    </li>
                    <li class="flex-shrink-0 me-2">
                        <a class="inline-block cursor-pointer px-2 py-1 sm:p-2 rounded-t-sm text-blue-800 whitespace-nowrap {{$data['type']===\App\Enums\HelperEnum::MienTrungSlug->value ? 'active-bar-tap bg-blue-500 text-white font-bold border-t border-l border-r border-blue-600':''}}" 
                           onclick="goShowResult('{{\App\Enums\HelperEnum::MienTrungSlug->value}}')">
                            {{__('lang.mien-trung')}}
                        </a>
                    </li>
                    <li class="flex-shrink-0 me-2">
                        <a class="inline-block cursor-pointer px-2 py-1 sm:p-2 rounded-t-sm text-blue-800 whitespace-nowrap {{$data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value ? 'active-bar-tap bg-blue-500 text-white font-bold border-t border-l border-r border-blue-600':''}}" 
                           onclick="goShowResult('{{\App\Enums\HelperEnum::MienBacDienToanSlug->value}}')">
                            {{__('lang.mien-bac')}}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Results Table Container -->
            <div class="flex-1 border rounded-b border-gray-400 p-2 sm:p-4 overflow-hidden results-container">
                <div class="w-full h-full overflow-auto">
                    <table class="w-full border-collapse border border-blue-800 rounded-lg text-center results-table">
                        <thead class="bg-yellow-600 sticky top-0 z-10">
                            <tr class="border-gray-300">
                                <td class="border-2 text-white font-bold sticky left-0 bg-yellow-600 z-20 prize-cell">
                                    <div class="prize-label">
                                        {{ $data['date_show'] }}
                                        <input type="hidden" value="{{$data['date_show']}}" id="date_result" name="date_result" />
                                    </div>
                                </td>
                                @foreach($data['form_result']['schedule'] as $val)
                                    <td class="border-2 text-white font-bold result-cell">
                                        <div class="flex-col w-full">
                                            <div class="province-name">
                                                {{ $val['province'] }}
                                            </div>
                                            <div class="province-code">
                                                ({{ $val['code'] }})
                                            </div>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['form_result']['result'] as $pKey => $prize)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 text-black sticky left-0 bg-white z-10 prize-cell">
                                        <div class="prize-label">
                                            {{ $prize['prize_label'] }}
                                        </div>
                                    </td>
                                    @foreach($prize['provinces'] as $province)
                                        <td class="border border-gray-300 align-middle result-cell">
                                            <div class="w-full">
                                                @if($data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value)
                                                    @php $c=1; $r=1; @endphp
                                                    @foreach($province['row_result'] as $key=>$row)
                                                        @if($c == 1)
                                                            <div class="flex w-full justify-between gap-1 mb-1">
                                                        @endif
                                                        <div class="flex-1 text-center">
                                                            <h6 class="winning-number {{ $row['tailwind_class']??'' }}">
                                                                {{ $row['winning_number']??'****'}}
                                                            </h6>
                                                        </div>
                                                        @if($c >= $row['col_count'])
                                                            </div>
                                                            @php $c=0; @endphp
                                                        @endif
                                                        @php $c++; @endphp
                                                    @endforeach
                                                @else
                                                    @foreach($province['row_result'] as $row)
                                                        <div class="flex w-full justify-center mb-1">
                                                            <h6 class="winning-number {{ $row['tailwind_class']??'' }}">
                                                                {{ $row['winning_number']??'****'}}
                                                            </h6>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
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

    <!-- Scripts -->
    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/datepicker/js-1-7-1/datepicker.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <script>
        $(function(){
            $('#dev_datepicker').datepicker({
                format: 'dd/mm/yyyy',
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

        // Auto-resize functionality
        function adjustTableHeight() {
            const container = document.querySelector('.results-container');
            if (container) {
                const windowHeight = window.innerHeight;
                const containerTop = container.getBoundingClientRect().top;
                const padding = 50; // Bottom padding
                container.style.height = `${windowHeight - containerTop - padding}px`;
            }
        }

        // Adjust on load and resize
        window.addEventListener('load', adjustTableHeight);
        window.addEventListener('resize', adjustTableHeight);
    </script>
</x-app-layout>