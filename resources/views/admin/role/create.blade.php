<x-admin>
    @section('title','Create Role')
    <section class="content">
        <!-- Default box -->
        <div class="d-flex justify-content-center">
            <div class="col-lg-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Create New Role</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.role.index') }}"
                                class="btn btn-sm btn-dark">Back</a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form action="{{ route('admin.role.store') }}" method="POST"
                        class="needs-validation" novalidate="">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Role Name</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            required="" value="{{ old('name') }}">
                                            <x-error>name</x-error>
                                        <div class="invalid-feedback">Role name field is required.</div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="permissions">Permissions</label>
                                        <div class="row">
                                            @foreach($permissions as $group => $groupPermissions)
                                                <div class="col-md-4">
                                                    <strong>{{ ucfirst($group) }}</strong> <!-- Display the group name -->
                                                    
                                                    <!-- Select All Checkbox -->
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input select-all" data-group="{{ $group }}" id="select_all_{{ $group }}">
                                                        <label class="form-check-label" for="select_all_{{ $group }}">Select All</label>
                                                    </div>

                                                    <!-- Individual Permission Checkboxes -->
                                                    <div class="checkbox">
                                                        @foreach($groupPermissions as $permission)
                                                            <div class="form-check">
                                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                                    @if(isset($data) && $data->hasPermissionTo($permission->name)) checked @endif
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
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer float-end float-right">
                            <button type="submit" id="submit"
                                class="btn btn-primary float-end float-right">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.card -->

    </section>
</x-admin>
<script>
    // When the "Select All" checkbox is clicked
    $(document).on('change', '.select-all', function() {
        var group = $(this).data('group'); // Get the group of the "Select All" checkbox
        var isChecked = $(this).prop('checked'); // Check if "Select All" is checked

        // Find all checkboxes in the same group and toggle their checked state
        $('.permission-checkbox[data-group="' + group + '"]').prop('checked', isChecked);
    });

    // Optionally, check if all checkboxes in a group are selected and update the "Select All" checkbox
    $(document).on('change', '.permission-checkbox', function() {
        var group = $(this).data('group');
        var allChecked = $('.permission-checkbox[data-group="' + group + '"]:checked').length === $('.permission-checkbox[data-group="' + group + '"]').length;

        // Set the "Select All" checkbox to be checked if all individual checkboxes are selected
        $('#select_all_' + group).prop('checked', allChecked);
    });
</script>

