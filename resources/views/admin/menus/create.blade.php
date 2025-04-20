<x-admin>
    @section('title', 'Create User')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Create Menus</h3>
            <div class="card-tools"><a href="{{ route('admin.menu.index') }}" class="btn btn-sm btn-dark">Back</a></div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                <div class="row">
                    {{-- Title --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="title" class="form-label">Title:*</label>
                            <input type="text" class="form-control" name="title" required value="{{ old('title') }}">
                            <x-error name="title" />
                        </div>
                    </div>

                    {{-- Text --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="text" class="form-label">Text:</label>
                            <input type="text" class="form-control" name="text" value="{{ old('text') }}">
                            <x-error name="text" />
                        </div>
                    </div>

                    {{-- Banner Image --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="banner" class="form-label">Banner Image:</label>
                            <input type="file" class="form-control" name="banner" accept="image/*">
                            <x-error name="banner" />
                        </div>
                    </div>

                    {{-- Menu Image --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="image" class="form-label">Image:</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <x-error name="image" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>

            </form>
        </div>
    </div>
</x-admin>
