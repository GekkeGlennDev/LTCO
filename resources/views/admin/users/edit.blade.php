@extends('layouts.app')

@section('title', 'Edit user')

@section('content')
    <h1>Edit {{ $user->username }}</h1>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="card">
        @method('PUT')
        @include('admin.users._form', ['user' => $user])

        <button type="submit">Save</button>
    </form>
@endsection
