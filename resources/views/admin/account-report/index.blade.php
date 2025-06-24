
    <link href="{{ asset('admin/plugins/datepicker/css/bootstrap-datepicker-1-7-1.min.css') }}" rel="stylesheet"/>

<style>
    @media (max-width: 768px) {
        table td, table th {
            font-size: 11px !important;
            white-space: nowrap;
        }

        .btn {
            font-size: 11px;
            padding: 2px 6px;
        }
    }
    .datepicker-days {
            padding-top: 15px !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
</style>
<x-admin>
    @section('title', 'Transaction Report')
    <div class="card">
    <div class="mb-3" style="margin-top: 1px;" >
    <div class="card-body">
    <div class="row">
        <div class="col-md-3 mb-3">
            <input type="text" id="startDate"  value="{{ request('startDate') }}" data-date-format="dd/mm/yyyy" class="form-control" placeholder="Select start date">
        </div>
        <div class="col-md-3 mb-3">
            <input type="text" id="endDate" value="{{ request('endDate') }}"  data-date-format="dd/mm/yyyy" class="form-control" placeholder="Select end date">
        </div>
        <div class="col-md-1 mb-1">
            <button 
                class="w-full flex justify-center items-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 cursor-pointer"
                onclick="searchTransaction('{{ route('admin.account-report.index') }}')">
                <svg class="size-6 mr-2" viewBox="-2.64 -2.64 29.28 29.28" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M17.0392 15.6244C18.2714 14.084 19.0082 12.1301 19.0082 10.0041C19.0082 5.03127 14.9769 1 10.0041 1C5.03127 1 1 5.03127 1 10.0041C1 14.9769 5.03127 19.0082 10.0041 19.0082C12.1301 19.0082 14.084 18.2714 15.6244 17.0392L21.2921 22.707C21.6828 23.0977 22.3163 23.0977 22.707 22.707C23.0977 22.3163 23.0977 21.6828 22.707 21.2921L17.0392 15.6244ZM10.0041 17.0173C6.1308 17.0173 2.99087 13.8774 2.99087 10.0041C2.99087 6.1308 6.1308 2.99087 10.0041 2.99087C13.8774 2.99087 17.0173 6.1308 17.0173 10.0041C17.0173 13.8774 13.8774 17.0173 10.0041 17.0173Z" fill="#ffffff"></path>
                    </g>
                </svg>
                {{ __('Search') }}
            </button>
                </div>
    </div>
</div>
    </div>
        <div class="card-body">
            <table class="table table-striped" >
                <thead>
                    <tr style="font-size: 12px;">
                        <th>Account</th>
                        <th>Weekday</th>
                        <th>Date</th>
                        <th>Add By</th>
                        <th>Currency</th>
                        <th>Deposit</th>
                        <th>Withdraw</th>
                        <th>Adjustment</th>
                        <th>Balance</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $user)
                            <tr style="font-size: 13px;">
                                <td>{{ $user->name_user}}</td>
                                <td>{{ \Carbon\Carbon::parse($user->report_date)->format('l') }}</td>
                                <td>{{ $user->created_at }}</td>
                                <td>{{ $user->created_by }}</td>
                                <td>VND</td>
                                <td>{{ $user->total_deposit }}</td>
                                <td>
                                 @if ($user->total_withdraw > 0)
                                    <h6 style="color: red;">- {{ $user->total_withdraw }}</h6>
                                 @else
                                    {{ $user->total_withdraw }}
                                 @endif
                               </td>
                                <td>{{ $user->total_adjustment }}</td>
                                <td>
                                @if ( $user->total_balance < 0)
                                    <h6 style="color: red;">{{ $user->total_balance }}</h6>
                                 @else
                                 <h6 >{{ $user->total_balance }}</h6>
                                 @endif
                               </td>
                               <td>{{ $user->text}}</td>
                              
                            </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin>
<script>
    $(function(){
        
        $('#startDate').datepicker({
            format: 'dd/mm/yyyy', // Customize the date format as needed
            autoclose: true,
            todayHighlight: true,
            endDate: '+0d',
        });
        $('#endDate').datepicker({
            format: 'dd/mm/yyyy', // Customize the date format as needed
            autoclose: true,
            todayHighlight: true,
            endDate: '+0d',
        });
       
    });
    function searchTransaction(url){
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        if (startDate.length && endDate.length) {
            window.location = url + '?startDate=' + startDate + '&endDate=' + endDate;
        } else {
            alert('Please select both start and end dates.');
        }
    }
</script>