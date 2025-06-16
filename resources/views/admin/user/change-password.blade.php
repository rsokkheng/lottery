<x-admin>
    @section('title', 'Change Password')
    <div class="card">
        <div class="card-header">
        <h3 class="card-title" style="font-weight: 600; font-size:22">Change Password : <span class="text-black font-bold" style="color:#007bff;font-size:26px">{{ $user->name }}</span></h3>

            <div class="card-tools"><a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-dark">Back</a></div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.user.update-password',$user) }}" method="POST">
                @method('PUT')
                @csrf
                <input type="hidden" name="id" value="{{ $user->id }}">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="Your Password" class="form-label">Your Password:*</label>
                            <input type="password" class="form-control" name="your_password" required>
                            @error('your_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="New Password" class="form-label">New Password:*</label>
                            <input type="password" class="form-control" name="new_password" required>
                            @error('new_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
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
