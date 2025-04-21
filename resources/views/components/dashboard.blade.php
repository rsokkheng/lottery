<div class="row">
   
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $user }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="{{ route('admin.user.index') }}" class="small-box-footer">View <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Bets</h3>
                    <p>Bets</p>
                </div>
                <div class="icon">
                    <i class="fas fa-list-alt"></i>
                </div>
                <a href="{{ url('lotto_vn/bet') }}" class="small-box-footer">View <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

</div>
