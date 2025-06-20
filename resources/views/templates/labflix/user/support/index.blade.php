@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="pt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="card custom--card">
                        <div class="card-body">
                            <div class="table--responsive--lg table">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>@lang('Subject')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Priority')</th>
                                            <th>@lang('Last Reply')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($supports as $key => $support)
                                            <tr>
                                                <td> <a class="font-weight-bold text-white" href="{{ route('ticket.view', $support->ticket) }}"> [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }} </a></td>
                                                <td>
                                                    @php echo $support->statusBadge; @endphp
                                                </td>
                                                <td>
                                                    @if ($support->priority == Status::PRIORITY_LOW)
                                                        <span class="badge badge--dark">@lang('Low')</span>
                                                    @elseif($support->priority == Status::PRIORITY_MEDIUM)
                                                        <span class="badge badge--warning">@lang('Medium')</span>
                                                    @elseif($support->priority == Status::PRIORITY_HIGH)
                                                        <span class="badge badge--danger">@lang('High')</span>
                                                    @endif
                                                </td>
                                                <td>{{ diffForHumans($support->last_reply) }} </td>

                                                <td>
                                                    <a class="cmn-btn btn--sm" href="{{ route('ticket.view', $support->ticket) }}">
                                                        <i class="fa fa-desktop"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center text-muted" colspan="100">{{ __($emptyMessage) }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                {{ paginateLinks($supports) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
