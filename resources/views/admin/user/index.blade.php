@php
    use Illuminate\Support\Facades\Auth;
    $auth = Auth::user();
    $supervisorRoles = ['admin', 'master', 'agent'];
    $canManage     = $auth->hasAnyRole($supervisorRoles);
    $isAdmin       = $auth->hasRole('admin');
    $isMaster      = $auth->hasRole('master');
    $showHierarchy = $isAdmin || $isMaster;

    $roleMeta = [
        'admin'    => ['color'=>'#1e293b', 'bg'=>'#e2e8f0', 'icon'=>'fa-shield-alt'],
        'master'   => ['color'=>'#5b21b6', 'bg'=>'#ede9fe', 'icon'=>'fa-crown'],
        'agent'    => ['color'=>'#0369a1', 'bg'=>'#e0f2fe', 'icon'=>'fa-user-tie'],
        'member'   => ['color'=>'#374151', 'bg'=>'#f3f4f6', 'icon'=>'fa-user'],
        'operator' => ['color'=>'#065f46', 'bg'=>'#d1fae5', 'icon'=>'fa-tools'],
        'finance'  => ['color'=>'#92400e', 'bg'=>'#fef3c7', 'icon'=>'fa-calculator'],
        'support'  => ['color'=>'#1d4ed8', 'bg'=>'#dbeafe', 'icon'=>'fa-headset'],
        'auditor'  => ['color'=>'#7c3aed', 'bg'=>'#ede9fe', 'icon'=>'fa-search'],
    ];

    $totalUsers  = $data->count();
    $activeUsers = $data->filter(fn($u) => $u->is_active)->count();
