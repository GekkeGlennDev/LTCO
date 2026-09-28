@csrf

<div class="field">
    <label for="name">Name</label>
    <input id="name" type="text" name="name" value="{{ old('name', $user?->name) }}" required>
    @error('name') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email', $user?->email) }}" required>
    @error('email') <div class="error">{{ $message }}</div> @enderror
</div>

@if($user instanceof \App\Models\User)
    <div class="field">
        <label class="inline">
            <input type="checkbox" name="generate_password" value="1" @checked(old('generate_password'))>
            Generate new password <span class="hint">(shown once after saving, the fields below are ignored)</span>
        </label>
    </div>
@endif

<div class="field">
    <input type="hidden" name="is_admin" value="0">
    <label class="inline">
        <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $user?->is_admin))>
        Admin (can manage users and allowed IPs)
    </label>
    @error('is_admin') <div class="error">{{ $message }}</div> @enderror
</div>
