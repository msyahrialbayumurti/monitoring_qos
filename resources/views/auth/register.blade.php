@extends('layouts.auth')

@section('title', 'Daftar Sekolah · NETMONITOR')

@section('content')
    <div class="auth-card auth-card-wide">
        <span class="auth-card-badge">Registrasi sekolah</span>

        <h2>Daftarkan sekolah Anda</h2>
        <p class="auth-sub">
            Buat akun sekolah. IP target, bandwidth, dan paket ISP akan dilengkapi
            otomatis setelah agent terpasang atau oleh admin.
        </p>

        @if ($errors->any())
            <div class="auth-alert" role="alert">
                <span aria-hidden="true">!</span>
                <div>
                    <strong>Pendaftaran belum dapat diproses.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <div class="auth-section-label">Akun Penanggung Jawab</div>

            <div class="auth-grid">
                <div class="auth-field">
                    <label for="name">Nama Penanggung Jawab <span class="auth-required">*</span></label>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon" aria-hidden="true">◉</span>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Nama lengkap"
                            maxlength="255"
                            autocomplete="name"
                            class="@error('name') is-invalid @enderror"
                            required
                        >
                    </div>
                    @error('name')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

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
                            maxlength="255"
                            autocomplete="email"
                            class="@error('email') is-invalid @enderror"
                            required
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
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            class="@error('password') is-invalid @enderror"
                            required
                        >
                    </div>
                    @error('password')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="auth-field">
                    <label for="password_confirmation">Konfirmasi Password <span class="auth-required">*</span></label>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon" aria-hidden="true">••</span>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi password"
                            autocomplete="new-password"
                            required
                        >
                    </div>
                </div>
            </div>

            <div class="auth-section-label">Data Sekolah</div>

            <div class="auth-grid">
                <div class="auth-field auth-span-2">
                    <label for="nama_sekolah">Nama Sekolah <span class="auth-required">*</span></label>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon" aria-hidden="true">▣</span>
                        <input
                            type="text"
                            id="nama_sekolah"
                            name="nama_sekolah"
                            value="{{ old('nama_sekolah') }}"
                            placeholder="Contoh: SMAN 1 Bukit Batu"
                            maxlength="255"
                            autocomplete="organization"
                            class="@error('nama_sekolah') is-invalid @enderror"
                            required
                        >
                    </div>
                    @error('nama_sekolah')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="auth-submit" style="margin-top:24px;">
                Buat Akun &amp; Mulai Monitoring
                <span aria-hidden="true">→</span>
            </button>
        </form>

        <p class="auth-switch">
            Sudah punya akun?
            <a href="{{ route('login') }}">Masuk di sini</a>
        </p>
    </div>
@endsection
