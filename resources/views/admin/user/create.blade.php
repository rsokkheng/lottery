@php
    use Illuminate\Support\Facades\Auth;
    $creator = Auth::user();
    $isAdmin = $creator->hasRole('admin');

    $roleLabels = [
        'master' => 'Master',
        'agent'  => 'Agent',
        'member' => 'Member',
    ];

    // Default selected bet type key and its derived currency
    $firstOpt      = $betTypeOptions[0] ?? null;
    $defaultBt     = old('bet_types.0') ?? ($firstOpt ? $firstOpt['bet_system'].'_'.$firstOpt['currency'] : '');
    $defaultCur    = old('currency', $firstOpt ? $firstOpt['currency'] : '');
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
                        <input type="text" class="form-control" name="phonenumber" required autocomplete="off"
                            value="{{ old('phonenumber') }}">
                        <x-error>phonenumber</x-error>
                    </div>

                    {{-- Role --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <select name="role" id="roleSelect" class="form-control" required>
                            <option value="" disabled selected>— Select role —</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ old('role') === $role->name ? 'selected' : '' }}>
                                    {{ $roleLabels[$role->name] ?? ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        <x-error>role</x-error>
                    </div>

                    {{-- Package --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Package <span class="text-danger">*</span></label>
                        <select name="package_id" class="form-control" required>
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
                            @if($isAdmin)
                                {{-- Admin: grouped by system (Bet Vietnam / Bet Khmer), radio, currency auto-follows --}}
                                @php $btGroups = collect($betTypeOptions)->groupBy('bet_system'); @endphp
                                @foreach($btGroups as $system => $opts)
                                <div class="{{ !$loop->last ? 'mb-3' : '' }}">
                                    <div class="fw-bold mb-2" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;color:#6c757d">
                                        Bet {{ ucfirst($system) }}
                                    </div>
                                    <div class="row g-2">
                                        @foreach($opts as $opt)
                                        @php $key = $opt['bet_system'] . '_' . $opt['currency']; @endphp
                                        <div class="col-6 col-md-3">
                                            <div class="form-check form-check-lg border rounded p-2 bg-white h-100">
                                                <input class="form-check-input" type="radio"
                                                    name="bet_types[]" value="{{ $key }}"
                                                    id="bt_create_{{ $key }}"
                                                    {{ $defaultBt === $key ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="bt_create_{{ $key }}">
                                                    {{ $opt['label'] }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            @else
                                {{-- Non-admin: flat list, single selection --}}
                                <div class="row g-2">
                                    @foreach($betTypeOptions as $opt)
                                    @php $key = $opt['bet_system'] . '_' . $opt['currency']; @endphp
                                    <div class="col-6 col-md-3">
                                        <div class="form-check form-check-lg border rounded p-2 bg-white h-100">
                                            <input class="form-check-input" type="radio"
                                                name="bet_types[]" value="{{ $key }}"
                                                id="bt_create_{{ $key }}"
                                                {{ $defaultBt === $key ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="bt_create_{{ $key }}">
                                                {{ $opt['label'] }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <x-error>bet_types</x-error>
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
