@extends('layouts.app')

@section('title', 'New user')

@section('content')
    <h1>New user</h1>

    <form method="POST" action="{{ route('admin.users.store') }}" class="card">
        @include('admin.users._form', ['user' => null])

        <button type="submit">Create user</button>
    </form>
@endsection
