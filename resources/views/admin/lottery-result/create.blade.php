<x-admin>
    @section('title',__('lang.menu.lottery-result'))
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link {{$createType=='mien-nam' ? 'active' : ''}}" href="{{ route('admin.result.create-mien-nam') }}">{{__('lang.mien-nam')}}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$createType=='mien-trung' ? 'active' : ''}}" href="{{ route('admin.result.create-mien-trung') }}">{{__('lang.mien-trung')}}</a>
        </li>
        <li class="nav-item {{$createType=='mien-bac' ? 'active' : ''}}">
            <a class="nav-link" href="{{ route('admin.result.create-mien-bac') }}">{{__('lang.mien-bac')}}</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div id="home" class="container tab-pane active"><br>
            <div class="px-5" style="width: 100%">
                <table class="table table-bordered text-center table-striped">
                <thead class="bg-white">
                    <tr>
                        <th class="text-primary text-bold">C.N</th>
                        <th class="text-primary text-bold">Tien Giang</th>
                        <th class="text-primary text-bold">Kien Giang</th>
                        <th class="text-primary text-bold">Da Lat</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-bold">20/01/2025</td>
                        <td class="text-bold">L: TG-C1</td>
                        <td class="text-bold">L: 1K3</td>
                        <td class="text-bold">L: DL1K1</td>
                    </tr>
                    @foreach($data as $val)
                        <tr>
                            <td>{{$val}}</td>
                            <td><input type="text" class="form-control"></td>
                            <td><input type="text" class="form-control"></td>
                            <td><input type="text" class="form-control"></td>
                        </tr>
                    @endforeach

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
