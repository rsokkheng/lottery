@php
    use Illuminate\Support\Facades\Auth;
    $creator = Auth::user();
    $isAdmin = $creator->hasRole('admin');

    $roleLabels = [
        'master'   => 'Master',
        'agent'    => 'Agent',
        'member'   => 'Member',
        'operator' => 'Operator',
        'finance'  => 'Finance',
        'support'  => 'Support',
        'auditor'  => 'Auditor',
    ];

    // Default selected bet type key and its derived currency
    $firstOpt      = $betTypeOptions[0] ?? null;
    $defaultBt     = old('bet_types.0') ?? ($firstOpt ? $firstOpt['currency'] : '');
    $defaultCur    = old('currency', $firstOpt ? $firstOpt['currency'] : '');
    $backOfficeRoles = $backOfficeRoles ?? ['operator', 'finance', 'support', 'auditor'];
    $backOfficeJson  = json_encode($backOfficeRoles);
@endphp

<x-admin>
    @section('title', 'Create User')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-user-plus me-1"></i> Create User</h3>
            <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-dark">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.user.store') }}" method="POST">
                @csrf
                <div class="row g-3">

                    {{-- Name --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required autocomplete="off"
                            value="{{ old('name') }}">
                        <x-error>name</x-error>
                    </div>

                    {{-- Account ID --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Account ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" required autocomplete="off"
                            value="{{ old('username') }}">
                        <x-error>username</x-error>
                    </div>

                    {{-- Password --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" required autocomplete="new-password">
                        <x-error>password</x-error>
                    </div>

                    {{-- Phone --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phonenumber" autocomplete="off"
                            value="{{ old('phonenumber') }}">
                        <x-error>phonenumber</x-error>
                    </div>

                    {{-- Role --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <select name="role" id="roleSelect" class="form-control" required>
                            <option value="" disabled selected>— Select role —</option>
                            @php
                                $bettingNames     = ['master', 'agent', 'member'];
                                $bettingRoles     = $roles->whereIn('name', $bettingNames);
                                $backOfficeInForm = $roles->whereIn('name', $backOfficeRoles);
                            @endphp
                            @if($bettingRoles->isNotEmpty())
                                <optgroup label="Betting Network">
                                    @foreach($bettingRoles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                            {{ $roleLabels[$role->name] ?? ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($backOfficeInForm->isNotEmpty())
                                <optgroup label="Back Office">
                                    @foreach($backOfficeInForm as $role)
                                        <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                            {{ $roleLabels[$role->name] ?? ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        <x-error>role</x-error>
                    </div>

                    {{-- Betting-only fields (hidden for back-office roles) --}}
                    <div id="bettingFields" class="col-12">
                        <div class="row g-3">

                            {{-- Package --}}
                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">Package <span class="text-danger">*</span></label>
                                <select name="package_id" id="packageSelect" class="form-control">
                                    <option value="" disabled selected>— Select package —</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->id }}"
                                            {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                            {{ $package->package_code }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-error>package_id</x-error>
                            </div>

                            <input type="hidden" name="currency" id="hidden-currency" value="{{ $defaultCur }}">

                            {{-- Initial Credit --}}
                            <div class="col-lg-6">
                                <label class="form-label fw-semibold">Initial Credit</label>
                                <input type="number" class="form-control" name="available_credit"
                                    value="{{ old('available_credit', 0) }}" min="0" step="0.01">
                            </div>

                            {{-- Bet Type Assignment --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-dice me-1 text-primary"></i>
                                    Bet Types
                                </label>
                                <div class="border rounded p-3 bg-light">
                                    <div class="row g-2">
                                        @foreach($betTypeOptions as $opt)
                                        @php $key = $opt['currency']; @endphp
                                        <div class="col-6 col-md-4">
                                            <div class="form-check form-check-lg border rounded p-2 bg-white h-100">
                                                <input class="form-check-input" type="radio"
                                                    name="bet_types[]" value="{{ $key }}"
                                                    id="bt_create_{{ $key }}"
                                                    {{ (old('bet_types.0', $defaultBt) === $key) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="bt_create_{{ $key }}">
                                                    {{ $opt['label'] }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <x-error>bet_types</x-error>
                            </div>

                        </div>
                    </div>

                    {{-- Back-office info notice (shown when back-office role selected) --}}
                    <div id="backOfficeNotice" class="col-12" style="display:none;">
                        <div class="alert alert-info mb-0" style="font-size:.875rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            Back-office users do not require a betting package or bet type. They access the system through the admin panel only.
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="fas fa-save me-1"></i> Save
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @section('js')
    <script>
    (function () {
        var BACK_OFFICE = {!! $backOfficeJson !!};
        var roleSelect       = document.getElementById('roleSelect');
        var bettingFields    = document.getElementById('bettingFields');
        var backOfficeNotice = document.getElementById('backOfficeNotice');
        var packageSelect    = document.getElementById('packageSelect');

        function toggleBettingFields(roleName) {
            var isBO = BACK_OFFICE.indexOf(roleName) !== -1;
            bettingFields.style.display    = isBO ? 'none' : '';
            backOfficeNotice.style.display = isBO ? '' : 'none';
            if (packageSelect) packageSelect.required = !isBO;
            if (isBO) {
                document.querySelectorAll('input[name="bet_types[]"]').forEach(function(r) { r.checked = false; });
            } else {
                var anyChecked = document.querySelector('input[name="bet_types[]"]:checked');
                if (!anyChecked) {
                    var first = document.querySelector('input[name="bet_types[]"]');
                    if (first) { first.checked = true; syncCurrencyFromBetType(first.value); }
                }
            }
        }

        roleSelect.addEventListener('change', function () {
            toggleBettingFields(this.value);
        });

        // Init on page load (handles old() repopulation)
        if (roleSelect.value) toggleBettingFields(roleSelect.value);

        function syncCurrencyFromBetType(val) {
            var cur = val.split('_').pop();
            var el = document.getElementById('hidden-currency');
            if (el) el.value = cur;
        }
        document.querySelectorAll('input[name="bet_types[]"]').forEach(function (r) {
            r.addEventListener('change', function () { syncCurrencyFromBetType(this.value); });
        });
        var initBt = document.querySelector('input[name="bet_types[]"]:checked');
        if (initBt) syncCurrencyFromBetType(initBt.value);
    })();
    </script>
    @endsection

</x-admin>
