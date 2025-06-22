<x-admin>
    @section('title',__('lang.menu.lottery-result'))
    @include('admin.lottery-result.navbar', ['data' => $data])

    <!-- Tab panes -->
    <div class="tab-content">
        <div id="content-of-mien-nam-{{$data['type']}}" class="container tab-pane active">
            <div style="width: 100%">
                    @if($data['type']===\App\Enums\HelperEnum::MienNamSlug->value)<h4 class="py-2">Entry result of {{__('lang.mien-nam')}}</h4>@endif
                    @if($data['type']===\App\Enums\HelperEnum::MienTrungSlug->value)<h4 class="py-2">Entry result of {{__('lang.mien-trung')}}</h4>@endif
                    @if($data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value)<h4 class="py-2">Entry result of {{__('lang.mien-bac')}}</h4>@endif

                        <form id="form_submit_result">
                            <table class="table table-bordered rounded-lg text-center table-striped" style="width: 100%">
                                <thead class="bg-dark">
                                    <tr>
                                        <td class="text-white">
                                            {{ $data['current_date'] }}
                                            <input type="hidden" id="result-show-type" value="{{$data['type']}}">
                                            <input type="hidden" id="url-index-page" value="{{route($data['url']['index'])}}">
                                            <input type="hidden" value="{{$data['current_date']}}" id="date_result" name="date_result" />
                                        </td>
                                        @foreach($data['form_result']['schedule'] as $val)
                                                <td class="text-white">
                                                    <div>{{ $val['province'] }}</div>
                                                    <div class="text-sm">({{ $val['code'] }})</div>
                                                </td>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['form_result']['result'] as $pKey => $prize)
                                        <tr>
                                            <td class="text-black">
                                                {{ $prize['prize_label'] }}
                                            </td>
                                            @foreach($prize['provinces'] as $province)
                                                <td class="text-primary p-1">
                                                    @if($data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value)
                                                        @php $c=1; $r=1; @endphp
                                                        @foreach($province['row_result'] as $key=>$row)
                                                            @if($c == 1)
                                                                <div class="d-flex w-full justify-content-between">  <!-- open tag dev for new row -->
                                                                    @endif
                                                                    <div class="d-flex w-full max-md:p-0 justify-center">
                                                                        <p class="pr-1 pl-1 pt-2 text-secondary" style="height: 100% !important;">{{$row['result_order']}}</p>
                                                                        <input type="text"
                                                                               name="[{{$province['province_code']}}][{{$pKey}}][{{$province['schedule_id']}}][{{$row['result_order']}}]"
                                                                               value="{{ $row['winning_number']??''}}"
                                                                               data-max-length="{{$row['input_length']??0}}"
                                                                               maxlength="{{$row['input_length']??0}}"
                                                                               class="form-control border-0 shadow-none class-only-input-win-number split-input"
                                                                               placeholder="0"
                                                                               aria-label="Winning number"
                                                                        >
                                                                    </div>
                                                                    @if($c >= $row['col_count'])
                                                                </div> <!-- close tag dev row -->
                                                                @php $c=0; @endphp
                                                            @endif
                                                            @php $c++; @endphp
                                                        @endforeach
                                                    @else
                                                        @foreach($province['row_result'] as $row)
                                                            <div class="d-flex w-full justify-center max-md:p-0">
                                                                <p class="pr-1 pt-2 text-secondary" style="height: 100% !important;">{{$row['result_order']}}</p>
                                                                <input type="text"
                                                                       name="[{{$province['province_code']}}][{{$pKey}}][{{$province['schedule_id']}}][{{$row['result_order']}}]"
                                                                       value="{{ $row['winning_number']??''}}"
                                                                       data-max-length="{{$row['input_length']??0}}"
                                                                       maxlength="{{$row['input_length']??0}}"
                                                                       class="form-control border-0 shadow-none class-only-input-win-number split-input"
                                                                       placeholder="0"
                                                                       aria-label="Winning number"
                                                                >
                                                            </div>
                                                        @endforeach
                                                    @endif

                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex mt-4 justify-content-end">
                                <input type="hidden" id="get_currency" value="{{session('currency')}}" />
                                <button type="submit" id="btnSaveResult" class="m-2 btn btn-success btn-md">Save</button>
                                <button type="button" id="btnClearResult" class="m-2 btn btn-secondary btn-md">Clear</button>
                            </div>
                        </form>
            </div>
        </div>
    </div>


    @section('js')
        <script>
            $(function(){


                $('table').on('paste', 'input', function (e) {
                    const clipboardData = (e.originalEvent || e).clipboardData || window.clipboardData;
                    const pastedText = clipboardData.getData('text').replace(/\s+/g, '');
                    e.preventDefault();

                    let charIndex = 0;

                    const $startInput = $(this);
                    const $startTd = $startInput.closest('td');
                    const columnIndex = $startTd.index();
                    const $table = $startTd.closest('table');
                    let startPasting = false;

                    // Traverse table rows to find all inputs in the same column
                    $table.find('tr').each(function () {
                        const $td = $(this).find('td').eq(columnIndex);
                        if (!$td.length) return;

                        const $inputs = $td.find('input');

                        $inputs.each(function () {
                            if (!startPasting) {
                                if (this === $startInput[0]) {
                                    startPasting = true;
                                } else {
                                    return;
                                }
                            }

                            if (startPasting && charIndex < pastedText.length) {
                                const maxLen = parseInt($(this).attr('maxlength')) || pastedText.length;
                                const valuePart = pastedText.slice(charIndex, charIndex + maxLen);
                                $(this).val(valuePart);
                                charIndex += valuePart.length;
                            }
                        });
                    });
                });


                $('.class-only-input-win-number').each(function () {
                    let inputLength = $(this).data("max-length")??0
                    $(this).on("input", function () {
                        let value = $(this).val();
                        // Allow only numbers (0-9) and keep leading zeros
                        $(this).val(value.replace(/[^0-9]/g, "").substring(0, inputLength));
                    });
                });

                $("#btnClearResult").click(function() {
                    $('.class-only-input-win-number').each(function () {
                        $(this).val("")
                    })
                });

                $('#form_submit_result').submit(function (e){
                    e.preventDefault();
                    $("#btnSaveResult").prop("disabled",true);
                    let getResult = [];
                    let resultDate = $("#date_result").val()
                    let isInvalid = false;
                    $('.class-only-input-win-number').each(function () {
                        let winNumber = $(this).val()??'';
                        if($(this).attr("name").length>0){
                            let str = ($(this).attr("name"))
                            let arr = str.match(/\[(.*?)\]/g).map(item => item.replace(/\[|\]/g, ''));
                            if(winNumber){
                                let inputLength = $(this).data("max-length")??0
                                if(inputLength === winNumber.length){
                                    $(this).removeClass('is-invalid');
                                    getResult.push({
                                        "result_date" : resultDate,
                                        "winning_number": winNumber,
                                        "province_code" : arr[0]??'',
                                        "prize_level": arr[1]??'',
                                        "schedule_id": arr[2]??'',
                                        "result_order": arr[3]??'',
                                    })
                                }else{
                                    isInvalid = true;
                                    $(this).addClass('is-invalid');
                                }
                            }else{
                                isInvalid = true;
                                $(this).addClass('is-invalid');
                            }
                        }
                    });
                    if(isInvalid){
                        toastr.warning('Invalid input');
                        $("#btnSaveResult").prop("disabled",false);
                        return;
                    }
                    let formData = {
                        data: getResult, // Sending all data inputs
                        result_region: $("#result-show-type").val(),
                        _token: "{{ csrf_token() }}"
                    };
                    // let currency = $("#get_currency").val();
                    // console.log(currency)
                    // let url_ = ''
                    // if(currency === 'USD'){
                    //     url_ = '/admin/result/store-winning-result-usd';
                    // }else{
                    //     url_ = '/admin/result/store-winning-result-vnd';
                    // }
                    // console.log(url_)
                    $.ajax({
                        url: '/admin/result/store-winning-record',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function (response) {
                            if(response.success){
                                toastr.success('Save successfully!');
                                setTimeout(()=>{
                                    window.location = $("#url-index-page").val();
                                }, 800)
                            }else{
                                toastr.warning('Invalid input!');
                                $("#btnSaveResult").prop("disabled",false);
                            }
                        },
                        error: function (xhr) {
                            toastr.warning(xhr?.statusText);
                            $("#btnSaveResult").prop("disabled",false);
                        }
                    });

                })

            });
        </script>
    @endsection
</x-admin>
