<x-admin>
    @section('title', 'Suspend User')
    <div class="card">
        <div class="card-header">
        <h3 class="card-title" style="font-weight: 600; font-size:22">Suspend User : <span class="text-black font-bold" style="color:#007bff;font-size:26px">{{ $user->name }}</span></h3>

            <div class="card-tools"><a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-dark">Back</a></div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.user.process-suspend',$user) }}" method="POST">
                @method('POST')
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
                             <label for="Your Password" class="form-label">Select Status:*</label>
                            <select class="form-control" name="suspend_status" required>
                                    <option value="" disabled selected>-- Select Status --</option>
                                    <option value="0">Suspend</option>
                                    <option value="1">Active</option>
                            </select>
                            @error('suspend_status')
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
