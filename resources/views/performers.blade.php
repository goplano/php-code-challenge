<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Template Code Challenge</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

</head>
<body class="antialiased">
<div id="app"  class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
    @if (Route::has('login'))
        <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
            @auth
                <a href="{{ url('/home') }}" class="text-sm text-gray-700 underline">Home</a>
            @else
                <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Login</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
                @endif
            @endauth
        </div>
    @endif
    <div class="mx-auto sm:px-6 lg:px-8">
        <performer-table class="">Performer Totals by Task</performer-table>

    </div>
</div>
<script src="{{mix('js/app.js')}}"></script>
</body>
</html>
<script>

</script>
