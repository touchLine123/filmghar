@php
    $user = auth()->user();
@endphp
@forelse ($plans as $plan)
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
        <div class="package-card">
            @auth
                @if ($user->exp > now() && $user->plan_id == $plan->id)
                    <div class="package-expire">
                        <h6 class="package-expire-title">@lang('Expire this after')</h6>
                        <span class="package-expire-date">{{ diffForHumans($user->exp) }}</span>
                    </div>
                @endif
            @endauth
            <h6 class="package-card__name">{{ __($plan->name) }}</h6>
            <div class="package-card__price">{{ showAmount($plan->pricing) }}</div>

            <ul class="package-benifits">
                <li class="package-benifits-item">
                    <span class="package-benifits-icon"><i class="las la-check"></i></span> @lang('Get ' . $plan->duration . ' days subscribtion')
                </li>
                <li class="package-benifits-item">
                    <span class="package-benifits-icon"><i class="las la-check"></i></span> @lang('Show Ads - '){{ __($plan->showAdStatus) }}
                </li>
                @if (gs('device_limit'))
                    <li class="package-benifits-item">
                        <span class="package-benifits-icon"><i class="las la-check"></i></span> @lang('Connect with ' . $plan->device_limit . ' device')
                    </li>
                @endif
            </ul>
            @auth
                <button class="plan-btn subscriptionBtn" data-question="@lang('Are you sure to subscribe this plan?')" data-action="{{ route('user.subscribe.plan', $plan->id) }}" @disabled($user->exp > now())>@lang('Subscribe Now')</button>
            @else
                <button class="plan-btn subscribeBtn" type="button">@lang('Subscribe Now')</button>
            @endauth
        </div>
    </div>
@empty
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <img src="{{ asset($activeTemplateTrue . 'images/no-results.png') }}" alt="@lang('image')">
    </div>
@endforelse
