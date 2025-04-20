<x-admin>
    @section('title', 'Edit Menu')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Menu</h3>
            <div class="card-tools"><a href="{{ route('admin.menu.index') }}" class="btn btn-sm btn-dark">Back</a></div>
        </div>
        <div class="card-body">
        <form action="{{ route('admin.menu.update', $betMenu) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $betMenu->id }}">

            <div class="row">
                {{-- Title --}}
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="title" class="form-label">Title:*</label>
                        <input type="text" name="title" class="form-control" required
                            value="{{ old('title', $betMenu->title) }}">
                        <x-error name="title" />
                    </div>
                </div>

                {{-- Text --}}
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="text" class="form-label">Text:*</label>
                        <input type="text" name="text" class="form-control" required
                            value="{{ old('text', $betMenu->text) }}">
                        <x-error name="text" />
                    </div>
                </div>

                {{-- Banner Image --}}
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="banner" class="form-label">Banner Image:</label>
                        <input type="file" name="banner" class="form-control" accept="image/*">
                        <x-error name="banner" />

                        @if ($betMenu->banner)
                            <img src="{{ asset('uploads/banners/' .$betMenu->banner) }}" alt="Current Banner" class="img-thumbnail mt-2" width="120">
                        @endif
                    </div>
                </div>

                {{-- Menu Image --}}
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="image" class="form-label">Image:</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <x-error name="image" />

                        @if ($betMenu->image)
                            <img src="{{ asset('uploads/images/' .$betMenu->image) }}" alt="Current Image" class="img-thumbnail mt-2" width="120">
                        @endif
                    </div>
                </div>

                {{-- Submit --}}
                <div class="col-lg-12">
                    <div class="float-end">
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </form>

        </div>
    </div>
</x-admin>
