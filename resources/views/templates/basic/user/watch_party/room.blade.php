@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="watch-party-section pb-80 pt-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-8">
                    <div class="watch-party-video">
                        <div class="main-video">
                            <video playsinline class="video-player"></video>
                        </div>
                    </div>
                    <div class="watch-party-video-info">
                        <div class="top">
                            <h3 class="title">{{ __(@$item->title) }}</h3>
                            @if ($partyRoom->user_id == auth()->id())
                                <button class="cancelPartyBtn" data-action="{{ route('user.watch.party.cancel', $partyRoom->id) }}" type="button">@lang('Cancel Party')</button>
                            @else
                                <button class="leavePartyBtn" data-action="{{ route('user.watch.party.leave', [$partyRoom->id, auth()->id()]) }}" type="button">@lang('Leave Party')</button>
                            @endif
                        </div>
                        <div class="bottom">
                            <div class="author">
                                <div class="thumb">
                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . @$partyRoom->user->image, getFileSize('userProfile'), true) }}" alt="">
                                </div>
                                <div>
                                    @if ($partyRoom->user_id == auth()->id())
                                        <span class="host-badge">@lang('Host')</span>
                                    @endif
                                    <h6 class="title">{{ __(auth()->user()->fullname) }}</h6>
                                </div>
                            </div>

                            @if ($partyRoom->user_id == auth()->id())
                                <div class="code-box">
                                    <span>@lang('Party Code'):</span>
                                    <div class="form-group">
                                        <input type="text" class="party-code" value="{{ $partyRoom->party_code }}" readonly>
                                        <button class="copy-code btn-transparent"><i class="las la-copy"></i></button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4">
                    <div class="chat-box" id="liveChat">
                        <div class="chat-header">
                            <h4 class="title">@lang('Live chat')</h4>
                            <button class="btn-transparent joinedBtn"><i class="las la-user"></i>@lang('Joined')</button>
                        </div>
                        <div class="chat-body chatBodyScroll" id="chatBodyScroll">
                            @foreach ($conversations as $conversation)
                                @include($activeTemplate . 'partials.single_message', ['conversation' => $conversation])
                            @endforeach
                        </div>
                        <div class="chat-footer">
                            <form action="#" class="chat-form" id="messageForm">
                                @csrf
                                <div class="form-group">
                                    <textarea name="message" id="" class="message-textarea" placeholder="Chat..."></textarea>
                                    <div class="chat-form-bottom">
                                        <div class="emoji" id="emoji-button"><i class="las la-smile"></i></div>
                                        <button type="submit" class="btn btn-transparent"><i class="las la-paper-plane"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="chat-box d-none" id="joinList">
                        <div class="chat-header">
                            <button class="btn-transparent d-inline-flex align-items-center chatBtn"><i class="las la-arrow-left"></i> @lang('Chat')</button>
                        </div>
                        <div class="chat-body chatBodyScroll" id="chatJoinBodyScroll">
                            @foreach ($partyMembers as $partyMember)
                                @include($activeTemplate . 'partials.join_list')
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal alert-modal" id="partyStatusModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" method="POST" id="statusFormSubmit">
                    <div class="modal-body">
                        <span class="alert-icon"><i class="fas fa-question-circle"></i></span>
                        <p class="modal-description">@lang('Confirmation Alert!')</p>
                        <p class="modal--text">@lang('Are you sure to remove this user?')</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--dark btn--sm" data-bs-dismiss="modal" type="button">@lang('No')</button>
                        <button class="btn btn--base btn--sm" type="submit">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal alert-modal" id="cancelPartyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" method="POST" id="cancelFormSubmit">
                    <div class="modal-body">
                        <span class="alert-icon"><i class="fas fa-question-circle"></i></span>
                        <p class="modal-description">@lang('Confirmation Alert!')</p>
                        <p class="modal--text">@lang('Are you sure to cancel this party room?')</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--dark btn--sm" data-bs-dismiss="modal" type="button">@lang('No')</button>
                        <button class="btn btn--base btn--sm" type="submit">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal alert-modal" id="leavePartyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" method="POST" id="leaveFormSubmit">
                    <div class="modal-body">
                        <span class="alert-icon"><i class="fas fa-question-circle"></i></span>
                        <p class="modal-description">@lang('Confirmation Alert!')</p>
                        <p class="modal--text">@lang('Are you sure to leave this party room?')</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--dark btn--sm" data-bs-dismiss="modal" type="button">@lang('No')</button>
                        <button class="btn btn--base btn--sm" type="submit">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('style')
    <style>
        /* Watch Party */
        .cancelPartyBtn {
            background: transparent;
            color: hsl(var(--base));
            border: 1px solid rgb(213 0 85 / 40%);
            line-height: 1;
            padding: 9px 16px;
            border-radius: 5px;
        }

        .leavePartyBtn {
            background: transparent;
            color: hsl(var(--base));
            border: 1px solid rgb(213 0 85 / 40%);
            line-height: 1;
            padding: 9px 16px;
            border-radius: 5px;
        }

        .host-badge {
            font-size: 10px;
            color: #2afc00;
            line-height: 1;
            padding: 4px 8px;
            border: 1px solid rgb(255 255 255 / 10%);
            background: rgb(255 255 255 / 10%);
            border-radius: 4px;
            margin-bottom: 6px;
        }

        .watch-party-video-info {
            background-color: #131722;
            padding: 16px;
            margin-top: 24px;
            border-radius: 12px;
            border: 1px solid rgb(255 255 255 / 10%);
        }

        .watch-party-video-info .top {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .watch-party-video-info .title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 0;
        }

        .watch-party-video-info .code-box {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        @media (max-width: 375px) {
            .watch-party-video-info .code-box {
                flex-direction: column;
                gap: 6px;
                align-items: flex-start
            }
        }

        .watch-party-video-info .code-box span {
            color: #ffffff;
            font-size: 16px;
            font-weight: 400;
            margin-right: 10px;
            margin-bottom: 0;
        }

        @media (max-width: 425px) {
            .watch-party-video-info .code-box span {
                font-size: 14px;
            }
        }

        .watch-party-video-info .code-box .form-group {
            position: relative;
            margin-bottom: 0;
        }

        .watch-party-video-info .code-box .form-group input {
            font-size: 16px;
            color: #ffffff;
            background: transparent;
            border: 1px solid #3E3E60;
            border-radius: 4px;
            padding: 7px 5px;
        }

        .watch-party-video-info .code-box .form-group input::placeholder {
            font-size: 16px;
            color: #ffffff;
        }

        .watch-party-video-info .code-box .form-group .copy-code {
            font-size: 18px;
            padding: 0;
            position: absolute;
            right: 8px;
            top: 0;
            bottom: 0;
            margin: auto 0;
        }

        .watch-party-video-info .bottom {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 22px 10px;
        }

        .watch-party-video-info .bottom .author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .watch-party-video-info .bottom .author .thumb {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
        }

        .watch-party-video-info .bottom .author .title {
            font-size: 16px;
            color: #ffffff;
            margin-bottom: 0;
        }

        .watch-party-video-info .bottom .btn {
            padding: 7px 25px;
        }

        .watch-party-video-info .bottom .btn:focus {
            box-shadow: none;
        }


        /* Chat Box */
        .chat-box {
            position: relative;
            border: 1px solid #3E3E60;
            border-radius: 7px;
        }



        .chat-box .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #3E3E60;
            padding: 13px 15px;
        }

        .chat-box .chat-header .title {
            font-size: 16px;
        }

        .chat-box .chat-header button {
            font-size: 14px;
            padding: 0;
        }

        .chat-box .chat-header button i {
            font-size: 20px;
            margin-right: 5px;
        }

        .chat-box .chat-body {
            padding: 15px 12px 20px;
            height: 410px;
            overflow-y: auto;
        }

        .chat-box .chat-body .chat-item {
            position: relative;
            padding-left: 35px;
            margin-bottom: 20px;
        }

        .chat-box .chat-body .chat-item:last-of-type {
            margin-bottom: 0;
        }

        .chat-box .chat-body .chat-item .thumb {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            overflow: hidden;
            position: absolute;
            top: 4px;
            left: 0;
        }

        .chat-box .chat-body .chat-item.block-unblock {
            padding-left: 32px;
        }

        .chat-box .chat-body .chat-item.block-unblock .thumb {
            top: 2px;
        }

        .chat-box .chat-body .chat-item .username {
            font-size: 14px;
            color: #696996;
            line-height: 1;
            margin-bottom: 5px;
        }

        .chat-box .chat-body .chat-item .message {
            font-size: 14px;
            color: #ffffff;
            line-height: 16.41px;
            margin-bottom: 0;
        }

        .chat-box .chat-footer {
            padding: 13px 15px;
            border-top: 1px solid #696996;
        }

        .chat-box .chat-footer .chat-form .form-group {
            margin-bottom: 0;
        }

        .chat-box .chat-footer .chat-form textarea {
            width: 100%;
            background: transparent !important;
            border: 0;
            border-bottom: 1px solid #696996 !important;
            border-radius: 0 !important;
            font-size: 14px;
            color: #ffffff;
            padding: 0;
            min-height: 30px !important;
            max-height: 30px !important;
        }

        .chat-box .chat-footer .chat-form textarea::placeholder {
            font-size: 14px;
            color: #ffffff;
        }

        .chat-box .chat-footer .chat-form .chat-form-bottom {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
        }

        .chat-box .chat-footer .chat-form .emoji {
            font-size: 20px;
            color: #ffffff;
            cursor: pointer;
        }

        .chat-box .chat-footer .chat-form .btn {
            font-size: 20px;
            padding: 0;
        }

        .chat-box .chat-footer .chat-form .btn:hover {
            color: #ffffff;
        }

        .chat-box .chat-body .chat-item.block-unblock button {
            padding: 0;
            font-size: 14px;
            text-decoration: underline;
        }

        .btn-transparent {
            background: transparent;
            color: #ffffff;
        }

        .btn-transparent:focus {
            box-shadow: none;
        }

        /* Chat Body Scrollbar */
        .chat-body::-webkit-scrollbar {
            width: 4px !important;
        }

        .chat-body::-webkit-scrollbar-thumb {
            background-color: #3E3E60 !important;
            border-radius: 4px !important;
        }

        .chat-body::-webkit-scrollbar-track {
            background-color: transparent !important;
        }

        /* Emoji */
        .emoji-picker {
            background: #0d0d31 !important;
            border: 1px solid #3E3E60 !important;
        }

        .emoji-picker__search {
            border: 1px solid #3E3E60 !important;
            background: transparent;
            color: #ffffff;
        }

        .emoji-picker__search::placeholder {
            color: #ffffff;
        }

        .emoji-picker__search-icon {
            top: 3px !important;
        }

        .emoji-picker__category-button {
            color: #ffffff !important;
        }

        .emoji-picker__category-button:hover {
            color: hsl(var(--base)) !important;
        }

        .emoji-picker__category-button.active {
            color: hsl(var(--base)) !important;
            border-bottom: 2px solid hsl(var(--base)) !important;
        }

        .emoji-picker__emojis {
            overflow-x: hidden !important;
        }

        .emoji-picker__preview {
            display: none !important;
        }

        .emoji-picker__emoji {
            color: #ffffff;
            font-size: 18px !important;
            line-height: 1 !important;
        }

        .emoji-picker__emoji:focus,
        .emoji-picker__emoji:hover {
            background: #3E3E60 !important;
        }

        .emoji-picker__emojis .emoji-picker__category-name {
            color: #ffffff !important;
            text-transform: capitalize !important;
            font-weight: 500 !important;
        }

        /* Emoji Scrollbar */
        .emoji-picker__emojis::-webkit-scrollbar {
            width: 5px;
        }

        .emoji-picker__emojis::-webkit-scrollbar-thumb {
            background-color: #3E3E60;
            border-radius: 5px;
        }

        .emoji-picker__emojis::-webkit-scrollbar-track {
            background-color: transparent;
        }

        /* Chat Box Textarea Scrollbar */
        .chat-box .chat-footer .chat-form textarea::-webkit-scrollbar {
            width: 3px;
        }

        .chat-box .chat-footer .chat-form textarea::-webkit-scrollbar-thumb {
            background-color: #3E3E60;
            border-radius: 3px;
        }

        .chat-box .chat-footer .chat-form textarea::-webkit-scrollbar-track {
            background-color: transparent;
        }




        @media (max-width: 1199px) {

            .watch-party-video-info .top .title,
            .watch-party-video-info .top .code-box span {
                font-size: 20px;
            }
        }

        @media (max-width: 991px) {
            .chat-box {
                margin-top: 30px;
            }

            .watch-party-video-info .top .title,
            .watch-party-video-info .top .code-box span {
                font-size: 20px;
            }
        }

        @media (max-width: 767px) {
            .watch-party-video-info .top .code-box .form-group input {
                font-size: 14px;
                padding: 5px 7px;
                max-width: 135px;
            }

            .watch-party-video-info .top .code-box .form-group input::placeholder {
                font-size: 14px;
            }

            .watch-party-video-info .top {
                margin: 15px 0;
            }

            .watch-party-video-info .bottom .btn {
                padding: 6px 18px;
                font-size: 14px;
            }

            .watch-party-video-info .bottom .author span {
                font-size: 14px;
            }
        }

        @media (max-width: 575px) {

            .watch-party-video-info .top .title,
            .watch-party-video-info .top .code-box span {
                font-size: 17px;
            }
        }

        ::-webkit-scrollbar {
            width: 7px !important;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #3E3E60 !important;
            border-radius: 4px !important;
        }

        ::-webkit-scrollbar-track {
            background-color: rgba(65, 65, 100, 0.400) !important;
        }
    </style>
    </style>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/plyr.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/plyr.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/hls.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/emoji.min.js') }}"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $(document).find('.plyr__controls').addClass('d-none');
            $(document).find('.ad-video').find('.plyr__controls').addClass('d-none');
        });
        (function($) {
            "use strict";

            let hostId = Number("{{ auth()->id() }}");
            let hostUserId = "{{ @$partyRoom->user_id == auth()->id() }}"
            const controls = [
                'play-large',
                'play',
                'mute',
                'pip',
                'airplay',
                'fullscreen'
            ];

            let player;
            if (hostUserId) {
                player = new Plyr('.video-player', {
                    controls,
                    ratio: '16:9',
                    clickToPlay: true
                });
            } else {
                player = new Plyr('.video-player', {
                    controls: false,
                    ratio: '16:9',
                    clickToPlay: false
                });
            }

            player.source = {
                type: 'video',
                sources: [
                    @foreach ($videos as $video)
                        {
                            src: "{{ $video->content }}",
                            type: 'video/mp4',
                            size: "{{ $video->size }}",
                        },
                    @endforeach
                ],
                poster: "{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}",
                tracks: [
                    @foreach ($subtitles ?? [] as $subtitle)
                        {
                            kind: 'captions',
                            label: "{{ $subtitle->language }}",
                            srclang: "{{ $subtitle->code }}",
                            src: "{{ getImage(getFilePath('subtitle') . '/' . $subtitle->file) }}",
                        },
                    @endforeach
                ]
            };

            player.on('play', () => {
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.watch.party.player.setting') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        party_id: "{{ @$partyRoom->id }}",
                        status: 'play'
                    },
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error);
                            return;
                        }
                        $(document).find('.plyr__controls').removeClass('d-none');
                    }
                });
            });


            player.on('pause', () => {
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.watch.party.player.setting') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        party_id: "{{ @$partyRoom->id }}",
                        status: 'pause'
                    },
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error);
                            return;
                        }
                        $(document).find('.plyr__controls').removeClass('d-none');
                    }
                });
            });



            $('.joinedBtn').on('click', function(e) {
                $('#liveChat').addClass('d-none');
                $('#joinList').removeClass('d-none');
            });

            $('.chatBtn').on('click', function(e) {
                $('#joinList').addClass('d-none');
                $('#liveChat').removeClass('d-none');
            });

            const button = $("#emoji-button");
            const picker = new EmojiButton();

            button.on("click", function() {
                picker.togglePicker(button.get(0));
            });

            picker.on("emoji", function(emoji) {
                $("[name=message]").val($("[name=message]").val() + emoji);
            });


            Pusher.logToConsole = true;

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

            pusherConnection("leave-watch-party", leaveWatchParty);

            function leaveWatchParty(data) {
                if (data.hostId == hostId) {
                    $("#chatJoinBodyScroll").html(data.html);
                }
            }

            pusherConnection("player-setting", playerSetting);

            function playerSetting(data) {
                console.log(data);
                let allMember = data.allMemberId;
                if (allMember.includes(Number(hostId))) {
                    if (data.status == 'play') {
                        player.muted = true;
                        player.play();
                    } else {
                        player.muted = false;
                        player.pause();
                    }
                }
            }

            let statusModal = $('#partyStatusModal');
            $(document).on('click', '.changeStatusBtn', function(e) {
                statusModal.find('form').attr('action', $(this).data('action'));
                statusModal.find('.modal-description').text($(this).data('question'));
                statusModal.modal('show');
            });


            $('#statusFormSubmit').on('submit', function(e) {
                e.preventDefault();
                const route = $('#partyStatusModal').find('form').attr('action');
                if (!route) {
                    notify('error', 'Invalid request');
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: route,
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error);
                            return;
                        }

                        $(`#member-${response.id}`).remove();
                        notify('success', response.success);
                        statusModal.modal('hide');
                    }
                });
            });

            $('.cancelPartyBtn').on('click', function(e) {
                let cancelModal = $("#cancelPartyModal")
                cancelModal.find('form').attr('action', $(this).data('action'));
                cancelModal.modal('show');
            });

            $('#cancelFormSubmit').on('submit', function(e) {
                e.preventDefault();
                const route = $('#cancelPartyModal').find('form').attr('action');
                if (!route) {
                    notify('error', 'Invalid request');
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: route,
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error);
                            return;
                        }
                        window.location.href = response.redirect_url;
                    }
                });
            });

            $('.leavePartyBtn').on('click', function(e) {
                let leaveModal = $("#leavePartyModal")
                leaveModal.find('form').attr('action', $(this).data('action'));
                leaveModal.modal('show');
            });

            $('#leaveFormSubmit').on('submit', function(e) {
                e.preventDefault();
                const route = $('#leavePartyModal').find('form').attr('action');
                if (!route) {
                    notify('error', 'Invalid request');
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: route,
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error);
                            return;
                        }
                        if (response.user_id == "{{ auth()->id() }}") {
                            window.location.href = response.route;
                        }
                    }
                });
            });


            $(window).on('load', function() {
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.watch.party.reload') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        party_id: "{{ $partyRoom->id }}"
                    },
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error);
                        }
                    }
                });
            });

        })(jQuery)
    </script>
@endpush
