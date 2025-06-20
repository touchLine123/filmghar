<!doctype html>
<html lang="en" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ gs()->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')
    <link type="image/png" href="{{ siteFavicon() }}" rel="icon" sizes="16x16">
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/line-awesome.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/global/css/global.css') }}" rel="stylesheet" />

    <link href="{{ asset($activeTemplateTrue . 'css/lightcase.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/vendor/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/vendor/nice-select.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/vendor/slick.css') }}" rel="stylesheet">

    @stack('style-lib')

    <link href="{{ asset($activeTemplateTrue . 'css/main.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/custom.css') }}" rel="stylesheet">

    @stack('style')

    <link href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ gs('base_color') }}&secondColor={{ gs('secondary_color') }}" rel="stylesheet">
</head>

@php echo loadExtension('google-analytics') @endphp

<body @stack('context')>
    @if (@$type != 'app')
        <div id="preloader">
            <div class="pre-logo">
                <div class="gif"></div>
            </div>
        </div>
    @endif

    <div class="page-wrapper" id="main-scrollbar" data-scrollbar>
        @if (@$type != 'app')
            @include($activeTemplate . 'partials.header')

            @if (!request()->routeIs('home'))
                @include($activeTemplate . 'partials.breadcrumb')
            @endif
        @endif
        @yield('app')

        <div class="loading"></div>

        @if (@$type != 'app')
            @include($activeTemplate . 'partials.footer')

            <div class="modal alert-modal fade" id="notifyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @php
        $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
    @endphp
    @if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
        <div class="cookies-card hide text-center">
            <div class="cookies-card__icon bg--base">
                <i class="las la-cookie-bite"></i>
            </div>
            <p class="cookies-card__content mt-4">{{ @$cookie->data_values->short_desc }} <a class="base--color" href="{{ route('cookie.policy') }}" target="_blank">@lang('learn more')</a></p>
            <div class="cookies-card__btn mt-4">
                <a class="cmn-btn w-100 policy" href="javascript:void(0)">@lang('Allow')</a>
            </div>
        </div>
    @endif

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/global.js') }}"></script>
    <script src="{{ asset('assets/global/js/pusher.min.js') }}"></script>


    @stack('script-lib')

    @php echo loadExtension('tawk-chat') @endphp

    @include('partials.notify')

    @if (gs('pn'))
        @include('partials.push_script')
    @endif

    <script src="{{ asset($activeTemplateTrue . 'js/vendor/lightcase.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/wow.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/jquery.syotimer.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/syotimer.lang.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/jquery.countdown.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/app.js') }}"></script>

    @stack('script')

    <script>
        (function($) {
            "use strict";
            $(".langSel").on("click", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).data('lang_code');
            });

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

            var inputElements = $('[type=text],select,textarea');
            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input:not([type=checkbox]):not([type=hidden]), select, textarea'), function(i, element) {
                var elementType = $(element);
                if (elementType.attr('type') != 'checkbox') {
                    if (element.hasAttribute('required')) {
                        $(element).closest('.form-group').find('label').addClass('required');
                    }
                }
            });

            let disableSubmission = false;
            $('.disableSubmission').on('submit', function(e) {
                if (disableSubmission) {
                    e.preventDefault()
                } else {
                    disableSubmission = true;
                }
            });

            function formatState(state) {
                if (!state.id) return state.text;
                let gatewayData = $(state.element).data();
                return $(`<div class="d-flex gap-2">${gatewayData.imageSrc ? `<div class="select2-image-wrapper"><img class="select2-image" src="${gatewayData.imageSrc}"></div>` : '' }<div class="select2-content"> <p class="select2-title">${gatewayData.title}</p><p class="select2-subtitle">${gatewayData.subtitle}</p></div></div>`);
            }

            $('.select2').each(function(index, element) {
                $(element).select2({
                    templateResult: formatState,
                    minimumResultsForSearch: "-1"
                });
            });

            $('.select2-searchable').each(function(index, element) {
                $(element).select2({
                    templateResult: formatState,
                    minimumResultsForSearch: "1"
                });
            });

            $('.select2-basic').each(function(index, element) {
                $(element).select2({
                    dropdownParent: $(element).closest('.select2-parent')
                });
            });

            $.each($('.select2'), function() {
                $(this)
                    .wrap(`<div class="position-relative"></div>`)
                    .select2({
                        dropdownParent: $(this).parent()
                    });
            });

            $('.subscribe-form').on('submit', function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                e.preventDefault();
                var email = $('input[name=email]').val();
                $.post('{{ route('subscribe') }}', {
                    email: email
                }, function(response) {
                    if (response.error) {
                        notify('error', response.error)
                    } else {
                        $('input[name=email]').val('');
                        notify('success', response.success)
                    }
                });
            });

            $(document).on("click", ".advertise", function() {
                var id = $(this).data('id');
                var url = "{{ route('add.click') }}";

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    method: 'POST',
                    data: {
                        'id': id
                    },
                    success: function(data) {

                    },
                });
            });

            $(document).on('click', '.addWishlist', function() {
                let id = $(this).data('id');
                let type = $(this).data('type');
                let contentType = ['item', 'episode'];
                if (!contentType.includes(type) || !Number.isInteger(id)) {
                    notify('error', 'Invalid Request');
                    return;
                }
                let data = {};
                data.id = id;
                data.type = type;

                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    type: "POST",
                    url: `{{ route('wishlist.add') }}`,
                    data: data,
                    success: function(response) {
                        if (response.status == 'success') {
                            notify('success', response.message);
                            $('.addWishlist').addClass('d-none');
                            $('.removeWishlist').removeClass('d-none');
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });

            $(document).on('click', '.removeWishlist', function() {
                let id = $(this).data('id');
                let type = $(this).data('type');
                let contentType = ['item', 'episode'];
                if (!contentType.includes(type) || !Number.isInteger(id)) {
                    notify('error', 'Invalid Request');
                    return;
                }
                let data = {};
                data.id = id;
                data.type = type;

                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    type: "POST",
                    url: `{{ route('wishlist.remove') }}`,
                    data: data,
                    success: function(response) {
                        if (response.status == 'success') {
                            notify('success', response.message);
                            $('.addWishlist').removeClass('d-none');
                            $('.removeWishlist').addClass('d-none');
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });

            $('.trailerBtn').on('click', function() {
                var modal = $('#trailerModal');
                var html = `<source src="${$(this).data('video')}" type="video/mp4" />`
                modal.find('video').attr('poster', $(this).data('poster'));
                modal.find('source').attr('src', $(this).data('video'));
                modal.modal('show');
            });

            $('.subscribe-alert').on('click', function() {
                var modal = $('#alertModal');
                modal.modal('show');
            });

            Array.from(document.querySelectorAll('table')).forEach(table => {
                let heading = table.querySelectorAll('thead tr th');
                Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
                    Array.from(row.querySelectorAll('td')).forEach((column, i) => {
                        (column.colSpan == 100) || column.setAttribute('data-label', heading[i].innerText)
                    });
                });
            });
        })(jQuery);
    </script>
</body>

</html>
