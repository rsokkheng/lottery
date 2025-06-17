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
                    <form class="needs-validation" novalidate action="{{ route('admin.bet-lottery-package.update',$data) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <div class="col-lg-12">
                        <label for="name">Package</label>
                        <input type="text" class="form-control"
                            required value="{{ $data->package_id==1 ? 'AA' : 'AJ' }}" readonly>
                        </div>
                        <div class="col-lg-12">
                        <label for="name">Digit</label>
                        <input type="text" class="form-control"
                           equired value="{{ $data->bet_type }}" readonly>
                        </div>
                        <div class="col-lg-12">
                        <label for="name">Rate</label>
                        <input type="text" class="form-control" id="rate" name="rate"
                            required value="{{ $data->rate }}" >
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="name">Price</label>
                                <input type="text" class="form-control" id="price" name="price"
                                    placeholder="Enter Packagename" required value="{{ $data->price }}">
                            </div>
                            <x-error>name</x-error>
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
