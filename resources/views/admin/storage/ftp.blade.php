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
                                    <label class="form-control-label font-weight-bold">@lang('FTP Hosting root path') <small class="text--primary">( @lang('Please Enter With http protocol') )</small></label>
                                    <input class="form-control form-control-lg" name="ftp[domain]" type="text" value="{{ @gs('ftp')->domain }}">
                                    <code>@lang('https://yourdomain.com')</code>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Host')</label>
                                    <input class="form-control form-control-lg" name="ftp[host]" type="text" value="{{ @gs('ftp')->host }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Username')</label>
                                    <input class="form-control form-control-lg" name="ftp[username]" type="text" value="{{ @gs('ftp')->username }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Password')</label>
                                    <input class="form-control form-control-lg" name="ftp[password]" type="text" value="{{ @gs('ftp')->password }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Port')</label>
                                    <input class="form-control form-control-lg" name="ftp[port]" type="text" value="{{ @gs('ftp')->port }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold">@lang('Root Folder')</label>
                                    <input class="form-control form-control-lg" name="ftp[root]" type="text" value="{{ @gs('ftp')->root }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
