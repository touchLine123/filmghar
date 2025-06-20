@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    @php echo $policy->data_values->description @endphp
                </div>
            </div>
        </div>
    </section>
@endsection
