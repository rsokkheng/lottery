<x-admin>
    @section('title',__('lang.menu.lottery-result'))
    @include('admin.lottery-result.navbar', ['data' => $data])

    <!-- Tab panes -->
    <div class="tab-content">
        <div id="mien-nam" class="container tab-pane active">
            <div class="px-5" style="width: 100%">
                <h4 class="py-2">Entry result of {{$data['type']}}</h4>

                <table class="table table-bordered rounded-lg text-center table-striped" style="width: 100%">
                    <thead class="bg-dark">
                        <tr>
                            <td class="text-white ">{{ $data['current_date'] }}</td>
                            @foreach($data['form_result']['schedule'] as $val)
                                    <td class="text-white ">{{ $val['province'] }}</td>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['form_result']['result'] as $prize)
                            <tr>
                                <td class=" text-white">{{ $prize['prize_label'] }}</td>
                                @foreach($prize['provinces'] as $province)
                                    <td class="text-primary">
                                        @foreach($province['row_result'] as $row)
                                            <div class="p-2">
                                                <input type="text" class="form-control" placeholder="{{ $row['result_order'] }}" aria-label="Winning number" >
                                            </div>
                                        @endforeach
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
        <div id="mien-trung" class="container tab-pane fade"><br>
            Create Mien Trung
        </div>
        <div id="mien-bac" class="container tab-pane fade"><br>
            Create Mien Bac
        </div>
    </div>


    @section('js')
        <script>
            $(function(){
                $(".nav-tabs a").click(function(){
                    $(this).tab('show');
                });

                $('#collectionTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "responsive": true,
                });
            });
        </script>
    @endsection
</x-admin>
