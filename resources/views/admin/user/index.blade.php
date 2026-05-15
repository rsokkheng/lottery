@php
    use Illuminate\Support\Facades\Auth;
    $auth = Auth::user();
    $roleBadge = [
        'admin'        => 'bg-dark',
        'master'       => 'bg-purple',
        'senior'       => 'bg-primary',
        'manager'      => 'bg-info',
        'share_master' => 'bg-warning text-dark',
        'member'       => 'bg-secondary',
    ];
    $supervisorRoles = ['admin', 'master', 'senior', 'manager'];
    $canManage = $auth->hasAnyRole($supervisorRoles);
@endphp
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .bg-purple { background-color: #6f42c1 !important; }
    .badge { font-size: 11px; }
</style>
<x-admin>
    @section('title', 'Account Management')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-users me-1"></i> Account Management</h3>
            @if($canManage)
            <a href="{{ route('admin.user.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Add New
            </a>
            @endif
        </div>
        <div class="card-body pb-2">
            <div class="table-responsive">
            <table class="table table-striped table-bordered" id="userTable" style="font-size:13px">
                <thead>
                    <tr style="font-size: 12px;">
                        <th style="width:3%">#</th>
                        <th>Role</th>
                        <th>Account ID</th>
                        <th>Name</th>
                        <th>Currency</th>
                        @if($auth->hasRole('admin'))
                        <th>Master / Manager</th>
                        @endif
                        <th>Register Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $user)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @foreach ($user->roles as $role)
                                    @php $cls = $roleBadge[$role->name] ?? 'bg-secondary'; @endphp
                                    @if (in_array($role->name, ['master', 'senior', 'manager']))
                                        <a href="{{ route('admin.user.under-manager', ['manager_id' => $user->id]) }}"
                                           title="View members under this user">
                                            <span class="badge {{ $cls }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                        </a>
                                    @else
                                        <span class="badge {{ $cls }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                    @endif
                                @endforeach
                            </td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->currencies->currency ?? '—' }}</td>
                            @if($auth->hasRole('admin'))
                            <td class="text-muted" style="font-size:12px">
                                @if($user->master)
                                    <span class="badge bg-purple me-1">{{ $user->master->name }}</span>
                                @endif
                                @if($user->manager && $user->manager->id !== $user->master?->id)
                                    <span class="badge bg-info">{{ $user->manager->name }}</span>
                                @endif
                            </td>
                            @endif
                            <td>{{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}</td>
                            <td class="text-center">
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Suspended</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($canManage)
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">⚙️</button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.user.edit', encrypt($user->id)) }}">
                                                <i class="fas fa-edit me-1 text-primary"></i> Edit
                                            </a>
                                        </li>
                                        @if($auth->hasRole('admin') || $auth->hasRole('master'))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.user.package-view-lotto', encrypt($user->id)) }}">
                                                <i class="fas fa-box me-1 text-info"></i> Lotto Package
                                            </a>
                                        </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.user.show', $user->id) }}">
                                                <i class="fas fa-sliders-h me-1 text-warning"></i> Bet Settings
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.user.change-password', $user->id) }}">
                                                <i class="fas fa-key me-1 text-secondary"></i> Change Password
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.user.suspend', $user->id) }}">
                                                <i class="fas fa-ban me-1 text-warning"></i> Suspend / Activate
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.user.destroy', encrypt($user->id)) }}" method="POST"
                                                onsubmit="return confirm('Delete this user?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-1"></i> Delete
                                                </button>
                                            </form>
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
