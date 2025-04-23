<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pantau Darah - Login</title>
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <!-- Logo tetes darah (SVG) -->
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C12 2 6 8.2 6 13a6 6 0 0012 0c0-4.8-6-11-6-11z" />
                </svg>
            </div>
            <h1>Pantau Darah</h1>
            <p>Silakan login untuk melanjutkan</p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>

                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
