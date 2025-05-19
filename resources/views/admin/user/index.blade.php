<x-admin>
    @section('title', 'Users')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">User Table</h3>
            <div class="card-tools"><a href="{{ route('admin.user.create') }}" class="btn btn-sm btn-primary">Add New</a></div>
        </div>
        <div class="card-body">
            <table class="table table-striped" id="userTable">
                <thead>
                    <tr style="font-size: 14px;">
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Manager</th>
                        <th>Account ID</th>
                        <th>Name</th>
                        <th>Package</th>
                        <th>Register Date</th>
                        <th>Action</th>
                  
                    </tr>
                </thead>
                <tbody>
                   
                    @foreach ($data as $key => $user)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>@foreach ($user->roles as $role)
                                @php
                                    switch ($role->name) {
                                        case 'admin':
                                            $badgeClass = 'bg-success'; // green
                                            break;
                                        case 'manager':
                                            $badgeClass = 'bg-primary'; // blue
                                            break;
                                        case 'member':
                                            $badgeClass = 'bg-danger'; // red
                                            break;
                                        default:
                                            $badgeClass = 'bg-secondary'; // default gray
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $role->name }}</span>
                            @endforeach

                            </td>
                            <td>{{ optional($user->manager)->name ?? '—' }} {{-- Manager's name --}}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->package->package_code }}</td>
                        
                            <td>{{ $user->created_at }}</td>
                            <td>
                                <a href="{{ route('admin.user.edit', encrypt($user->id)) }}" class="btn btn-sm btn-primary" style="display: inline-block; margin-right: 5px;">Edit</a> 
                                <form action="{{ route('admin.user.destroy', encrypt($user->id)) }}" method="POST" onsubmit="return confirm('Are sure want to delete?')" style="display: inline-block;">
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
                    "ordering": false,
                    "responsive": true,
                });
            });
        </script>
    @endsection
</x-admin>
