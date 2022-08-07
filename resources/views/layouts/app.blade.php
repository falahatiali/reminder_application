<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @livewireStyles
    <script src="{{ asset("js/app.js") }}"></script>
    @yield('stylesheets')
</head>
<body class="d-flex flex-column h-100">

<header class="p-3 bg-dark text-white">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            @guest
                <div class="float-start">
                    <a href="/login" class="btn btn-outline-light me-2">ورود</a>
                    <a href="/register" class="btn btn-warning">ثبت نام</a>
                </div>
            @else
                <div class="d-flex flex-row-reverse flex-nowrap align-content-center justify-content-center align-self-center">
                    <span class="mt-2"> خوش آمدید {{ auth()->user()->name }}</span>&nbsp;
                    <livewire:auth.logout />
                </div>
            @endif

            <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3 text-end">
                <input type="search" class="form-control form-control-dark" placeholder="جستجو..." aria-label="Search">
            </form>

            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 d-flex flex-row-reverse bd-highlight mb-md-0">
                <li><a href="/home" class="nav-link px-2 text-white">صفحه اصلی</a></li>
                <li><a href="#" class="nav-link px-2 text-white">مقالات</a></li>
                <li><a href="/news" class="nav-link px-2 text-white">اخبار</a></li>
                <li><a href="#" class="nav-link px-2 text-white">آموزش</a></li>
                <li><a href="#" class="nav-link px-2 text-white">درباره ما</a></li>
            </ul>

            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap">
                    <use xlink:href="#bootstrap"></use>
                </svg>
            </a>

        </div>
    </div>
</header>
{{ $slot }}

<footer class="footer mt-auto py-5 bg-light">
    <div class="container">
        <span class="text-muted">Place sticky footer content here.</span>
    </div>
</footer>

@livewireScripts

</body>

</html>
