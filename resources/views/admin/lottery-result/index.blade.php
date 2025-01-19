<x-admin>
    @section('title',__('lang.menu.lottery-result'))
    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link px-3 bg-gradient-primary active" id="nav-tab-1" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">{{__('lang.mien-nam')}}</button>
            <button class="nav-link px-3 bg-gradient-gray-dark" id="nav-tab-2" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">{{__('lang.mien-trung')}}</button>
            <button class="nav-link px-3 bg-gradient-gray-dark" id="nav-tab-3" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">{{__('lang.mien-bac')}}</button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade active show" id="content-tap-1" role="tabpanel" aria-labelledby="nav-home-tab">
            <div class="card">
                <div class="card-header">
{{--                    <h3 class="card-title">Result</h3>--}}
                    <div class="card-tools">
                        <a href="{{ route('admin.lottery-result.create') }}" class="btn btn-sm btn-primary">Add</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="collectionTable">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Result Date</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody >
                        @forelse ($data as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->created_at }}</td>
                                <td>19/01/2025 13:23:32</td>
                                <td>
                                    <a href="{{ route('admin.lottery-result.edit', encrypt($permission->id)) }}"
                                       class="btn btn-sm btn-secondary">
                                        <i class="far fa-edit"></i>
                                    </a>
                                </td>
{{--                                <td>--}}
                                    {{--                            <form action="{{ route('admin.lottery-result.destroy', encrypt($permission->id)) }}"--}}
                                    {{--                                  method="POST" onclick="confirm('Are you sure')">--}}
                                    {{--                                @method('DELETE')--}}
                                    {{--                                @csrf--}}
                                    {{--                                <button type="submit" class="btn btn-danger">--}}
                                    {{--                                    <i class="fas fa-trash-alt"></i>--}}
                                    {{--                                </button>--}}
                                    {{--                            </form>--}}
{{--                                </td>--}}
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
        <div class="tab-pane fade" id="content-tap-2" role="tabpanel" aria-labelledby="nav-profile-tab">BBBB</div>
        <div class="tab-pane fade" id="content-tap-3" role="tabpanel" aria-labelledby="nav-contact-tab">CCCC</div>
    </div>


    @section('js')
        <script>
            $(function(){
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
