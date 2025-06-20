@extends($activeTemplate . 'layouts.app')
@section('app')
    @yield('content')
@endsection
@push('script')
    <script>
        (function($) {
            "use strict";

            let userId = Number("{{ auth()->id() }}");

            Pusher.logToConsole = false;
            var pusher = new Pusher("{{ gs('pusher_config')->app_key }}", {
                cluster: "{{ gs('pusher_config')->cluster }}",
            });


            const pusherConnection = (eventName, callback) => {
                pusher.connection.bind('connected', () => {
                    const SOCKET_ID = pusher.connection.socket_id;
                    const CHANNEL_NAME = `private-${eventName}`;
                    const BASE_URL = "{{ route('home') }}";
                    const url = `${BASE_URL}/pusher/auth/${SOCKET_ID}/${CHANNEL_NAME}`
                    pusher.config.authEndpoint = url;

                    let channel = pusher.subscribe(CHANNEL_NAME);
                    channel.bind('pusher:subscription_succeeded', function() {
                        channel.bind(eventName, function(data) {
                            callback(data)
                        })
                    });
                });
            };

            pusherConnection("send-notification", joinRequestModal);


            let modal = $("#notifyModal");

            function joinRequestModal(data) {
                if (userId == data.hostId) {
                    console.log(data);
                    modal.find('.modal-body').html(data.html);
                    modal.modal('show')
                }
            }

            pusherConnection("accept-join-request", acceptJoinRequest);

            function acceptJoinRequest(data) {
                if (userId == data.userId) {
                    window.location.href = data.redirectUrl;
                }
                if (userId == data.hostId) {
                    $('#chatJoinBodyScroll').append(data.html);
                }
            }


            pusherConnection("reject-join-request", rejectJoinRequest);

            function rejectJoinRequest(data) {
                if (userId == data.userId) {
                    let rejectMessage = `<div class="text-center">
                                        <i class="las la-4x la-times-circle"></i>
                                        <h5>@lang('Your join request has been rejected by the host')</h5>
                                    </div>`
                    $('.join-pending').html(rejectMessage);
                    setTimeout(() => {
                        window.location.href = data.redirectUrl;
                    }, 3000);
                }
            }


            pusherConnection("conversation-message", conversationMessage);

            function conversationMessage(data) {
                let members = data.membersId;
                if (members.includes(Number(userId))) {
                    $('#chatBodyScroll').append(data.html);
                    scrollHeight();
                }
            }

            pusherConnection("cancel-party", cancelParty);

            function cancelParty(data) {
                let cancelMemberId = data.allMemberId;
                if (cancelMemberId.includes(Number(userId))) {
                    window.location.href = data.redirectUrl;
                }
            }

            pusherConnection("reload-party", reloadParty);

            function reloadParty(data) {
                let cancelMemberId = data.allMemberId;
                if (cancelMemberId.includes(Number(userId))) {
                    window.location.reload();
                }
            }

            $('#partyJoinForm').submit(function(e) {
                e.preventDefault();
                let submitBtn = $(this).find('button');
                submitBtn.prop('disabled', true);
                $('.join-form-area').addClass('d-none');
                $('.join-pending').removeClass('d-none');

                const partyCode = $('[name=party_code]').val();
                if (!partyCode) {
                    notify('error', 'Party code is required');
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('user.watch.party.join.request') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        party_code: partyCode
                    },
                    success: function(response) {
                        if (response.error) {
                            $('.join-pending').addClass('d-none');
                            $('.join-form-area').removeClass('d-none');
                            submitBtn.prop('disabled', false);
                            notify('error', response.error);
                            return;
                        }

                        if (response.redirect_url) {
                            window.location.href = response.redirect_url;
                        }

                    }
                });
            });

            $(document).on('click', '.joinRejectBtn', function(e) {
                let rejectBtn = $(this);
                let acceptBtn = $('.joinAcceptBtn');
                rejectBtn.prop('disabled', true);
                acceptBtn.prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: `{{ route('user.watch.party.request.reject') }}/${$(this).data('memberid')}`,
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        if (response.error) {
                            rejectBtn.prop('disabled', true);
                            acceptBtn.prop('disabled', true);
                            notify('error', response.error);
                            return;
                        }
                        notify('success', response.success);
                        modal.modal('hide');
                    }
                });
            });

            $(document).on('click', '.joinAcceptBtn', function(e) {
                let rejectBtn = $(this);
                let acceptBtn = $('.joinRejectBtn');
                rejectBtn.prop('disabled', true);
                acceptBtn.prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: `{{ route('user.watch.party.request.accept') }}/${$(this).data('memberid')}`,
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        if (response.error) {
                            rejectBtn.prop('disabled', true);
                            acceptBtn.prop('disabled', true);
                            notify('error', response.error);
                            return;
                        }
                        notify('success', response.success);
                        modal.modal('hide');
                    }
                });
            });

            $("#messageForm").on('submit', function(e) {
                e.preventDefault();

                let data = {
                    _token: "{{ csrf_token() }}",
                    message: $('[name=message]').val(),
                    party_id: "{{ @$partyRoom->id }}"
                }

                $.ajax({
                    url: "{{ route('user.watch.party.send.message') }}",
                    method: "POST",
                    data: data,
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error);
                        } else {
                            $('#messageForm')[0].reset();
                            scrollHeight();
                        }
                    }
                });
            });

            function scrollHeight() {
                if ($('#chatBodyScroll').length) {
                    $('#chatBodyScroll').animate({
                        scrollTop: $('#chatBodyScroll')[0].scrollHeight
                    });
                }
            }
            scrollHeight();


        })(jQuery)
    </script>
@endpush
