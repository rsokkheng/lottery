@php
    use Illuminate\Support\Facades\Auth;
    $auth = Auth::user();
@endphp
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <tr style="font-size: 12px;">
                        <th style="width: 3%;">#</th>
                        <th style="width: 5%;">Role</th>
                        <th>Account ID</th>
                        <th>Name</th>
                        <th>Currency</th>
                        <th>Available Credit</th>
                        <th>Bet Credit</th>
                        <th>Cash Balance</th>
                        <th>Register Date</th>
                        <th>Status</th>
                        <th>Action</th>
                  
                    </tr>
                </thead>
                <tbody>
                   
                    @foreach ($data as $key => $user)
                  
                        <tr style="font-size: 14px;">
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
                               @if ($role->name === 'manager')
                                    <a href="{{ route('admin.user.under-manager', ['manager_id' => $user->id]) }}">
                                        <span class="badge {{ $badgeClass }}">{{ $role->name }}</span>
                                    </a>
                                @else
                                    <span class="badge {{ $badgeClass }}">{{ $role->name }}</span>
                                @endif
                            @endforeach

                            </td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->currencies->currency ?? '' }}</td>
                            @if ($user->relationLoaded('accountManagement'))
                                <td>{{ $user->accountManagement->sum('available_credit') }}</td>
                                <td>{{ $user->accountManagement->sum('bet_credit') }}</td>
                            @elseif (isset($user->total_available_credit) && isset($user->total_bet_credit))
                                <td>{{ $user->total_available_credit }}</td>
                                <td>{{ $user->total_bet_credit }}</td>
                            @else
                                <td>0</td>
                                <td>0</td>
                            @endif
                            <td>0</td>
                            <td>{{ $user->created_at }}</td>
                            <td class="{{ $user->is_active == 1 ? 'text-blue-500' : 'text-red-500' }}">
                                {{ $user->is_active == 1 ? 'Active' : 'Suspend' }}
                            </td>

                            <td>
                            @if($auth->hasRole('admin') || $auth->hasRole('manager'))
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenu{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false"> ⚙️
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu{{ $user->id }}">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.user.edit', encrypt($user->id)) }}">Edit</a><br>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.user.package-view-lotto', encrypt($user->id)) }}">Lotto VN</a><br>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.user.destroy', encrypt($user->id)) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Delete</button>
                                            </form>
                                        </li>
                                        <li>
                                        <a class="dropdown-item" href="{{ route('admin.user.show', $user->id) }}">Setting</a>
                                        </li><br>
                                        <li>
                                        <a class="dropdown-item" href="{{ route('admin.user.change-password', $user->id) }}">Change Password</a>
                                        </li>
                                        <br>
                                        <li>
                                        <a class="dropdown-item" href="{{ route('admin.user.suspend', $user->id) }}">Suspend</a>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
