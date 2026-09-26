@extends('layouts.app')

@section('title', 'Log in')
@section('container-class', 'narrow')

@section('content')
    <h1>Log in</h1>

    <form method="POST" action="{{ route('login') }}" class="card">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label class="inline">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                Remember me
            </label>
        </div>

        <button type="submit">Log in</button>
    </form>
@endsection
