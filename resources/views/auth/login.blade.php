<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — POS Friday Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="login-page">
    <div class="login-card">

        {{-- Logo --}}
        <div class="login-brand">
            <img src="{{ asset('images/fridaylogo.png') }}" alt="Friday Logo" style="height: 42px; max-width: 100%; object-fit: contain;">
        </div>

        <p class="login-title">Masuk ke akun Anda</p>

        <form method="POST" action="{{ route('login.post') }}" class="login-form">
            @csrf

            <div>
                <label class="form-label" for="email">Email</label>
                <input
                    id="email" type="email" name="email"
                    value="{{ old('email') }}" required autofocus
                    placeholder="nama@email.com"
                    autocomplete="email"
                    class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                >
                @error('email')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label" for="password">Password</label>
                <input
                    id="password" type="password" name="password"
                    required placeholder="••••••••"
                    autocomplete="current-password"
                    class="form-input"
                >
            </div>

            <div class="login-remember">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:9px 16px; font-size:13px; margin-top:4px;">
                Masuk
            </button>
        </form>
    </div>
</div>
</body>
</html>
