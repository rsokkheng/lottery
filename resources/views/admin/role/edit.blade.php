<x-admin>
    @section('title', 'Edit Role')
    <section class="content">
        <!-- Default box -->
        <div class="d-flex justify-content-center">
            <div class="col-lg-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Role</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.role.index') }}" class="btn btn-sm btn-dark">Back</a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form action="{{ route('admin.role.update',$data) }}" method="POST">
                        @method('PUT')
                        @csrf
                   
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Role Name</label>
                                        <input type="text" class="form-control" name="name" id="name" required=""
                                            value="{{ $data->name }}">
                                        <x-error>name</x-error>
                                        <div class="invalid-feedback">Role name field is required.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Permissions Section -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="permissions" class="form-label">Permissions</label>
                                        <div class="row">
                                            @foreach($permissions as $group => $groupPermissions)
                                                <div class="col-md-4">
                                                    <strong>{{ ucfirst($group) }}</strong> <!-- Display group name -->
                                                    <!-- Select All Checkbox -->
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input select-all" data-group="{{ $group }}" id="select-all-{{ $group }}">
                                                        <label class="form-check-label" for="select-all-{{ $group }}">Select All</label>
                                                    </div>
                                                    <div class="checkbox">
                                                        @foreach($groupPermissions as $permission)
                                                            <div class="form-check">
                                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                                    @if($data->hasPermissionTo($permission->name)) checked @endif
                                                                    class="form-check-input permission-checkbox" data-group="{{ $group }}">
                                                                <label class="form-check-label">{{ $permission->name }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Permissions Section -->
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer float-end float-right">
                            <button type="submit" id="submit" class="btn btn-primary float-end float-right">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Add event listeners to the "Select All" checkboxes
                document.querySelectorAll('.select-all').forEach(function (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function () {
                        const group = selectAllCheckbox.getAttribute('data-group');
                        const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);

                        // Set the checked state of all checkboxes to match "Select All" checkbox
                        checkboxes.forEach(function (checkbox) {
                            checkbox.checked = selectAllCheckbox.checked;
                        });
                    });
                });

                // Optionally, check if all checkboxes in a group are selected and update the "Select All" checkbox
                document.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
                    checkbox.addEventListener('change', function () {
                        const group = checkbox.getAttribute('data-group');
                        const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
                        const selectAllCheckbox = document.querySelector(`#select-all-${group}`);
                        
                        // If all checkboxes are checked, select "Select All"
                        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                        selectAllCheckbox.checked = allChecked;
                    });
                });
            });
        </script>
    @endpush
</x-admin>
