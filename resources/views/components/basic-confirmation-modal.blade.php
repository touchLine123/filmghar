<div class="modal alert-modal" id="basicConfirmationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <span class="alert-icon"><i class="fas fa-question-circle"></i></span>
                    <p class="modal-description">@lang('Confirmation Alert!')</p>
                    <p class="modal--text">@lang('Are you sure to disbaled this party room?')</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--dark btn--sm" data-bs-dismiss="modal" type="button">@lang('No')</button>
                    <button class="btn btn--base btn--sm" type="submit">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.basicConfirmationBtn', function() {
                var modal = $('#basicConfirmationModal');
                let data = $(this).data();
                modal.find('.modal--text').text(`${data.question}`);
                modal.find('form').attr('action', `${data.action}`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
