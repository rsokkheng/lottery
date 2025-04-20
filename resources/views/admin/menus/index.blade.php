<x-admin>
    @section('title', 'Menus')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Menus Table</h3>
            <div class="card-tools"><a href="{{ route('admin.menu.create') }}" class="btn btn-sm btn-primary">Add New</a></div>
        </div>
        <div class="card-body">
            <table class="table table-striped" id="userTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Banner</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Text</th>
                        <th>Action</th>
                  
                    </tr>
                </thead>
                <tbody>
                    @foreach ($menus as $menu)
                        <tr>
                            <td>{{ $menu->id }}</td>
                            <td> <img src="{{ asset('uploads/banners/' .$menu->banner) }}"  width="100"></td>
                            <td> <img src="{{ asset('uploads/images/' .$menu->image) }}" width="100"></td>
                            <td>{{ $menu->title }}</td>
                            <td>{{ $menu->text }}</td>
                            <td>
                                <a href="{{ route('admin.menu.edit', encrypt($menu->id)) }}" class="btn btn-sm btn-primary" style="display: inline-block; margin-right: 5px;">Edit</a> 
                                <form action="{{ route('admin.menu.destroy', encrypt($menu->id)) }}" method="POST" onsubmit="return confirm('Are sure want to delete?')" style="display: inline-block;">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @section('js')
        <script>
            $(function() {
                $('#userTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "responsive": true,
                });
            });
        </script>
    @endsection
</x-admin>
