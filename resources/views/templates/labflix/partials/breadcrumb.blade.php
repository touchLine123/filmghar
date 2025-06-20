@php
$breadcrumb = getContent('breadcrumb.content',true);
@endphp
<section class="inner-hero bg_img dark--overlay" data-background="{{ getImage('assets/images/frontend/breadcrumb/'.@$breadcrumb->data_values->background_image, '1920x500') }}">
  <div class="container position-relative">
    <div class="row">
      <div class="col-lg-12">
        <h2 class="text-center ">{{ __($pageTitle) }}</h2>
        <ul class="page-breadcrumb d-flex justify-content-center">
          <li><a href="{{ route('home') }}" class="">@lang('Home')</a></li>
          <li>{{ __($pageTitle) }}</li>
        </ul>
      </div>
    </div>
  </div>
</section>