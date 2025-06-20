<div class="card custom--card mb-4">
    <div class="card-body">
        <div class="widget-card-inner">
            <div class="widget-card bg--success">
                <a href="{{ url()->route('vendor.deposit.successful',request()->all()) }}" class="widget-card-link"></a>
                <div class="widget-card-left">
                    <div class="widget-card-icon">
                        <i class="las la-check-circle"></i>
                    </div>
                    <div class="widget-card-content">
                        <h6 class="widget-card-amount">{{ showAmount($successful) }}</h6>
                        <p class="widget-card-title">@lang('Total Wallet Amount')</p>
                    </div>
                </div>
                <span class="widget-card-arrow">
                    <i class="las la-angle-right"></i>
                </span>
            </div>

            <div class="widget-card bg--info">
                <a href="{{ url()->route('vendor.deposit.pending',request()->all()) }}" class="widget-card-link"></a>
                <div class="widget-card-left">
                    <div class="widget-card-icon">
                    <i class="la la-money-check-alt"></i>
                    </div>
                    <div class="widget-card-content">
                        <h6 class="widget-card-amount">{{ showAmount($pending) }}</h6>
                        <p class="widget-card-title">@lang('Payouts (or Paid Earnings)')</p>
                    </div>
                </div>
                <span class="widget-card-arrow">
                    <i class="las la-angle-right"></i>
                </span>
            </div>

            <div class="widget-card bg--warning">
                <a href="{{ url()->route('vendor.deposit.rejected',request()->all()) }}" class="widget-card-link"></a>
                <div class="widget-card-left">
                    <div class="widget-card-icon">
                    <i class="la la-money-check-alt"></i>
                    </div>
                    <div class="widget-card-content">
                        <h6 class="widget-card-amount">{{ showAmount($rejected) }}</h6>
                        <p class="widget-card-title">@lang('Pending Payouts')</p>
                    </div>
                </div>
                <span class="widget-card-arrow">
                    <i class="las la-angle-right"></i>
                </span>
            </div>

            <div class="widget-card bg--dark">
                <a href="{{ url()->route('vendor.deposit.initiated',request()->all()) }}" class="widget-card-link"></a>
                <div class="widget-card-left">
                    <div class="widget-card-icon">
                        <i class="la la-money-check-alt"></i>
                    </div>
                    <div class="widget-card-content">
                        <h6 class="widget-card-amount">{{ showAmount($initiated) }}</h6>
                        <p class="widget-card-title">@lang('Initiated Payment')</p>
                    </div>
                </div>
                <span class="widget-card-arrow">
                    <i class="las la-angle-right"></i>
                </span>
            </div>

        </div>
    </div>
</div>
