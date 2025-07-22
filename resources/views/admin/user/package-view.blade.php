<x-admin>
    @section('title', 'Package')
    <div class="card">
        <div class="card-header">
        <div class="row">
            <div class="col-md-5">
            <h3 class="card-title" style="font-weight: 600; font-size:22">Setting: <span class="text-black font-bold" style="color:#007bff;font-size:26px">{{ $package->username }}</span></h3>
            </div>
            <div class="col-md-5">
            <h3 class="card-title" style="font-weight: 600; font-size:22">Package:  <span class="text-black font-bold" style="color:#007bff;font-size:26px">{{ $bpCode }}</span></h3>
            </div>
            <div class="col-md-2">
            <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-light">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
        </div>
       
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>2D</th>
                        <th>3D</th>
                        <th>4D</th>
                        <th>PL2</th>
                        <th>PL3</th>
                    </tr>
                    <tr>
                        @foreach (['2D', '3D', '4D', 'PL2', 'PL3'] as $type)
                            <th>Rate / Price</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grouped = collect($data)->keyBy('bet_type');
                    @endphp
                    <tr>
                        @foreach (['2D', '3D', '4D', 'PL2', 'PL3'] as $type)
                            @if ($grouped->has($type))
                                
                                <td>
                                    {{ $grouped[$type]['bet_rate'] }} / {{ number_format($grouped[$type]['bet_price'], 2) }}
                                </td>
                            @else
                                <td>-</td>
                            @endif
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-admin>
