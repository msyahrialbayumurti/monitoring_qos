@extends('layouts.auth')

@section('title', 'Masuk · NETMONITOR')

@section('content')
    <div class="auth-card">
        <span class="auth-card-badge">Masuk ke akun</span>

        <h2>Selamat datang kembali</h2>
        <p class="auth-sub">
            Masuk untuk melihat kondisi jaringan dan hasil monitoring sekolah Anda.
        </p>

        @if (session('success'))
            <div class="auth-alert" style="border-color:#bbf7d0;background:#f0fdf4;color:#166534;">
                <span aria-hidden="true">✓</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="auth-alert" role="alert">
                <span aria-hidden="true">!</span>
                <div>
                    <strong>Gagal masuk.</strong>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="auth-field">
                <label for="email">Email <span class="auth-required">*</span></label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon" aria-hidden="true">@</span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@sekolah.sch.id"
                        autocomplete="email"
                        class="@error('email') is-invalid @enderror"
                        required
                        autofocus
                    >
                </div>
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-field">
                <label for="password">Password <span class="auth-required">*</span></label>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon" aria-hidden="true">••</span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        class="@error('password') is-invalid @enderror"
                        required
                    >
                </div>
                @error('password')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-row">
                <label class="auth-check">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="auth-submit">
                Masuk ke Dashboard
                <span aria-hidden="true">→</span>
            </button>
        </form>

        <p class="auth-switch">
            Belum punya akun sekolah?
            <a href="{{ route('register') }}">Daftar sekarang</a>
        </p>
    </div>
@endsection
