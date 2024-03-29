<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Reminder App</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @livewireStyles
    @yield('stylesheets')
</head>
<body class="d-flex flex-column h-100">

<header class="p-3 bg-dark text-white">
    <div class="container">
        <div class="d-flex flex-wrap flex-row-reverse align-items-center justify-content-center justify-content-lg-start">
            @guest
                <div class="float-end">
                    <a href="/login" class="btn btn-outline-light me-2">Login</a>
                    <a href="/register" class="btn btn-warning">Register</a>
                </div>
            @else
                <div class="d-flex flex-row-reverse flex-nowrap align-content-center justify-content-center align-self-center">
                    <livewire:auth.logout />
                    <span class="m-3 mb-2 mt-2"> Your welcome {{ auth()->user()->name }}</span>&nbsp;
                </div>
            @endif

            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 d-flex flex-row bd-highlight mb-md-0">
                <li><a href="/home" class="nav-link px-2 text-white">Home</a></li>
                @if(auth()->check())
                    <li><a href="/reminders" class="nav-link px-2 text-white">Reminders</a></li>
                @endif
            </ul>

            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap">
                    <use xlink:href="#bootstrap"></use>
                </svg>
            </a>

        </div>
    </div>
</header>

@if(isset($slot))
    {{ $slot }}
@endif

{{--<footer class="footer mt-auto py-5 bg-light">--}}
{{--    <div class="container">--}}
{{--        <span class="text-muted">Place sticky footer content here.</span>--}}
{{--    </div>--}}
{{--</footer>--}}

@livewireScripts

</body>

</html>
