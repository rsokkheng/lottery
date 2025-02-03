<x-admin>
    @section('title',__('lang.menu.lottery-result'))
    @include('admin.lottery-result.navbar', ['data' => $data])

    <!-- Tab panes -->
    <div class="tab-content">
        <div id="content-of-mien-nam-{{$data['type']}}" class="container tab-pane active">
            <div class="px-5 pb-5" style="width: 100%">
                    @if($data['type']===\App\Enums\HelperEnum::MienNamSlug->value)<h4 class="py-2">Entry result of {{__('lang.mien-nam')}}</h4>@endif
                    @if($data['type']===\App\Enums\HelperEnum::MienTrungSlug->value)<h4 class="py-2">Entry result of {{__('lang.mien-trung')}}</h4>@endif
                    @if($data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value)<h4 class="py-2">Entry result of {{__('lang.mien-bac')}}</h4>@endif

                        <form id="form_submit_result">
                            <table class="table table-bordered rounded-lg text-center table-striped" style="width: 100%">
                                <thead class="bg-dark">
                                    <tr>
                                        <td class="text-white ">
                                            {{ $data['current_date'] }}
                                            <input type="hidden" id="result-show-type" value="{{$data['type']}}">
                                            <input type="hidden" id="url-index-page" value="{{route($data['url']['index'])}}">
                                            <input type="hidden" value="{{$data['current_date']}}" id="date_result" name="date_result" />
                                        </td>
                                        @foreach($data['form_result']['schedule'] as $val)
                                                <td class="text-white ">{{ $val['province'] }}</td>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['form_result']['result'] as $pKey => $prize)
                                        <tr>
                                            <td class="text-white">
                                                {{ $prize['prize_label'] }}
                                            </td>
                                            @foreach($prize['provinces'] as $province)
                                                <td class="text-primary">
                                                    @foreach($province['row_result'] as $row)
                                                        <div class="p-1">
                                                            <input type="text"
                                                                   name="[{{$province['province_code']}}][{{$pKey}}][{{$province['schedule_id']}}][{{$row['result_order']}}]"
                                                                   value="{{ $row['winning_number']??''}}"
                                                                   data-max-length="{{$row['input_length']??0}}"
                                                                   class="form-control class-only-input-win-number"
                                                                   placeholder="0"
                                                                   aria-label="Winning number"
                                                            >
                                                        </div>
                                                    @endforeach
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex mt-4 justify-content-end">
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
                        alert('Invalid input data!');
                        $("#btnSaveResult").prop("disabled",false);
                        return;
                    }
                    let formData = {
                        data: getResult, // Sending all data inputs
                        result_region: $("#result-show-type").val(),
                        _token: "{{ csrf_token() }}"
                    };

                    $.ajax({
                        url: '/admin/result/store-winning-result',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function (response) {
                            console.log(response)
                            if(response.success){
                                alert('Data has been saved!');
                                setTimeout(()=>{
                                    window.location = $("#url-index-page").val();
                                }, 800)
                            }else{
                                alert('Data error input!');
                                $("#btnSaveResult").prop("disabled",false);
                            }
                        },
                        error: function (xhr) {
                            alert(xhr?.statusText);
                            $("#btnSaveResult").prop("disabled",false);
                        }
                    });

                })
            });
        </script>
    @endsection
</x-admin>
