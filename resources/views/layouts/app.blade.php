<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, sans-serif; color: #1f2328; background: #f6f8fa; line-height: 1.5; }
        header { background: #fff; border-bottom: 1px solid #d0d7de; }
        header .container { display: flex; align-items: center; gap: 1.5rem; padding-block: .75rem; }
        header nav { display: flex; gap: 1rem; flex: 1; }
        .container { max-width: 960px; margin: 0 auto; padding: 1.5rem 1rem; }
        .narrow { max-width: 380px; }
        a { color: #0969da; }
        h1 { font-size: 1.5rem; margin-top: 0; }
        .card { background: #fff; border: 1px solid #d0d7de; border-radius: 6px; padding: 1.25rem; margin-bottom: 1rem; }
        label { display: block; font-weight: 600; margin-bottom: .25rem; }
        label.inline { display: flex; gap: .5rem; align-items: center; font-weight: normal; }
        input[type=text], input[type=password], input[type=email], input[type=number], select { width: 100%; padding: .5rem; border: 1px solid #d0d7de; border-radius: 6px; font: inherit; }
        .field { margin-bottom: 1rem; }
        .hint { color: #59636e; font-size: .875rem; }
        .error { color: #d1242f; font-size: .875rem; margin-top: .25rem; }
        .alert { padding: .75rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
        .alert-success { background: #dafbe1; border: 1px solid #4ac26b; }
        .alert-error { background: #ffebe9; border: 1px solid #ff8182; }
        button, .button { display: inline-block; padding: .45rem 1rem; border-radius: 6px; border: 1px solid #1f883d; background: #1f883d; color: #fff; font: inherit; cursor: pointer; text-decoration: none; }
        button.link { background: none; border: none; color: #0969da; padding: 0; text-decoration: underline; }
        button.danger { background: #fff; color: #d1242f; border-color: #d0d7de; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: .5rem; border-bottom: 1px solid #d0d7de; vertical-align: middle; }
        td.actions { display: flex; gap: .75rem; justify-content: flex-end; align-items: center; }
        form.inline { display: inline; }
        .converter { display: grid; grid-template-columns: 280px 1fr; gap: 1rem; align-items: start; }
        @media (max-width: 700px) { .converter { grid-template-columns: 1fr; } }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    </style>
</head>
<body>
    @auth
        <header>
            <div class="container">
                <strong>{{ config('app.name') }}</strong>
                <nav>
                    <a href="{{ route('home') }}">Home</a>
                    @can('admin')
                        <a href="{{ route('admin.users.index') }}">Users</a>
                        <a href="{{ route('admin.allowed-ips.index') }}">Allowed IPs</a>
                    @endcan
                </nav>
                <span class="hint">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="link">Log out</button>
                </form>
            </div>
        </header>
    @endauth

    <main class="container @yield('container-class')">
        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
