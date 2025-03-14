<x-auth-layout>
    @section('title')
        Masuk dengan SSO
    @endsection

    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!-- Left Content -->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1 bg-light">
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <div class="w-100 w-md-50 w-lg-500px p-10">
                        <div class="text-center mb-11">
                            <h1 class="text-dark fw-bold mb-3">Masuk dengan SSO</h1>
                            <p class="text-muted fw-semibold">Gunakan Single Sign-On untuk akses cepat</p>
                        </div>
                        <div class="d-grid mb-10">
                            <!-- Redirect to SSO for OAuth -->
                            <a href="{{ config('sso.url') }}/auth/v2/login?client_id={{ config('sso.client_id') }}&redirect_uri={{ config('sso.app_callback_url') }}&response_type=code&scope="
                                class="btn btn-primary py-3 px-6 fw-bold shadow-sm">
                                Login dengan SSO
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Background -->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2"
                style="background-image: url({{ asset('assets/media/auth/bg3-flip.jpg') }}); background-size: cover; background-repeat: no-repeat;">
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100 text-center">
                    <a href="#" class="mb-10">
                        <img alt="Logo" src="{{ asset('assets/media/logos/m-mart.svg') }}" class="h-60px h-lg-75px" />
                    </a>
                    <img class="mx-auto w-275px w-md-10 w-xl-250px w-xxl-500px mb-10 mb-lg-20"
                        src="{{ asset('assets/media/illustrations/sigma-1/13.png') }}" alt="" />
                    <h1 class="text-white fs-2qx fw-bolder text-center mb-7">{{ env('APP_NAME')}}</h1>
                    <div class="d-none d-lg-block text-white fs-base text-center">
                            Selamat datang di Aplikasi {{ env('APP_NAME')}} ! Masuk dengan akun Anda untuk akses cepat <br /> dan mudah ke semua fitur aplikasi.
                            <span class="opacity-75-hover text-primary fw-bold me-1">Satu login</span>, semua kebutuhan Anda terpenuhi.
                            <br />
                            Mulai sekarang!
                            <span class="opacity-75-hover text-primary fw-bold me-1">Masuk sekarang</span>
                            dan nikmati kemudahan dalam satu platform.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-primary {
            background-color: #0056b3;
            border-color: #004085;
        }

        .btn-primary:hover {
            background-color: #004085;
            border-color: #003366;
        }

        .bg-light {
            background-color: #f9f9f9 !important;
        }

    </style>
</x-auth-layout>
