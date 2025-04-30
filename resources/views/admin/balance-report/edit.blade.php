<x-admin>
    @section('title', 'Edit User')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit User</h3>
            <div class="card-tools"><a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-dark">Back</a></div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.user.update',$user) }}" method="POST">
                @method('PUT')
                @csrf
                <input type="hidden" name="id" value="{{ $user->id }}">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Name:*</label>
                            <input type="text" class="form-control" name="name" required
                                value="{{ $user->name }}">
                                <x-error>name</x-error>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="Username" class="form-label">Account ID:*</label>
                            <input type="username" class="form-control" name="username" required
                                value="{{ $user->username}}">
                                <x-error>Account ID</x-error>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="PhoneNumber" class="form-label">Phone Number:*</label>
                            <input type="number" class="form-control" name="phonenumber" required
                                value="{{ $user->phonenumber}}">
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
                                        {{ $user->roles[0]['name'] === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <x-error>role</x-error>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="role" class="form-label">Package:*</label>
                            <select name="package_id" id="package_id" class="form-control" required>
                                <option value="" selected disabled>selecte the Package</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}"
                                        {{ $user->package_id === $package->id ? 'selected' : '' }}>{{ $package->package_code }}</option>
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
