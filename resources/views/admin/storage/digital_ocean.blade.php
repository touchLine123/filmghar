@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Driver Name')</label>
                                    <input class="form-control form-control-lg" name="digital_ocean[driver]" type="text" value="{{ @gs('digital_ocean')->driver }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('API Key')</label>
                                    <input class="form-control form-control-lg" name="digital_ocean[key]" type="text" value="{{ @gs('digital_ocean')->key }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Secret Key')</label>
                                    <input class="form-control form-control-lg" name="digital_ocean[secret]" type="text" value="{{ @gs('digital_ocean')->secret }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Region')</label>
                                    <input class="form-control form-control-lg" name="digital_ocean[region]" type="text" value="{{ @gs('digital_ocean')->region }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Bucket Name')</label>
                                    <input class="form-control form-control-lg" name="digital_ocean[bucket]" type="text" value="{{ @gs('digital_ocean')->bucket }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('End point')</label>
                                    <input class="form-control form-control-lg" name="digital_ocean[endpoint]" type="text" value="{{ @gs('digital_ocean')->endpoint }}" required>
                                    <code>(@lang('https://your-space-endpoint'))</code>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Update')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
