@extends('layouts.app')

@section('title', 'Allowed IPs')

@section('content')
    <h1>Allowed IPs</h1>

    <p class="hint">Only clients with an IP address in this list can reach the application. Your current IP is <strong>{{ $currentIp }}</strong>.</p>

    <form method="POST" action="{{ route('admin.allowed-ips.store') }}" class="card">
        @csrf

        <div class="field">
            <label for="range">IP address or subnet</label>
            <input id="range" type="text" name="range" value="{{ old('range') }}" required placeholder="192.168.1.10 or 192.168.1.0/24">
            @error('range') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="description">Description <span class="hint">(optional)</span></label>
            <input id="description" type="text" name="description" value="{{ old('description') }}" placeholder="Office network">
            @error('description') <div class="error">{{ $message }}</div> @enderror
        </div>

        <button type="submit">Add</button>
    </form>

    @error('allowed_ip') <div class="alert alert-error">{{ $message }}</div> @enderror

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>IP / subnet</th>
                    <th>Description</th>
                    <th>Added</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($allowedIps as $allowedIp)
                    <tr>
                        <td><code>{{ $allowedIp->range }}</code></td>
                        <td>{{ $allowedIp->description }}</td>
                        <td>{{ $allowedIp->created_at->toDateString() }}</td>
                        <td class="actions">
                            <form method="POST" action="{{ route('admin.allowed-ips.destroy', $allowedIp) }}" class="inline"
                                  onsubmit="return confirm(@js("Remove {$allowedIp->range}?"))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="hint">No entries.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
