<x-admin>
    @section('title', 'Create User')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Create User</h3>
            <div class="card-tools"><a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-dark">Back</a></div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.user.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Name:*</label>
                            <input type="text" class="form-control" autocomplete="off" name="name" required
                                value="{{ old('name') }}">
                                <x-error>name</x-error>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="Username" class="form-label">Username:*</label>
                            <input type="username" class="form-control" autocomplete="off" name="username" required
                                value="{{ old('username') }}">
                                <x-error>username</x-error>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="Password"  class="form-label">Password:*</label>
                            <input type="password" class="form-control" autocomplete="off" name="password" required>
                            <x-error>password</x-error>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="PhoneNumber" class="form-label">Phone Number:*</label>
                            <input type="number" class="form-control"  autocomplete="off" name="phonenumber" required
                                value="{{ old('phonenumber') }}">
                                <x-error>phonenumber</x-error>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="role" class="form-label">Role:*</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="" selected disabled>selecte the role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ $role->name == old('role') ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <x-error>role</x-error>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="package" class="form-label">Package:*</label>
                            <select name="package_id" id="package_id" class="form-control" required>
                                <option value="" selected disabled>selecte the Package</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}"
                                        {{ $package->package_code == old('package') ? 'selected' : '' }}>{{ $package->package_code }}</option>
                                @endforeach
                            </select>
                            <x-error>package</x-error>
                        </div>
                    </div>
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
