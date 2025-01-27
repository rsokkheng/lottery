<x-admin>
    @section('title',__('lang.menu.lottery-result'))
    @include('admin.lottery-result.navbar', ['data' => $data])

    <!-- Tab panes -->
    <div class="tab-content">
        <div id="home" class="container tab-pane active"><br>
            <div class="px-5" style="width: 100%">
                <table class="table table-bordered text-center table-striped">
                <thead class="bg-white rounded">
                    <tr>
                        <th class="text-primary text-bold">{{ $data['current_date'] }}</th>
                        @foreach($data['lottery_schedule'] as $val)
                            <th class="text-primary text-bold">{{ $val['province'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-primary">Gian Tam </td>
                        <td class="text-blue">GianTam-TG-Date-Number-Oder</td>
                        <td class="text-blue">GianTam-TG-Date-Number-Oder</td>
                        <td class="text-blue">GianTam-TG-Date-Number-Oder</td>
                    </tr>
                    @foreach($data['form_result'] as $row)
                        <tr>
                            <td class="text-primary">{{$row['label']}}</td>
                            <td class="text-blue">GianTam-TG-Date-Number-Oder</td>
                            <td class="text-blue">GianTam-TG-Date-Number-Oder</td>
                            <td class="text-blue">GianTam-TG-Date-Number-Oder</td>
                        </tr>
                    @endforeach


{{--                    @foreach($data['lottery_schedule'] as $val)--}}
{{--                        <td class="text-primary">{{ $val['code'] }}</td>--}}
{{--                    @endforeach--}}


{{--                    @foreach($data as $val)--}}
{{--                        <tr>--}}
{{--                            <td>{{$val}}</td>--}}
{{--                            <td><input type="text" class="form-control"></td>--}}
{{--                            <td><input type="text" class="form-control"></td>--}}
{{--                            <td><input type="text" class="form-control"></td>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}

                </tbody>
            </table>
            </div>
        </div>
        <div id="menu1" class="container tab-pane fade"><br>
            Create Mien Trung
        </div>
        <div id="menu2" class="container tab-pane fade"><br>
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
