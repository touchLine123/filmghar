@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.item.update', $item->id) }}" method="post" enctype="multipart/form-data" id="itemForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Portrait Image')</label>
                                    <div class="image--uploader w-100">
                                        <div class="image-upload-wrapper">
                                            <div class="image-upload-preview portrait" style="background-image: url({{ getImage(getFilePath('item_portrait') . '/' . @$item->image->portrait) }})">
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
                                            <div class="image-upload-preview landscape" style="background-image: url({{ getImage(getFilePath('item_landscape') . '/' . @$item->image->landscape) }})">
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
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Title')</label>
                                <input class="form-control" name="title" type="text" value="{{ $item->title }}" placeholder="Title">
                            </div>
                            @if ($item->item_type == Status::EPISODE_ITEM)
                                <div class="form-group col-md-6 rent-option">
                                    <label>@lang('Do you want to add it as rent?')</label>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <div class="form-check">
                                            <input class="form-check-input" id="yes" name="version" type="radio" value="2" @checked($item->version == Status::RENT_VERSION)>
                                            <label class="form-check-label" for="yes">@lang('Yes')</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" id="no" name="version" type="radio" value="0" @checked($item->version == Status::FREE_VERSION)>
                                            <label class="form-check-label" for="no">@lang('No')</label>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="form-group col-md-6 version">
                                    <label>@lang('Version')</label>
                                    <select class="form-control select2" data-minimum-results-for-search="-1" name="version">
                                        <option value="0">@lang('Free')</option>
                                        <option value="1">@lang('Paid')</option>
                                        <option value="2">@lang('Rent')</option>
                                    </select>
                                </div>
                            @endif
                        </div>

                        <div class="row d-none" id="rentalArea">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Rent Price')</label>
                                    <div class="input-group">
                                        <input class="form-control" name="rent_price" type="number" step="any" value="{{ getAmount($item->rent_price) }}">
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Rental Period')</label>
                                    <div class="input-group">
                                        <input class="form-control" name="rental_period" type="number" value="{{ $item->rental_period }}">
                                        <span class="input-group-text">@lang('Days')</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Exclude from plan')</label>
                                    <select class="form-control select2" data-minimum-results-for-search="-1" name="exclude_plan">
                                        <option value="">@lang('Select One')</option>
                                        <option value="0" @selected($item->exclude_plan == Status::NO)>@lang('No')</option>
                                        <option value="1" @selected($item->exclude_plan == Status::YES)>@lang('Yes')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Category')</label>
                                <select class="form-control select2" name="category">
                                    <option value="">@lang('Select One')</option>
                                    @foreach ($categories as $category)
                                        <option data-subcategories="{{ $category->subcategories }}" {{ ($item->category_id == $category->id) ? "selected" : "" }} value="{{ $category->id }}">{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Sub Category')</label>
                                <select class="form-control select2" name="sub_category_id">
                                    <option value="">@lang('Select One')</option>
                                    @foreach ($subcategories as $sub_categorie)
                                        <option value="{{ $sub_categorie->id }}" {{ ($item->sub_category_id == $sub_categorie->id) ? "selected" : "" }} >{{ __($sub_categorie->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Preview Text')</label>
                                <textarea class="form-control" name="preview_text" rows="5" placeholder="@lang('Preview Text')">{{ $item->preview_text }}</textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Description')</label>
                                <textarea class="form-control" name="description" rows="5" placeholder="@lang('Description')">{{ $item->description }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>@lang('Director')</label>
                                <select class="form-control select2-auto-tokenize director-option" name="director[]" multiple="multiple" required>
                                    @foreach (explode(',', $item->team->director) as $director)
                                        <option value="{{ $director }}" selected>{{ $director }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Producer')</label>
                                <select class="form-control select2-auto-tokenize director-option" name="producer[]" multiple="multiple" required>
                                    @foreach (explode(',', $item->team->producer) as $producer)
                                        <option value="{{ $producer }}" selected>{{ $producer }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Ratings') <small class="text--primary">(@lang('maximum 10 star'))</small></label>
                                <div class="input-group">
                                    <input class="form-control" name="ratings" type="text" value="{{ $item->ratings }}" placeholder="@lang('Ratings')">
                                    <span class="input-group-text"><i class="las la-star"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4 ">
                                <label>@lang('Release Date')</label>
                                <input class="form-control" name="release_date" type="date" value="{{ $item->release_date }}" id="release_date" required>
                            </div>
                            <div class="form-group col-md-4 ">
                                <label>@lang('Movie Duration Hours')</label>
                                <div class="input-group">
                                    <input class="form-control" name="movie_duration_hours" value="{{ $item->movie_duration_hours }}" type="number" value="" id="movie_duration_hours" required>
                                    <span class="input-group-text">Hours</span>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Movie Duration Minutes')</label>
                                <div class="input-group">
                                    <input class="form-control" name="movie_duration_minutes" type="number" value="{{ $item->movie_duration_minutes }}" step="any" required>
                                    <span class="input-group-text">Minutes</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Genres')</label>
                                <select class="form-control select2-auto-tokenize genres-option" name="genres[]" multiple="multiple" required>
                                    @foreach (explode(',', @$item->team->genres) as $genre)
                                        <option value="{{ $genre }}" selected>{{ $genre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Languages')</label>
                                <select class="form-control select2-auto-tokenize language-option" name="language[]" multiple="multiple" required>
                                    @foreach (explode(',', @$item->team->language) as $lang)
                                        <option value="{{ $lang }}" selected>{{ $lang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-control-label">@lang('Casts')</label>
                                <small class="text-facebook ml-2 mt-2">@lang('Separate multiple by') <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>

                                <select class="form-control select2-auto-tokenize" name="casts[]" placeholder="Add short words which better describe your site" multiple="multiple" required>
                                    @foreach (explode(',', $item->team->casts) as $cast)
                                        <option value="{{ $cast }}" selected>{{ $cast }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Tags')</label>
                                <small class="text-facebook ml-2 mt-2">@lang('Separate multiple by') <code>,</code>(@lang('comma')) @lang('or') <code>@lang('enter')</code> @lang('key')</small>
                                <select class="form-control select2-auto-tokenize" name="tags[]" placeholder="Add short words which better describe your site" multiple="multiple" required>
                                    @foreach (explode(',', $item->tags) as $tag)
                                        <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>@lang('Total Views')</label>
                                <input class="form-control" name="view" type="text" value="{{ @$item->view }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Status')</label>
                                <input name="status" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Active')" data-off="@lang('Inactive')" type="checkbox" @checked($item->status)>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Featured')</label>
                                <input name="featured" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Yes')" data-off="@lang('No')" type="checkbox" @checked($item->featured)>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Trending')</label>
                                <input name="trending" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Yes')" data-off="@lang('No')" type="checkbox" @checked($item->trending)>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>@lang('Single Section')</label>
                                <input name="single" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Yes')" data-off="@lang('No')" type="checkbox" @checked($item->single)>
                            </div>
                            @if ($item->item_type == Status::SINGLE_ITEM)
                                <div class="col-md-4 form-group">
                                    <label>@lang('Trailer')</label>
                                    <input name="is_trailer" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Yes')" data-off="@lang('No')" type="checkbox" @checked($item->is_trailer)>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <a class="btn btn--sm btn-outline--primary" href="{{ route('admin.item.index') }}"><i class="la la-undo"></i> @lang('Back')</a>
@endpush

@push('style')
    <style>
        .image-upload-preview {
            background-size: cover !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"
            $('[name=category]').no('change', function() {
                var subcategoryOption = '<option>@lang('Select One')</option>';
                var subcategories = $(this).find(':selected').data('subcategories');

                subcategories.forEach(subcategory => {
                    subcategoryOption += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                });

                $('[name=sub_category_id]').html(subcategoryOption);
            });

            $('select[name=category]').val('{{ $item->category->id }}').change();
            $('select[name=sub_category_id]').val('{{ @$item->sub_category->id }}').change();
            $('select[name=version]').val('{{ @$item->version }}');

            let rent = "{{ Status::RENT_VERSION }}";
            let version;
            let rentalArea = $('#rentalArea');
            let currentVersion = "{{ $item->version }}";

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


            $('[name=version]').on('change', function(e) {
                version = Number($(this).val())
                if (!version) {
                    version = $('[name=version]:checked').val();
                }
                if (version != undefined) {
                    if (version == rent) {
                        rentalArea.removeClass('d-none');
                    } else {
                        rentalArea.addClass('d-none');
                    }
                }
            }).change();
        })(jQuery);
    </script>
@endpush
