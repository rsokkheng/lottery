<x-admin>
    @section('title','Lottery Package')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lottery Package</h3>
            <div class="card-tools">
                <a href="{{ route('admin.bet-lottery-package.create') }}" class="btn btn-sm btn-info">New</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped" id="categoryTable">
                <thead>
                    <tr>
                        <th>Package</th>
                        <th>Bet Type</th>
                        <th>Bet Term</th>
                        <th>Rate</th>
                        <th>Price</th>
                        <th>Action</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $cat)
                    @foreach ($cat->packageConfiges as $pack)
                        <tr>
                            <td>{{ $cat->package_code }}</td>
                            <td>{{ $pack->bet_type }}</td>
                            <td>{{ $pack->bet_pattern }}</td>
                            <td>{{ $pack->rate }}</td>
                            <td>{{ $pack->price }}</td>
                            <td><a href="{{ route('admin.bet-lottery-package.edit', encrypt($cat->id)) }}"
                                    class="btn btn-sm btn-primary" style="display: inline-block; margin-right: 5px;">Edit</a>
                                <form action="{{ route('admin.bet-lottery-package.destroy', encrypt($cat->id)) }}" method="POST"
                                    onsubmit="return confirm('Are sure want to delete?')" style="display: inline-block;">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @section('js')
        <script>
            $(function() {
                $('#categoryTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "responsive": true,
                });
            });
        </script>
    @endsection
</x-admin>
