@extends('layouts.app')

@section('title', 'Tambah Data Sekolah')

@section('styles')
<style>
    .school-form-page {
        --sf-blue: #1768e5;
        --sf-blue-dark: #104fb5;
        --sf-ink: #102b59;
        --sf-muted: #6f82a3;
        --sf-line: #dce7f8;
        width: 100%;
        color: var(--sf-ink);
    }

    .school-form-page * {
        box-sizing: border-box;
    }

    .school-form-hero {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 22px;
        padding: 25px 28px;
        margin-bottom: 22px;
        overflow: hidden;
        border: 1px solid rgba(190, 213, 249, .9);
        border-radius: 20px;
        background:
            radial-gradient(circle at 88% 12%, rgba(91, 157, 255, .20), transparent 29%),
            linear-gradient(120deg, rgba(255,255,255,.97), rgba(239,246,255,.94));
        box-shadow: 0 12px 30px rgba(34, 83, 153, .07);
    }

    .school-form-hero::after {
        content: "";
        position: absolute;
        right: -34px;
        bottom: -75px;
        width: 190px;
        height: 190px;
        border: 1px solid rgba(23,104,229,.12);
        border-radius: 50%;
        box-shadow: 0 0 0 20px rgba(23,104,229,.035), 0 0 0 42px rgba(23,104,229,.025);
        pointer-events: none;
    }

    .school-form-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        color: #507bbd;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .17em;
        text-transform: uppercase;
    }

    .school-form-eyebrow-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #2477f3;
        box-shadow: 0 0 0 4px rgba(36,119,243,.12);
    }

    .school-form-hero h1 {
        margin: 0 0 8px;
        color: var(--sf-ink);
        font-size: clamp(25px, 2.5vw, 32px);
        font-weight: 800;
        letter-spacing: -.035em;
        line-height: 1.15;
    }

    .school-form-hero p {
        max-width: 650px;
        margin: 0;
        color: var(--sf-muted);
        font-size: 13px;
        line-height: 1.7;
    }

    .school-form-back {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border: 1px solid #d4e2f8;
        border-radius: 11px;
        background: rgba(255,255,255,.88);
        color: #315783;
        font-size: 12px;
        font-weight: 750;
        text-decoration: none;
        white-space: nowrap;
        transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
    }

    .school-form-back:hover {
        transform: translateY(-2px);
        background: #fff;
        box-shadow: 0 7px 18px rgba(36, 83, 150, .10);
    }

    .school-form-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(245px, .85fr);
        align-items: start;
        gap: 20px;
    }

    .school-form-card,
    .school-form-aside {
        overflow: hidden;
        border: 1px solid var(--sf-line);
        border-radius: 18px;
        background: rgba(255,255,255,.94);
        box-shadow: 0 12px 30px rgba(34, 83, 153, .065);
    }

    .school-form-card-header {
        display: flex;
        align-items: flex-start;
        gap: 13px;
        padding: 22px 24px;
        border-bottom: 1px solid #e8effa;
        background: linear-gradient(120deg, #fff, #f7faff);
    }

    .school-form-icon {
        display: inline-flex;
        flex: 0 0 44px;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border: 1px solid #d7e6ff;
        border-radius: 14px;
        background: #eaf2ff;
        color: var(--sf-blue);
        font-size: 21px;
    }

    .school-form-card-header h2 {
        margin: 1px 0 5px;
        color: var(--sf-ink);
        font-size: 16px;
        font-weight: 800;
    }

    .school-form-card-header p {
        margin: 0;
        color: var(--sf-muted);
        font-size: 11px;
        line-height: 1.6;
    }

    .school-form-body {
        padding: 24px;
    }

    .school-form-group {
        margin-bottom: 21px;
    }

    .school-form-label-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }

    .school-form-group label {
        display: block;
        color: #243e67;
        font-size: 12px;
        font-weight: 750;
    }

    .school-form-required {
        color: #e24a5a;
    }

    .school-form-control-wrap {
        position: relative;
    }

    .school-form-control-icon {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        color: #7d98c0;
        font-size: 15px;
        pointer-events: none;
    }

    .school-form-group input {
        display: block;
        width: 100%;
        min-height: 46px;
        padding: 11px 13px 11px 42px;
        border: 1px solid #d8e3f2;
        border-radius: 11px;
        outline: none;
        background: #fbfdff;
        color: #1d355b;
        font: inherit;
        font-size: 12px;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .school-form-group input::placeholder {
        color: #9aabc2;
    }

    .school-form-group input:hover {
        border-color: #b8cdeb;
    }

    .school-form-group input:focus {
        border-color: #4389f4;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(40, 119, 235, .11);
    }

    .school-form-help {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-top: 8px;
        color: #7a8eaa;
        font-size: 10px;
        line-height: 1.65;
    }

    .school-form-help span {
        color: #4280d8;
        font-weight: 800;
    }

    .school-form-error {
        margin-top: 7px;
        color: #c53030;
        font-size: 11px;
    }

    .school-form-control.is-invalid {
        border-color: #e76a76;
        background: #fffafa;
    }

    .school-form-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 18px;
        padding: 14px 16px;
        border: 1px solid #fecdd3;
        border-radius: 12px;
        background: #fff1f2;
        color: #9f2635;
        font-size: 12px;
        line-height: 1.6;
    }

    .school-form-alert strong {
        display: block;
        margin-bottom: 3px;
    }

    .school-form-alert ul {
        margin: 4px 0 0;
        padding-left: 18px;
    }

    .school-form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid #edf2fa;
    }

    .school-form-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        padding: 0 17px;
        border: 1px solid transparent;
        border-radius: 11px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .school-form-btn:hover {
        transform: translateY(-1px);
    }

    .school-form-btn-primary {
        border-color: #1768e5;
        background: linear-gradient(135deg, #3185ff, #125bd0);
        color: #fff;
        box-shadow: 0 8px 16px rgba(23,104,229,.2);
    }

    .school-form-btn-primary:hover {
        box-shadow: 0 11px 22px rgba(23,104,229,.27);
    }

    .school-form-btn-secondary {
        border-color: #d8e3f2;
        background: #fff;
        color: #496486;
    }

    .school-form-btn-secondary:hover {
        background: #f4f8ff;
    }

    .school-form-aside {
        padding: 22px;
        background:
            radial-gradient(circle at 100% 0, rgba(69,141,255,.14), transparent 35%),
            linear-gradient(155deg, #fff, #f3f8ff);
    }

    .school-form-aside-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        margin-bottom: 16px;
        border: 1px solid #d8e7ff;
        border-radius: 15px;
        background: #e8f1ff;
        color: #1768e5;
        font-size: 23px;
    }

    .school-form-aside h3 {
        margin: 0 0 8px;
        color: var(--sf-ink);
        font-size: 15px;
        font-weight: 800;
    }

    .school-form-aside > p {
        margin: 0 0 18px;
        color: var(--sf-muted);
        font-size: 11px;
        line-height: 1.7;
    }

    .school-form-tip {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 0;
        border-top: 1px solid #e3ecf9;
    }

    .school-form-tip-icon {
        display: inline-flex;
        flex: 0 0 28px;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 9px;
        background: #e6f0ff;
        color: #286fce;
        font-size: 12px;
        font-weight: 800;
    }

    .school-form-tip strong {
        display: block;
        margin-bottom: 3px;
        color: #294568;
        font-size: 11px;
    }

    .school-form-tip small {
        display: block;
        color: #7a8eaa;
        font-size: 10px;
        line-height: 1.6;
    }

    @media (max-width: 980px) {
        .school-form-layout {
            grid-template-columns: minmax(0, 1fr);
        }

        .school-form-aside {
            display: grid;
            grid-template-columns: 48px minmax(0, 1fr);
            column-gap: 14px;
            align-items: start;
        }

        .school-form-aside-icon {
            grid-row: span 2;
        }

        .school-form-aside h3 {
            align-self: end;
        }

        .school-form-aside > p {
            margin-bottom: 14px;
        }

        .school-form-tips {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 640px) {
        .school-form-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 21px;
        }

        .school-form-card-header,
        .school-form-body {
            padding: 18px;
        }

        .school-form-aside {
            padding: 18px;
        }

        .school-form-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .school-form-btn {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .school-form-back,
        .school-form-btn,
        .school-form-group input {
            transition: none;
        }
    }
</style>
@endsection

@section('content')
<div class="school-form-page">
    <header class="school-form-hero">
        <div>
            <div class="school-form-eyebrow">
                <span class="school-form-eyebrow-dot"></span>
                Manajemen Infrastruktur
            </div>
            <h1>Tambah Data Sekolah</h1>
            <p>Daftarkan sekolah sebagai objek pemantauan kualitas jaringan internet dan pengukuran QoS pada NETMONITOR.</p>
        </div>

        <a href="{{ route('schools.index') }}" class="school-form-back">
            <span aria-hidden="true">←</span> Kembali ke Data Sekolah
        </a>
    </header>

    @if ($errors->any())
        <div class="school-form-alert" role="alert">
            <span aria-hidden="true">⚠</span>
            <div>
                <strong>Data belum dapat disimpan.</strong>
                Periksa kembali informasi berikut:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="school-form-layout">
        <section class="school-form-card">
            <div class="school-form-card-header">
                <div class="school-form-icon" aria-hidden="true">＋</div>
                <div>
                    <h2>Informasi Sekolah</h2>
                    <p>Lengkapi data sekolah dan target jaringan yang akan dipantau.</p>
                </div>
            </div>

            <div class="school-form-body">
                <form action="{{ route('schools.store') }}" method="POST">
                    @csrf

                    <div class="school-form-group">
                        <div class="school-form-label-row">
                            <label for="nama_sekolah">Nama Sekolah <span class="school-form-required">*</span></label>
                        </div>
                        <div class="school-form-control-wrap">
                            <span class="school-form-control-icon" aria-hidden="true">🏫</span>
                            <input
                                class="school-form-control @error('nama_sekolah') is-invalid @enderror"
                                type="text"
                                id="nama_sekolah"
                                name="nama_sekolah"
                                value="{{ old('nama_sekolah') }}"
                                placeholder="Contoh: SMAN 1 BUKIT BATU"
                                maxlength="255"
                                autocomplete="organization"
                                required
                                aria-describedby="nama-sekolah-help"
                            >
                        </div>
                        <div class="school-form-help" id="nama-sekolah-help">
                            <span>i</span>
                            Gunakan nama resmi sekolah agar mudah dikenali pada laporan monitoring.
                        </div>
                        @error('nama_sekolah')
                            <div class="school-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="school-form-group">
                        <div class="school-form-label-row">
                            <label for="ip_target">IP Target <span class="school-form-required">*</span></label>
                        </div>
                        <div class="school-form-control-wrap">
                            <span class="school-form-control-icon" aria-hidden="true">⌁</span>
                            <input
                                class="school-form-control @error('ip_target') is-invalid @enderror"
                                type="text"
                                id="ip_target"
                                name="ip_target"
                                value="{{ old('ip_target') }}"
                                placeholder="Contoh: 192.168.1.1"
                                maxlength="255"
                                spellcheck="false"
                                autocomplete="off"
                                required
                                aria-describedby="ip-target-help"
                            >
                        </div>
                        <div class="school-form-help" id="ip-target-help">
                            <span>i</span>
                            Masukkan alamat IP perangkat atau target jaringan yang memang dapat dijangkau dari mesin monitoring.
                        </div>
                        @error('ip_target')
                            <div class="school-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="school-form-group">
                        <div class="school-form-label-row">
                            <label for="bandwidth">Kapasitas Bandwidth <span class="school-form-required">*</span></label>
                        </div>
                        <div class="school-form-control-wrap">
                            <span class="school-form-control-icon" aria-hidden="true">↕</span>
                            <input
                                class="school-form-control @error('bandwidth') is-invalid @enderror"
                                type="number"
                                id="bandwidth"
                                name="bandwidth"
                                value="{{ old('bandwidth') }}"
                                placeholder="Contoh: 100"
                                step="0.01"
                                min="1"
                                required
                                aria-describedby="bandwidth-help"
                            >
                        </div>
                        <div class="school-form-help" id="bandwidth-help">
                            <span>i</span>
                            Masukkan kapasitas layanan internet sekolah dalam Mbps, sesuai data langganan yang tersedia.
                        </div>
                        @error('bandwidth')
                            <div class="school-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="school-form-group">
                        <div class="school-form-label-row">
                            <label for="paket_isp">Paket ISP <span class="school-form-required">*</span></label>
                        </div>
                        <div class="school-form-control-wrap">
                            <span class="school-form-control-icon" aria-hidden="true">◎</span>
                            <input
                                class="school-form-control @error('paket_isp') is-invalid @enderror"
                                type="text"
                                id="paket_isp"
                                name="paket_isp"
                                value="{{ old('paket_isp') }}"
                                placeholder="Contoh: IndiHome 100 Mbps"
                                maxlength="255"
                                required
                                aria-describedby="paket-isp-help"
                            >
                        </div>
                        <div class="school-form-help" id="paket-isp-help">
                            <span>i</span>
                            Tuliskan nama paket atau penyedia layanan internet yang digunakan sekolah.
                        </div>
                        @error('paket_isp')
                            <div class="school-form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="school-form-actions">
                        <a href="{{ route('schools.index') }}" class="school-form-btn school-form-btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="school-form-btn school-form-btn-primary">
                            <span aria-hidden="true">✓</span> Simpan Data Sekolah
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <aside class="school-form-aside">
            <div class="school-form-aside-icon" aria-hidden="true">⌁</div>
            <h3>Petunjuk Pengisian</h3>
            <p>Pastikan informasi yang dimasukkan sesuai kondisi jaringan sekolah agar hasil pemantauan QoS lebih relevan.</p>

            <div class="school-form-tips">
                <div class="school-form-tip">
                    <div class="school-form-tip-icon">01</div>
                    <div>
                        <strong>Nama resmi sekolah</strong>
                        <small>Gunakan nama yang konsisten dengan identitas sekolah pada data administrasi.</small>
                    </div>
                </div>
                <div class="school-form-tip">
                    <div class="school-form-tip-icon">02</div>
                    <div>
                        <strong>IP target dapat dijangkau</strong>
                        <small>Pastikan target dapat diakses dari komputer yang menjalankan proses monitoring.</small>
                    </div>
                </div>
                <div class="school-form-tip">
                    <div class="school-form-tip-icon">03</div>
                    <div>
                        <strong>Bandwidth sesuai layanan</strong>
                        <small>Isi dengan kapasitas internet yang benar. Nilai ini menjadi informasi konfigurasi sekolah.</small>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
