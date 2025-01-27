<x-admin>
    @section('title',__('lang.menu.lottery-result'))
    @include('admin.lottery-result.navbar', ['data' => $data])

    <!-- Tab panes -->
    <div class="tab-content">
        <div id="{{\App\Enums\HelperEnum::MienNamSlug->value }}" class="container tab-pane active"><br>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Table of Mien Nam</h3>
                    <div class="card-tools">
                        <a href="{{ route($data['url']['create']) }}" class="btn btn-sm btn-primary">Add</a>
                    </div>
                </div>
                <div class="card-body">
                    <h1>{{$data['type']}}</h1>
{{--                    <table class="table table-striped" style="width: 100%" id="collectionTable">--}}
{{--                        <thead>--}}
{{--                        <tr>--}}
{{--                            <th>No</th>--}}
{{--                            <th>Result Date</th>--}}
{{--                            <th>Created By</th>--}}
{{--                            <th style="width: 15px;">Action</th>--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody >--}}
{{--                        @forelse ($data as $key=>$permission)--}}
{{--                            <tr>--}}
{{--                                <td>{{ $key+1 }}</td>--}}
{{--                                <td>{{ date('d/m/Y',strtotime($permission->created_at)) }}</td>--}}
{{--                                <td>19/01/2025 13:23:32</td>--}}
{{--                                <td>--}}
{{--                                    <a href="{{ route('admin.lottery-result.edit', encrypt($permission->id)) }}"--}}
{{--                                       class="btn btn-sm btn-secondary">--}}
{{--                                        <i class="far fa-edit"></i>--}}
{{--                                    </a>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @empty--}}
{{--                            <tr>--}}
{{--                                <td colspan="4" class="text-center bg-light">No record</td>--}}
{{--                            </tr>--}}
{{--                        @endforelse--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
                </div>
            </div>
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
