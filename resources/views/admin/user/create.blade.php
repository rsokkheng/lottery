@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
@endphp

<x-admin>
    @section('title', 'Create Member')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Create Member</h3>
            <div class="card-tools">
                <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-dark">Back</a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.user.store') }}" method="POST">
                @csrf
                <div class="row">
                    {{-- Name --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">Name:*</label>
                            <input type="text" class="form-control" name="name" required autocomplete="off"
                                value="{{ old('name') }}">
                            <x-error>name</x-error>
                        </div>
                    </div>

                    {{-- Username --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">Account ID:*</label>
                            <input type="text" class="form-control" name="username" required autocomplete="off"
                                value="{{ old('username') }}">
                            <x-error>username</x-error>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">Password:*</label>
                            <input type="password" class="form-control" name="password" required autocomplete="off">
                            <x-error>password</x-error>
                        </div>
                    </div>

                    {{-- Phone Number --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">Phone Number:*</label>
                            <input type="number" class="form-control" name="phonenumber" required autocomplete="off"
                                value="{{ old('phonenumber') }}">
                            <x-error>phonenumber</x-error>
                        </div>
                    </div>

                    {{-- Role Selection --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">Role:*</label>
                            <select name="role" class="form-control" required>
                                @foreach ($roles as $role)
                                    @if (($user->hasRole('admin') && $role->name === 'manager') ||
                                         ($user->hasRole('manager') && $role->name === 'member'))
                                        <option value="{{ $role->name }}"
                                            {{ old('role') === $role->name ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <x-error>role</x-error>
                        </div>
                    </div>

                    {{-- Package Selection --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">Package:*</label>
                            <select name="package_id" class="form-control" required>
                                <option value="" disabled selected>Select the package</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}"
                                        {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                        {{ $package->package_code }}
                                    </option>
                                @endforeach
                            </select>
                            <x-error>package_id</x-error>
                        </div>
                    </div>

                    {{-- Credit --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">Give Credit:* (Available: 352,870.63)</label>
                            <input type="number" class="form-control" name="given_credit" required autocomplete="off"
                                value="{{ old('given_credit') }}">
                            <x-error>given_credit</x-error>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="col-lg-12">
                        <div class="float-right">
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin>
