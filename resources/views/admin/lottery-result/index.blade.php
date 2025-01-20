<x-admin>
    @section('title',__('lang.menu.lottery-result'))

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="#home">{{__('lang.mien-nam')}}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#menu1">{{__('lang.mien-trung')}}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#menu2">{{__('lang.mien-bac')}}</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div id="home" class="container tab-pane active"><br>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Table of Mien Nam</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.result.create-mien-nam') }}" class="btn btn-sm btn-primary">Add</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped" style="width: 100%" id="collectionTable">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Result Date</th>
                            <th>Created By</th>
                            <th style="width: 15px;">Action</th>
                        </tr>
                        </thead>
                        <tbody >
                        @forelse ($data as $key=>$permission)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ date('d/m/Y',strtotime($permission->created_at)) }}</td>
                                <td>19/01/2025 13:23:32</td>
                                <td>
                                    <a href="{{ route('admin.lottery-result.edit', encrypt($permission->id)) }}"
                                       class="btn btn-sm btn-secondary">
                                        <i class="far fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center bg-light">No record</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="menu1" class="container tab-pane fade"><br>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Table of Mien Trung</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.result.create-mien-trung') }}" class="btn btn-sm btn-primary">Add</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped" style="width: 100%" id="datatableNumber2">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Result Date</th>
                            <th>Created By</th>
                            <th style="width: 15px;">Action</th>
                        </tr>
                        </thead>
                        <tbody >
                        @forelse ($data as $key=>$permission)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ date('d/m/Y',strtotime($permission->created_at)) }}</td>
                                <td>19/01/2025 13:23:32</td>
                                <td>
                                    <a href="{{ route('admin.lottery-result.edit', encrypt($permission->id)) }}"
                                       class="btn btn-sm btn-secondary">
                                        <i class="far fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center bg-light">No record</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="menu2" class="container tab-pane fade"><br>
            List of Mien Bac
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

                $('#datatableNumber2').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "responsive": true,
                });
            });
        </script>
    @endsection
</x-admin>
