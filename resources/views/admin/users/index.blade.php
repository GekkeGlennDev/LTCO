@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="toolbar">
        <h1>Users</h1>
        <a href="{{ route('admin.users.create') }}" class="button">New user</a>
    </div>

    @if (session('generated_password'))
        <div class="alert alert-success">
            Generated password: <code>{{ session('generated_password') }}</code><br>
            <span class="hint">Share it securely with the user. It will not be shown again.</span>
        </div>
    @endif

    @error('user') <div class="alert alert-error">{{ $message }}</div> @enderror

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->is_admin ? 'Admin' : 'User' }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.users.edit', $user) }}">Edit</a>
                            @unless ($user->is(auth()->user()))
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                      onsubmit="return confirm(@js("Delete {$user->username}?"))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="danger">Delete</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
@endsection
