<header class="header-section">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-xl p-0">
                            <a class="site-logo site-title" href="{{ route('home') }}">
                                <img src="{{ siteLogo() }}" alt="site-logo">
                            </a>

                            <div class="search-bar d-block d-xl-none ml-auto">
                                <a href="#0"><i class="fas fa-search"></i></a>
                                <div class="header-top-search-area">
                                    <form class="header-search-form" action="{{ route('search') }}">
                                        <input name="search" type="search" placeholder="@lang('Search here')...">
                                        <button class="header-search-btn" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <button class="navbar-toggler ml-auto" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" type="button" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="las la-bars"></span>
                            </button>

                            <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                                <div class="header-action">
                                    <a class="btn btn--base" href="{{ route('vendor.login') }}">
                                        <i class="las la-user-circle"></i> @lang('Login')
                                    </a>
                                </div>
                            </div>
                        </nav>
                </div>
            </div>
        </div>
    </div>
</header>


@push('style')
    <sty
     @endpush
