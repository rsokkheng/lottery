<x-admin>
    @section('title','Edit Package')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Package</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.bet-lottery-package.index') }}" class="btn btn-info btn-sm">Back</a>
                        </div>
                    </div>
                    <form class="needs-validation" novalidate method="POST" action="{{ route('admin.bet-lottery-package.update', $data->id) }}">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">

                        <div class="form-group col-lg-12">
                            <label for="package_code">Package</label>
                            <input type="text" class="form-control" name="package_code"
                                required value="{{ $data->package_code }}" readonly>
                        </div>

                        <div class="form-group col-lg-12">
                            <label for="bet_type">Digit</label>
                            <input type="text" class="form-control" name="bet_type"
                                required value="{{ $data->bet_type }}" readonly>
                        </div>

                        <div class="form-group col-lg-12">
                            <label for="rate">Rate</label>
                            <input type="text" class="form-control" id="rate" name="rate"
                                required value="{{ $data->rate }}">
                            @error('rate') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group col-lg-12">
                            <label for="price">Price</label>
                            <input type="text" class="form-control" id="price" name="price"
                                required value="{{ $data->price }}">
                            @error('price') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary float-right">Update</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-admin>
