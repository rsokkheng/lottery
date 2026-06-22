@php
    use Illuminate\Support\Facades\Auth;
    $auth = Auth::user();
    $roleBadge = [
        'admin'  => 'bg-dark',
        'master' => 'bg-purple',
        'agent'  => 'bg-info',
        'member' => 'bg-secondary',
    ];
    $betSystemColor = ['vietnam' => 'bg-success', 'khmer' => 'bg-danger'];
    $supervisorRoles = ['admin', 'master', 'agent'];
    $canManage = $auth->hasAnyRole($supervisorRoles);
    $isAdmin   = $auth->hasRole('admin');
    $isMaster  = $auth->hasRole('master');
    $showHierarchy = $isAdmin || $isMaster;
@endphp
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .bg-purple { background-color: #6f42c1 !important; }
    .badge { font-size: 11px; }
    .bet-badge { font-size: 10px; padding: 2px 6px; border-radius: 4px; }
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
                        @if($showHierarchy)
                        <th>{{ $isAdmin ? 'Master / Agent' : 'Agent' }}</th>
                        <th>Bet Types</th>
                        @endif
                        <th>Register Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $user)
                    @php $userRole = $user->roles->first()->name ?? null; @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @foreach ($user->roles as $role)
                                    @php $cls = $roleBadge[$role->name] ?? 'bg-secondary'; @endphp
                                    @if (in_array($role->name, ['master', 'agent']))
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
                            <td>
                                @if($user->bet_system)
                                    @php $curLabel = $user->currency === 'VND' ? 'Vietnamese Dong' : 'USD Dollar'; @endphp
                                    <span class="badge {{ $user->bet_system === 'khmer' ? 'bg-danger' : 'bg-success' }} me-1" style="font-size:10px">
                                        Bet {{ ucfirst($user->bet_system) }}
                                    </span>
                                    <span class="badge bg-secondary" style="font-size:10px">{{ $curLabel }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            @if($showHierarchy)
                            {{-- Hierarchy: admin sees master + agent, master sees agent only --}}
                            <td class="text-muted" style="font-size:12px">
                                @if($isAdmin && $user->master)
                                    <span class="badge bg-purple me-1">{{ $user->master->name }}</span>
                                @endif
                                @if($user->manager && ($isAdmin ? $user->manager->id !== $user->master?->id : true))
                                    <span class="badge bg-info">{{ $user->manager->name }}</span>
                                @endif
                            </td>

                            {{-- Bet Types (only meaningful for agents) --}}
                            <td>
                                @if($user->bet_system && $user->currency)
                                    @php
                                        $sysColor     = $betSystemColor[$user->bet_system] ?? 'bg-secondary';
                                        $curLabel     = $user->currency === 'VND' ? 'Vietnamese Dong' : 'USD Dollar';
                                    @endphp
                                    <span class="badge {{ $sysColor }} bet-badge">
                                        Bet {{ ucfirst($user->bet_system) }} · {{ $curLabel }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
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
                "paging":    true,
                "searching": true,
                "ordering":  false,
                "responsive": true,
            });
        });
    </script>
    @endsection
</x-admin>
