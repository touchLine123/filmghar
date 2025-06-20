<!doctype html>
<html lang="en" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ gs()->siteName(__($pageTitle)) }}</title>
    <link type="image/png" href="{{ asset('assets/images/logoIcon/favicon.png') }}" rel="icon" sizes="16x16">
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/main.css') }}" rel="stylesheet">
    <style>
        body {
            min-height: calc(100vh + 0px) !important;
        }

        .maintanance-page {
            display: grid;
            place-content: center;
            width: 100%;
            height: 100vh;
        }

        .maintanance-icon {
            width: 60px;
            height: 60px;
            display: grid;
            place-items: center;
            aspect-ratio: 1;
            border-radius: 50%;
            background: #fff;
            font-size: 26px;
            color: #e73d3e;
        }
    </style>
</head>

<body @stack('context')>

    <div class="maintanance-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="maintanance-icon mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    @php echo $maintenance->data_values->description @endphp
                </div>
            </div>
        </div>
    </div>
</body>

</html>
