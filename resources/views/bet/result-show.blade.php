<x-app-layout>
    <style>
        .active-bar-tap {
            /* background-color: deepskyblue !important;*/
            /*color: white !important;*/
        }
        
        /* Full screen layout */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        .full-screen-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .table-container {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .table-wrapper {
            flex: 1;
            overflow: auto;
        }
        
        .responsive-table {
            width: 100%;
            height: 100%;
            min-height: 400px;
        }
        
        /* Mobile adjustments */
        @media (max-width: 768px) {
            .date-controls {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .tab-navigation {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .responsive-table {
                min-width: 100%;
                font-size: 0.75rem;
            }
        }
    </style>

    <div class="full-screen-container bg-white">
        <div class="content-wrapper px-2 sm:px-4 py-2 sm:py-4">
            <!-- Date Control Section -->
            <div class="date-controls flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 mb-3 sm:mb-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                    <a class="hidden" id="href_show_result" href="{{ route('bet.result-show') }}"></a>
                    <input type="date" 
                           id="date_input" 
                           class="px-3 py-2 border-2 border-blue-400 rounded focus:border-blue-600 focus:outline-none text-sm sm:text-base w-full sm:w-auto"
                           value="{{date('Y-m-d', strtotime(str_replace('/', '-', $data['date_show'])))}}"
                           max="{{date('Y-m-d')}}">
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="mb-3 sm:mb-4">
                <input type="hidden" id="hidden_region" value="{{$data['region']['slug']??'mien-nam'}}">
                <div class="tab-navigation">
                    <ul class="flex flex-row font-bold text-sm sm:text-lg text-center text-gray-500 border-b">
                        <li class="flex-shrink-0">
                            <a class="inline-block cursor-pointer px-3 py-2 sm:px-4 sm:py-3 text-blue-800 whitespace-nowrap {{$data['type']===\App\Enums\HelperEnum::MienNamSlug->value ? 'active-bar-tap bg-blue-500 text-white font-bold border-t border-l border-r border-blue-600 rounded-t-lg':'hover:bg-blue-50'}}" 
                               onclick="goShowResult('{{\App\Enums\HelperEnum::MienNamSlug->value}}')">{{__('lang.mien-nam')}}</a>
                        </li>
                        <li class="flex-shrink-0">
                            <a class="inline-block cursor-pointer px-3 py-2 sm:px-4 sm:py-3 text-blue-800 whitespace-nowrap {{$data['type']===\App\Enums\HelperEnum::MienTrungSlug->value ? 'active-bar-tap bg-blue-500 text-white font-bold border-t border-l border-r border-blue-600 rounded-t-lg':'hover:bg-blue-50'}}" 
                               onclick="goShowResult('{{\App\Enums\HelperEnum::MienTrungSlug->value}}')">{{__('lang.mien-trung')}}</a>
                        </li>
                        <li class="flex-shrink-0">
                            <a class="inline-block cursor-pointer px-3 py-2 sm:px-4 sm:py-3 text-blue-800 whitespace-nowrap {{$data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value ? 'active-bar-tap bg-blue-500 text-white font-bold border-t border-l border-r border-blue-600 rounded-t-lg':'hover:bg-blue-50'}}" 
                               onclick="goShowResult('{{\App\Enums\HelperEnum::MienBacDienToanSlug->value}}')">{{__('lang.mien-bac')}}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container border border-gray-300 rounded-lg overflow-hidden">
                <div class="table-wrapper">
                    <table class="responsive-table border-collapse text-center">
                        <thead class="bg-yellow-600 sticky top-0">
                            <tr class="border-gray-300">
                                <td class="border-2 py-2 px-2 text-lg max-md:text-sm text-white font-bold min-w-[120px]">
                                    {{ $data['date_show'] }}
                                    <input type="hidden" value="{{$data['date_show']}}" id="date_result" name="date_result" />
                                </td>
                                @foreach($data['form_result']['schedule'] as $val)
                                    <td class="border-2 text-white font-bold px-1 sm:px-2 min-w-[100px]">
                                        <div class="flex-col w-full py-2">
                                            <div class="text-2xl max-md:text-lg">
                                                {{ $val['province'] }}
                                            </div>
                                            <div class="text-lg max-md:text-sm">
                                                ({{ $val['code'] }})
                                            </div>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($data['form_result']['result'] as $pKey => $prize)
                            <tr class="{{$loop->even ? 'bg-gray-50' : 'bg-white'}}">
                                <td style="font-size: 20px; font-weight: 600;" class="border border-gray-300 text-black font-bold text-bold text-xl max-md:text-md py-2 px-2 sticky left-0 {{$loop->even ? 'bg-gray-50' : 'bg-white'}}">
                                    {{ $prize['prize_label'] }}
                                </td>
                                @foreach($prize['provinces'] as $province)
                                    <td class="p-1 sm:p-2 border border-gray-300 align-middle">
                                        @if($data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value)
                                            @php $c=1; $r=1; @endphp
                                            @foreach($province['row_result'] as $key=>$row)
                                                @if($c == 1)
                                                    <div class="flex w-full justify-content-between gap-1">
                                                @endif
                                                <div class="flex w-full justify-center">
                                                    <h6 @class([$row['tailwind_class']??'', 'text-bold'])>
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
                                                <div class="flex w-full justify-center py-1">
                                                    <h6 @class([$row['tailwind_class']??'', 'text-bold'])>
                                                        {{ $row['winning_number']??'****'}}
                                                    </h6>
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

    <script>
        $(function(){
            // Handle date input change
            $('#date_input').on('change', function() {
                let selectedDate = $(this).val();
                if(selectedDate) {
                    // Convert from YYYY-MM-DD to DD/MM/YYYY for display
                    let dateObj = new Date(selectedDate);
                    let formattedDate = String(dateObj.getDate()).padStart(2, '0') + '/' + 
                                      String(dateObj.getMonth() + 1).padStart(2, '0') + '/' + 
                                      dateObj.getFullYear();
                    
                    $('#date_result').val(formattedDate);
                    
                    // Navigate to new URL
                    window.location = $("#href_show_result").attr('href') + '?date=' + formattedDate + '&region=' + $("#hidden_region").val();
                }
            });

            // Responsive adjustments
            function adjustLayout() {
                const viewport = window.innerHeight;
                const headerHeight = $('.date-controls').outerHeight() + $('.tab-navigation').outerHeight() + 100; // Add some padding
                const availableHeight = viewport - headerHeight;
                
                $('.table-container').css('height', Math.max(400, availableHeight) + 'px');
            }

            // Initial adjustment and on window resize
            adjustLayout();
            $(window).resize(adjustLayout);
        });

        function goShowResult(region){
            let selectedDate = $('#date_input').val();
            let formattedDate = '';
            
            if(selectedDate) {
                // Convert from YYYY-MM-DD to DD/MM/YYYY
                let dateObj = new Date(selectedDate);
                formattedDate = String(dateObj.getDate()).padStart(2, '0') + '/' + 
                               String(dateObj.getMonth() + 1).padStart(2, '0') + '/' + 
                               dateObj.getFullYear();
            } else {
                formattedDate = $('#date_result').val();
            }
            
            window.location = $("#href_show_result").attr('href') + '?date=' + formattedDate + '&region=' + region;
        }
    </script>
</x-app-layout>