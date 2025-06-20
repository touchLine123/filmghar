@extends('vendor.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('vendor.item.store') }}" method="post" enctype="multipart/form-data" id="itemForm">
                    @csrf
                    <div class="card-body">
                        <div class="d-flex justify-content-end flex-wrap gap-3">
                            <div class="form-group">
                                <input class="form-control" name="id" type="number" placeholder="@lang('Enter TMDB ID Ex: 1000')">
                            </div>
                            <div class="form-group">
                                <select class="form-control select2" data-minimum-results-for-search="-1" name="item_type">
                                    <option value="1">@lang('Single Item')</option>
                                    <option value="2">@lang('Episode Item')</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-outline--dark fetchBtn h-45" type="button"><i class="las la-server"></i> @lang('Fetch')</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Portrait Image')</label>
                                    <div class="image--uploader w-100">
                                        <div class="image-upload-wrapper">
                                            <div class="image-upload-preview portrait" style="background-image: url({{ getImage('/') }})">
                                            </div>
                                            <div class="image-upload-input-wrapper">
                                                <input type="file" class="image-upload-input" name="portrait" id="profilePicUpload1" accept=".png, .jpg, .jpeg">
                                                <label for="profilePicUpload1" class="bg--primary"><i class="la la-cloud-upload"></i></label>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="mt-3 text-muted"> @lang('Supported Files:')
                                                <b>@lang('.png, .jpg, .jpeg')</b>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>@lang('Landscape Image')</label>
                                    <div class="image--uploader w-100">
                                        <div class="image-upload-wrapper">
                                            <div class="image-upload-preview landscape" style="background-image: url({{ getImage('/') }})">
                                            </div>
                                            <div class="image-upload-input-wrapper">
                                                <input type="file" class="image-upload-input" name="landscape" id="profilePicUpload2" accept=".png, .jpg, .jpeg">
                                                <label for="profilePicUpload2" class="bg--primary"><i class="la la-cloud-upload"></i></label>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="mt-3 text-muted"> @lang('Supported Files:')
                                                <b>@lang('.png, .jpg, .jpeg')</b>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input name="portrait_url" type="hidden" value="">
                        <input name="landscape_url" type="hidden" value="">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Title')</label>
                                <input class="form-control" name="title" type="text" value="{{ old('title') }}" required>
                            </div>
                            <div class="form-group col-md-6 version">
                                <label>@lang('Version')</label>
                                <select class="form-control select2" data-minimum-results-for-search="-1" name="version">
                                    <option value="">@lang('Select One')</option>
                                    <option value="0">@lang('Free')</option>
                                    <option value="1">@lang('Paid')</option>
                                    <option value="2">@lang('Rent')</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6 rent-option d-none">
                                <label>@lang('Do you want to add it as rent?')</label>
                                <div class="d-flex gap-3 flex-wrap">
                                    <div class="form-check">
                                        <input class="form-check-input" id="yes" name="version" type="radio" value="2">
                                        <label class="form-check-label" for="yes">@lang('Yes')</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" id="no" name="version" type="radio" value="0">
                                        <label class="form-check-label" for="no">@lang('No')</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row d-none" id="rentalArea">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Rent Price')</label>
                                    <div class="input-group">
                                        <input class="form-control" name="rent_price" type="number" step="any" value="{{ old('rent_price') }}">
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Rental Period')</label>
                                    <div class="input-group">
                                        <input class="form-control" name="rental_period" type="number" value="{{ old('rental_period') }}">
                                        <span class="input-group-text">@lang('Days')</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Exclude from plan')</label>
                                    <select class="form-control select2" data-minimum-results-for-search="-1" name="exclude_plan">
                                        <option value="">@lang('Select One')</option>
                                        <option value="0">@lang('No')</option>
                                        <option value="1">@lang('Yes')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Category')</label>
                                <select class="form-control select2" name="category" required>
                                    <option value="">@lang('Select One')</option>
                                    @foreach ($categories as $category)
                                        <option data-subcategories="{{ $category->subcategories }}" value="{{ $category->id }}">{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Sub Category')</label>
                                <select class="form-control select2" name="sub_category_id">
                                    <option value="">@lang('Select One')</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Preview Text')</label>
                                <textarea class="form-control" name="preview_text" rows="5" required>{{ old('preview_text') }}</textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Description')</label>
                                <textarea class="form-control" name="description" rows="5" required>{{ old('description') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="form-group col-md-4  position-relative">
                                <label>@lang('Director')</label>
                                <select class="form-control select2-auto-tokenize director-option" name="director[]" multiple="multiple" required></select>
                            </div>
                            <div class="form-group col-md-4  position-relative">
                                <label>@lang('Producer')</label>
                                <select class="form-control select2-auto-tokenize producer-option" name="producer[]" multiple="multiple" required></select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Ratings') <small class="text--primary">(@lang('maximum 10 star'))</small></label>
                                <div class="input-group">
                                    <input class="form-control" name="ratings" type="number" value="{{ old('ratings') }}" step="any" required>
                                    <span class="input-group-text"><i class="las la-star"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4 ">
                                <label>@lang('Release Date')</label>
                                <input class="form-control" name="release_date" type="date" value="" id="release_date" required>
                            </div>
                            <div class="form-group col-md-4 ">
                                <label>@lang('Movie Duration Hours')</label>
                                <div class="input-group">
                                    <input class="form-control" name="movie_duration_hours" type="number" value="" id="movie_duration_hours" required>
                                    <span class="input-group-text">Hours</span>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Movie Duration Minutes')</label>
                                <div class="input-group">
                                    <input class="form-control" name="movie_duration_minutes" type="number" value="{{ old('movie_duration_minutes') }}" step="any" required>
                                    <span class="input-group-text">Minutes</span>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="form-group col-md-6  position-relative">
                                <label>@lang('Genres')</label>
                                <select class="form-control select2-auto-tokenize genres-option" name="genres[]" multiple="multiple" required></select>
                            </div>
                            <div class="form-group col-md-6  position-relative">
                                <label>@lang('Languages')</label>
                                <select class="form-control select2-auto-tokenize language-option" name="language[]" multiple="multiple" required></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6  position-relative">
                                <label class="form-control-label">@lang('Casts')</label>
                                <small class="text-facebook ml-2 mt-2">@lang('Separate multiple by') <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>
                                <select class="form-control select2-auto-tokenize cast-option" name="casts[]" multiple="multiple" required></select>
                            </div>
                            <div class="form-group col-md-6 position-relative">
                                <label>@lang('Tags')</label>
                                <small class="text-facebook ml-2 mt-2">@lang('Separate multiple by') <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>
                                <select class="form-control select2-auto-tokenize tag-option" name="tags[]" multiple="multiple" required></select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <x-back route="{{ route('vendor.item.index') }}" />
@endpush

@push('style')
    <style>
        .image-upload-preview {
            background-size: cover !important;
        }

        .select2-container {
            min-width: 140px !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            let rent = "{{ Status::RENT_VERSION }}";
            let version;
            let rentalArea = $('#rentalArea');

            $('#itemForm').on('submit', function(e) {
                version = $('[name=version]').find('option:selected').val();
                if (version == rent) {
                    e.preventDefault();
                    if (!$('[name=rent_price]').val()) {
                        notify('error', 'Rent price field is required');
                        return;
                    }
                    if (!$('[name=rental_period]').val()) {
                        notify('error', 'Rental period field is required');
                        return;
                    }
                    if (!$('[name=exclude_plan]').val()) {
                        notify('error', 'Exclude from plan field is required');
                        return;
                    }
                }
                $(this).off('submit').submit();
            });


            $.each($('.select2-auto-tokenize'), function(index, element) {
                $(element).select2({
                    dropdownParent: $(element).closest('.position-relative'),
                    tags: true,
                    tokenSeparators: [',']
                });
            });

            $('[name=category]').on('change', function() {
                var subcategoryOption = '<option>@lang('Select One')</option>';
                var subcategories = $(this).find(':selected').data('subcategories');
                subcategories.forEach(subcategory => {
                    subcategoryOption += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                });
                $('[name=sub_category_id]').html(subcategoryOption);
            });


            $('[name=version]').on('change', function(e) {
                version = Number($(this).val());
                if (version == rent) {
                    rentalArea.removeClass('d-none');
                } else {
                    rentalArea.addClass('d-none');
                }
            });


            $('[name=item_type]').on('change', function() {
                if ($(this).val() == '1') {
                    $('.version').removeClass('d-none');
                    $('.rent-option').addClass('d-none');
                } else {
                    $('.version').addClass('d-none');
                    $('.rent-option').removeClass('d-none');
                }
            });


            $('select[name=version]').val('{{ old('version') }}');
            $('select[name=category]').val('{{ old('category') }}');
            $('select[name=sub_category_id]').val('{{ old('sub_category_id') }}');


            $('.fetchBtn').on('click', function(e) {
                e.preventDefault();

                let data = {};
                data.id = $('[name=id]').val();
                data.item_type = $('[name=item_type]').find(":selected").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{ route('vendor.item.fetch') }}",
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            var casts = response.casts;
                            var tags = response.tags;

                            var portraitImage = `https://image.tmdb.org/t/p/original${data.poster_path}`;
                            var landscapeImage = `https://image.tmdb.org/t/p/original${data.backdrop_path}`;

                            $('.portrait').attr('style', `background-image: url(${portraitImage})`);
                            $('.landscape').attr('style', `background-image: url(${landscapeImage})`);
                            $('[name=portrait_url]').val(portraitImage);
                            $('[name=landscape_url]').val(landscapeImage);

                            $('[name=ratings]').val(data.vote_average);
                            $('[name=title]').val(data.title ?? data.name);
                            $('[name=preview_text]').val(data.tagline);
                            $('[name=description]').val(data.overview);

                            // Cast list
                            var castOption = '';
                            $.each(casts.cast, function(index, value) {
                                castOption += `<option value="${value.name}" selected>${value.name}</option>`
                            });
                            $('.cast-option').html(castOption);

                            // producer
                            var producerOption = '';
                            $.each(casts.crew, function(index, value) {
                                if (value.job == "Producer") {
                                    producerOption += `<option value="${value.name}" selected>${value.name}</option>`
                                }
                            });
                            $('.producer-option').html(producerOption);

                            // director
                            var directorOption = '';
                            $.each(casts.crew, function(index, value) {
                                if (value.known_for_department == "Directing" && (value.job == "Screenplay" || value.job == "Director")) {
                                    directorOption += `<option value="${value.name}" selected>${value.name}</option>`
                                }
                            });
                            $('.director-option').html(directorOption);

                            if (directorOption == '') {
                                $.each(data.created_by, function(index, value) {
                                    directorOption += `<option value="${value.name}" selected>${value.name}</option>`
                                });
                                $('.director-option').html(directorOption);
                            }

                            // genres
                            var genresOption = '';
                            $.each(data.genres, function(index, value) {
                                genresOption += `<option value="${value.name}" selected>${value.name}</option>`
                            });
                            $('.genres-option').html(genresOption);
                            // language
                            var langOption = '';
                            $.each(data.spoken_languages, function(index, value) {
                                langOption += `<option value="${value.name}" selected>${value.name}</option>`
                            });
                            $('.language-option').html(langOption);

                            // tags
                            var tagOption = '';
                            $.each(tags.keywords ?? tags.results, function(index, value) {
                                tagOption += `<option value="${value.name}" selected>${value.name}</option>`
                            });
                            $('.tag-option').html(tagOption);

                            notify('success', 'Data imported successfully');

                        } else {
                            notify('error', response.error);
                        }
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
