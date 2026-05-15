@php
    use Illuminate\Support\Facades\Auth;
    $creator = Auth::user();
    $hasVND  = $creator->currencies()->where('currency', 'VND')->exists();
    $hasUSD  = $creator->currencies()->where('currency', 'USD')->exists();
    $hasKHR  = $creator->currencies()->where('currency', 'KHR')->exists();

    $roleLabels = [
        'master'       => 'Master Agent',
        'senior'       => 'Senior Agent',
        'manager'      => 'Manager',
        'share_master' => 'Share Master',
        'member'       => 'Member / Staff',
    ];
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

                    {{-- Username --}}
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

                    {{-- Phone Number --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phonenumber" required autocomplete="off"
                            value="{{ old('phonenumber') }}">
                        <x-error>phonenumber</x-error>
                    </div>

                    {{-- Role --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-control" required>
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

                    {{-- Currency --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Currency <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3 mt-1">
                            @if($hasVND)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="currency" id="currVND"
                                    value="VND" {{ old('currency', 'VND') === 'VND' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="currVND">VND</label>
                            </div>
                            @endif
                            @if($hasUSD)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="currency" id="currUSD"
                                    value="USD" {{ old('currency') === 'USD' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="currUSD">USD</label>
                            </div>
                            @endif
                            @if($hasKHR)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="currency" id="currKHR"
                                    value="KHR" {{ old('currency') === 'KHR' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="currKHR">KHR</label>
                            </div>
                            @endif
                        </div>
                        <x-error>currency</x-error>
                    </div>

                    {{-- Give Credit (read-only for now) --}}
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Initial Credit</label>
                        <input type="number" class="form-control" name="available_credit"
                            value="{{ old('available_credit', 0) }}" min="0" step="0.01">
                        <x-error>available_credit</x-error>
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
</x-admin>