@endphp
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* ── Stat cards ── */
    .cr-stat { border-radius:14px; padding:16px 18px; display:flex; align-items:center; gap:14px; box-shadow:0 2px 12px rgba(0,0,0,.07); }
    .cr-stat-icon { width:46px; height:46px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:1.25rem; flex-shrink:0; }
    .cr-stat-label { font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; margin-bottom:2px; }
    .cr-stat-value { font-size:1.4rem; font-weight:700; line-height:1.1; }

    /* ── Table ── */
    .cr-table-wrap { border-radius:12px; overflow:visible; border:1px solid #e8ecf0; }
    #userTable { margin-bottom:0 !important; font-size:.845rem; }
    #userTable thead th {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color:#fff; font-weight:600; font-size:.75rem; text-transform:uppercase;
        letter-spacing:.5px; border:none; padding:11px 12px; white-space:nowrap;
    }
    #userTable tbody td { padding:10px 12px; vertical-align:middle; border-color:#f0f2f5; }
    #userTable tbody tr:hover { background:#f0f5ff; }

    /* ── Role pill ── */
    .role-pill { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; text-decoration:none; transition:opacity .15s; }
    .role-pill:hover { opacity:.8; }

    /* ── System badges ── */
    .sys-vn  { background:#dcfce7; color:#166534; }
    .sys-kh  { background:#fee2e2; color:#991b1b; }
    .cur-vnd { background:#e0f2fe; color:#0c4a6e; }
    .cur-usd { background:#d1fae5; color:#064e3b; }

    /* ── Actions dropdown ── */
    .act-toggle { font-size:.78rem; font-weight:700; padding:4px 12px; border-radius:8px;
                  background:#f1f5f9; color:#374151; border:1px solid #cbd5e1; transition:all .15s; }
    .act-toggle:hover, .act-toggle:focus { background:#334155; color:#fff; border-color:#334155; }
    .act-toggle::after { margin-left:4px; }
    .act-menu { border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.13); border:1px solid #e2e8f0;
                padding:4px 0; min-width:160px; }
    .act-menu .dropdown-item { font-size:.82rem; font-weight:600; padding:7px 14px; display:flex; align-items:center; gap:8px; }
    .act-menu .dropdown-item:hover { background:#f1f5f9; }
    .act-menu .dropdown-item.text-danger:hover { background:#fef2f2; }
    .act-menu .dropdown-item button { background:none; border:none; padding:0; font-size:.82rem;
                                      font-weight:600; color:#dc2626; width:100%; text-align:left;
                                      display:flex; align-items:center; gap:8px; }
    .act-menu .dropdown-item button:hover { background:none; }

    /* ── DataTables ── */
    .dataTables_wrapper .dataTables_filter input { border-radius:8px; border:1px solid #d1d8e0; padding:5px 10px; font-size:.82rem; }
    .dataTables_wrapper .dataTables_length select { border-radius:8px; border:1px solid #d1d8e0; padding:4px 8px; font-size:.82rem; }

    @media (max-width:768px) {
        #userTable td, #userTable th { font-size:.73rem; white-space:nowrap; }
    }
</style>

<x-admin>
    @section('title', 'Account Management')

    <div>
        {{-- ── Summary Cards ── --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="cr-stat bg-white">
                    <div class="cr-stat-icon" style="background:#e8f0fe">
                        <i class="fas fa-users" style="color:#1a73e8"></i>
                    </div>
                    <div>
                        <div class="cr-stat-label">Total Accounts</div>
                        <div class="cr-stat-value" style="color:#1a73e8">{{ $totalUsers }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="cr-stat bg-white">
                    <div class="cr-stat-icon" style="background:#e8f5e9">
                        <i class="fas fa-check-circle" style="color:#2e7d32"></i>
                    </div>
                    <div>
                        <div class="cr-stat-label">Active</div>
                        <div class="cr-stat-value" style="color:#2e7d32">{{ $activeUsers }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="cr-stat bg-white">
                    <div class="cr-stat-icon" style="background:#fce4ec">
                        <i class="fas fa-ban" style="color:#c62828"></i>
                    </div>
                    <div>
                        <div class="cr-stat-label">Suspended</div>
                        <div class="cr-stat-value" style="color:#c62828">{{ $totalUsers - $activeUsers }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="cr-stat bg-white">
                    <div class="cr-stat-icon" style="background:#e3f2fd">
                        <i class="fas fa-user" style="color:#1565c0"></i>
                    </div>
                    <div>
                        <div class="cr-stat-label">Members</div>
                        <div class="cr-stat-value" style="color:#1565c0">
                            {{ $data->filter(fn($u) => $u->roles->first()?->name === 'member')->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Table Card ── --}}
        <div class="bg-white rounded-3 shadow-sm" style="border:1px solid #e8ecf0;">

            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid #e8ecf0;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#1e293b,#334155);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-users text-white" style="font-size:.9rem"></i>
                    </div>
                    <div>
                        <div style="font-size:.95rem;font-weight:700;color:#1e293b;">Account Management</div>
                        <div style="font-size:.72rem;color:#6c757d;">All system accounts &amp; roles</div>
                    </div>
                </div>
                @if($canManage)
                <a href="{{ route('admin.user.create') }}" class="btn btn-primary btn-sm" style="border-radius:9px;font-weight:600;padding:6px 16px;">
                    <i class="fas fa-plus me-1"></i>Add Account
                </a>
                @endif
            </div>

            <div class="p-3">
                <div class="cr-table-wrap">
                    <table id="userTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width:3%">#</th>
                                <th>Role</th>
                                <th>Account ID</th>
                                <th>Full Name</th>
                                <th>System</th>
                                @if($showHierarchy)
                                    <th>{{ $isAdmin ? 'Master / Agent' : 'Agent' }}</th>
                                @endif
                                <th>Registered</th>
                                <th class="text-center">Status</th>
                                @if($canManage)
                                    <th class="text-center" style="min-width:260px">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $user)
                                @php
                                    $roleName = $user->roles->first()?->name ?? 'member';
                                    $meta     = $roleMeta[$roleName] ?? $roleMeta['member'];
                                @endphp
                                <tr>
                                    <td class="text-muted" style="font-size:.75rem;">{{ $key + 1 }}</td>
                                    <td>
                                        @foreach ($user->roles as $role)
                                            @php $m = $roleMeta[$role->name] ?? $roleMeta['member']; @endphp
                                            @if (in_array($role->name, ['master', 'agent']))
                                                <a href="{{ route('admin.user.under-manager', ['manager_id' => $user->id]) }}"
                                                   title="View members under {{ $user->name }}" class="role-pill text-decoration-none"
                                                   style="color:{{ $m['color'] }};background:{{ $m['bg'] }};">
                                                    <i class="fas {{ $m['icon'] }}" style="font-size:.65rem;"></i>
                                                    {{ ucfirst($role->name) }}
                                                </a>
                                            @else
                                                <span class="role-pill" style="color:{{ $m['color'] }};background:{{ $m['bg'] }};">
                                                    <i class="fas {{ $m['icon'] }}" style="font-size:.65rem;"></i>
                                                    {{ ucfirst($role->name) }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        <span style="font-family:monospace;font-weight:600;font-size:.85rem;color:#1e293b;">{{ $user->username }}</span>
                                    </td>
                                    <td style="font-weight:500;">{{ $user->name }}</td>
                                    <td>
                                        @if($user->currency)
                                            <span class="role-pill sys-vn" style="margin-bottom:2px;display:inline-flex;">
                                                Lotto (Vietnam &amp; Cambodia)
                                            </span>
                                            <span class="role-pill {{ $user->currency === 'USD' ? 'cur-usd' : 'cur-vnd' }}" style="display:inline-flex;">
                                                {{ $user->currency }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    @if($showHierarchy)
                                        <td>
                                            @if($isAdmin && $user->master)
                                                <span class="role-pill" style="color:#5b21b6;background:#ede9fe;margin-bottom:2px;display:inline-flex;">
                                                    <i class="fas fa-crown" style="font-size:.6rem;"></i>{{ $user->master->name }}
                                                </span>
                                            @endif
                                            @if($user->manager && ($isAdmin ? $user->manager->id !== $user->master?->id : true))
                                                <span class="role-pill" style="color:#0369a1;background:#e0f2fe;display:inline-flex;">
                                                    <i class="fas fa-user-tie" style="font-size:.6rem;"></i>{{ $user->manager->name }}
                                                </span>
                                            @endif
                                            @if(!$user->master && !$user->manager)
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endif

                                    <td class="text-muted" style="font-size:.8rem;">
                                        {{ $user->created_at?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="text-center">
                                        @if($user->is_active)
                                            <span class="badge rounded-pill bg-success" style="font-size:.7rem;">● Active</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger" style="font-size:.7rem;">● Suspended</span>
                                        @endif
                                    </td>
                                    @if($canManage)
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn act-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu act-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.user.edit', encrypt($user->id)) }}">
                                                        <i class="fas fa-edit text-primary"></i>Edit
                                                    </a>
                                                </li>
                                                @if(($auth->hasRole('admin') || $auth->hasRole('master')) && $user->package_id)
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.user.package-view-lotto', encrypt($user->id)) }}">
                                                        <i class="fas fa-box text-success"></i>Package
                                                    </a>
                                                </li>
                                                @endif
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.user.show', $user->id) }}">
                                                        <i class="fas fa-sliders-h text-warning"></i>Settings
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.user.change-password', $user->id) }}">
                                                        <i class="fas fa-key text-secondary"></i>Password
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.user.suspend', $user->id) }}">
                                                        <i class="fas fa-ban text-warning"></i>{{ $user->is_active ? 'Suspend' : 'Activate' }}
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <span class="dropdown-item text-danger p-0">
                                                        <form action="{{ route('admin.user.destroy', encrypt($user->id)) }}" method="POST"
                                                              onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit">
                                                                <i class="fas fa-trash"></i>Delete
                                                            </button>
                                                        </form>
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(function () {
            $('#userTable').DataTable({
                paging: true, searching: true, ordering: true,
                responsive: true, autoWidth: false,
                language: { search: '', searchPlaceholder: 'Search account…' },
                columnDefs: [{ orderable: false, targets: -1 }]
            });
        });
    </script>
    @endsection
</x-admin>
