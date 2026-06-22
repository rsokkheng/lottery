@php
    use Illuminate\Support\Facades\Auth;
    $authUser  = Auth::user();
    $isAdmin   = $authUser->hasRole('admin');
    $editedUser = $user; // keep original variable before any potential @auth overwrite
@endphp

<x-admin>
    @section('title', 'Edit User')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Edit User</h3>
            <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-dark">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.user.update', $editedUser) }}" method="POST">
                @method('PUT')
                @csrf
                <input type="hidden" name="id" value="{{ $editedUser->id }}">
                <div class="row g-3">

                    {{-- Name --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required
                            value="{{ old('name', $editedUser->name) }}">
                        <x-error>name</x-error>
                    </div>

                    {{-- Account ID --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Account ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" required
                            value="{{ old('username', $editedUser->username) }}">
                        <x-error>username</x-error>
                    </div>

                    {{-- Phone --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="phonenumber" required
                            value="{{ old('phonenumber', $editedUser->phonenumber) }}">
                        <x-error>phonenumber</x-error>
                    </div>

                    {{-- Role --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <select name="role" id="roleSelect" class="form-control" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ old('role', $editedUser->roles->first()->name ?? '') === $role->name ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                </option>
                            @endforeach
                        </select>
                        <x-error>role</x-error>
                    </div>

                    {{-- Package --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Package <span class="text-danger">*</span></label>
                        <select name="package_id" class="form-control" required>
                            <option value="" disabled>— Select package —</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}"
                                    {{ $editedUser->package_id === $package->id ? 'selected' : '' }}>
                                    {{ $package->package_code }}
                                </option>
                            @endforeach
                        </select>
                        <x-error>package_id</x-error>
                    </div>

                    {{-- Credit --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">
                            Give Credit
                            <small class="text-muted">(current: {{ number_format($editedUser->total_bet_credit ?? 0, 2) }})</small>
                        </label>
                        <input type="number" class="form-control" id="amount_bet_credit"
                            name="available_credit" required autocomplete="off"
                            value="{{ old('available_credit', $editedUser->total_available_credit ?? 0) }}"
                            data-max="{{ $editedUser->total_bet_credit ?? 0 }}">
                        <small id="amount-error" class="text-danger d-none">
                            Amount cannot exceed the allowed limit.
                        </small>
                    </div>

                    @php
                        $firstEditOpt  = $betTypeOptions[0] ?? null;
                        $currentBtKey  = old('bet_types.0') ?? ($selectedBetTypes[0] ?? ($firstEditOpt ? $firstEditOpt['bet_system'].'_'.$firstEditOpt['currency'] : ''));
                        $currentCur    = $currentBtKey ? strtoupper(substr($currentBtKey, strrpos($currentBtKey, '_') + 1)) : ($firstEditOpt ? $firstEditOpt['currency'] : 'VND');
                        $currentCur    = old('currency', $currentCur);
                    @endphp
                    <input type="hidden" name="currency" id="hidden-currency" value="{{ $currentCur }}">

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
                                                    id="bt_edit_{{ $key }}"
                                                    {{ $currentBtKey === $key ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="bt_edit_{{ $key }}">
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
                                                id="bt_edit_{{ $key }}"
                                                {{ $currentBtKey === $key ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="bt_edit_{{ $key }}">
                                                {{ $opt['label'] }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary px-4" id="btn-save" type="submit">
                            <i class="fas fa-save me-1"></i> Save
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @section('js')
    <script>
        $(document).ready(function () {
            $('#amount_bet_credit').on('input', function () {
                var entered = parseFloat($(this).val()) || 0;
                var max     = parseFloat($(this).data('max')) || 0;
                $('#amount-error').toggleClass('d-none', entered <= max);
                $('#btn-save').prop('disabled', entered > max);
            });
        });
    </script>

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
