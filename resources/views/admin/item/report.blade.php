@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>@lang('Item Information')</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <span class="fw-bold">@lang('Category')</span>
                            <span>{{ __(@$item->category->name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <span class="fw-bold">@lang('Total Views')</span>
                            <span>{{ getAmount($totalViews) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <span class="fw-bold">@lang('Title')</span>
                            <span>{{ __($title) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <span class="fw-bold">@lang('Rating')</span>
                            <span>{{ __($item->ratings) }} @lang('star')</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div id="video-report"></div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.item.index') }}" />
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
@endpush
@push('script')
    <script>
        var options = {
            series: [{
                name: "Total View",
                data: [
                    @foreach ($reports as $report)
                        {{ getAmount($report) }},
                    @endforeach
                ]
            }],
            chart: {
                height: 450,
                type: 'line',
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: true,
                    tools: {
                        download: false
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'straight'
            },
            title: {
                text: 'Video Report',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: [
                    @foreach ($reports as $key => $report)
                        "{{ $key }}",
                    @endforeach
                ],
            }
        };

        var chart = new ApexCharts(document.querySelector("#video-report"), options);
        chart.render();
    </script>
@endpush
