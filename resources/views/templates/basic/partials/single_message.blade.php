<div class="chat-item">
    <div class="thumb">
        <img src="{{ getImage(getFilePath('userProfile') . '/' . @$conversation->user->image, getFileSize('userProfile')), true }}" alt="@lang('image')">
    </div>
    <span class="username"><span>@</span>{{ @$conversation->user->username }}</span>
    <p class="message">{{ __($conversation->message) }}</p>
</div>
