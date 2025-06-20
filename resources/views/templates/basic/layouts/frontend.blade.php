@extends($activeTemplate . 'layouts.app')
@section('app')
    @yield('content')
@endsection


@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.subscribe-alert', function() {
                var modal = $('#alertModal');
                modal.modal('show');
            });

            $(document).on('submit', '.subscribe-form', function(e) {
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
        })(jQuery)
    </script>
@endpush
