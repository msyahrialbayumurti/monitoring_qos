@extends('layouts.app')

@section('title', 'Pengaturan NETMONITOR')

@section('styles')
<style>
    .settings-page {
        width: 100%;
        max-width: 1440px;
        margin: 0 auto;
        color: #10295c;
    }

    .settings-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 28px;
        margin-bottom: 22px;
        border: 1px solid #c7dcff;
        border-radius: 20px;
        background:
            radial-gradient(circle at 92% 15%, rgba(76, 145, 255, .22), transparent 30%),
            linear-gradient(115deg, rgba(255,255,255,.98), rgba(222,236,255,.96));
        box-shadow: 0 12px 30px rgba(35, 91, 165, .07);
    }

    .settings-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        color: #4778bd;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .settings-eyebrow::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #2878f0;
        box-shadow: 0 0 0 4px rgba(40,120,240,.12);
    }

    .settings-hero h1 {
        margin: 0 0 8px;
        color: #10295c;
        font-size: clamp(25px, 3vw, 34px);
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -.035em;
    }

    .settings-hero p {
        max-width: 720px;
        margin: 0;
        color: #617da8;
        font-size: 13px;
        line-height: 1.7;
    }

    .settings-date {
        flex-shrink: 0;
        padding: 11px 15px;
        border: 1px solid rgba(255,255,255,.9);
        border-radius: 12px;
        background: rgba(255,255,255,.82);
        color: #315b94;
        font-size: 11px;
        font-weight: 700;
        box-shadow: 0 5px 16px rgba(32,88,160,.06);
    }

    .settings-alert {
        padding: 13px 16px;
        margin-bottom: 18px;
        border: 1px solid;
        border-radius: 12px;
        font-size: 12px;
        line-height: 1.6;
    }

    .settings-alert-success {
        color: #166534;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .settings-alert-error {
        color: #991b1b;
        background: #fff1f2;
        border-color: #fecdd3;
    }

    .settings-alert ul {
        margin: 7px 0 0;
        padding-left: 20px;
    }

    .settings-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(300px, .85fr);
        gap: 20px;
        align-items: start;
    }

    .settings-card {
        min-width: 0;
        overflow: hidden;
        margin-bottom: 20px;
        border: 1px solid #d5e3f8;
        border-radius: 18px;
        background: rgba(255,255,255,.94);
        box-shadow: 0 12px 30px rgba(35, 91, 165, .065);
    }

    .settings-card-header {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 20px 22px;
        border-bottom: 1px solid #e7effb;
        background: linear-gradient(110deg, #fff, #f4f8ff);
    }

    .settings-card-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        border: 1px solid #d4e4ff;
        border-radius: 13px;
        background: #eaf2ff;
        color: #1769e8;
        font-size: 19px;
        font-weight: 800;
    }

    .settings-card-header h2 {
        margin: 0 0 4px;
        color: #10295c;
        font-size: 16px;
        font-weight: 800;
    }

    .settings-card-header p {
        margin: 0;
        color: #7186a8;
        font-size: 11px;
        line-height: 1.6;
    }

    .settings-card-body {
        padding: 22px;
    }

    .settings-form-group {
        margin-bottom: 23px;
    }

    .settings-form-group:last-of-type {
        margin-bottom: 20px;
    }

    .settings-form-group label {
        display: block;
        margin-bottom: 9px;
        color: #213e6a;
        font-size: 12px;
        font-weight: 800;
    }

    .settings-form-group select {
        display: block;
        width: 100%;
        min-height: 45px;
        padding: 0 13px;
        border: 1px solid #d3e0f2;
        border-radius: 11px;
        background: #fbfdff;
        color: #183967;
        font: inherit;
        font-size: 12px;
        outline: none;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .settings-form-group select:focus {
        border-color: #3984f6;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(39,117,238,.11);
    }

    .settings-form-group input[type="text"] {
        display: block;
        width: 100%;
        min-height: 45px;
        padding: 0 13px;
        border: 1px solid #d3e0f2;
        border-radius: 11px;
        background: #fbfdff;
        color: #183967;
        font: inherit;
        font-size: 12px;
        outline: none;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .settings-form-group input[type="text"]:focus {
        border-color: #3984f6;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(39,117,238,.11);
    }

    .settings-toggle {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 13px 14px;
        border: 1px solid #dbe8fb;
        border-radius: 12px;
        background: #f8fbff;
        cursor: pointer;
    }

    .settings-toggle input {
        flex-shrink: 0;
        width: 16px;
        height: 16px;
        margin-top: 2px;
        accent-color: #1769e8;
    }

    .settings-toggle span {
        color: #33547f;
        font-size: 11px;
        line-height: 1.6;
    }

    .settings-toggle strong {
        display: block;
        margin-bottom: 3px;
        color: #173968;
        font-size: 11px;
    }

    .settings-inline-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 9px;
        padding-top: 17px;
        border-top: 1px solid #eaf0fa;
    }

    .settings-test {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 43px;
        padding: 0 17px;
        border: 1px solid #c8dbf7;
        border-radius: 11px;
        background: #f3f8ff;
        color: #1769e8;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: background .2s ease, border-color .2s ease;
    }

    .settings-test:hover {
        border-color: #9dc2f5;
        background: #e9f2ff;
    }

    .settings-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 11px;
        margin-bottom: 16px;
        border: 1px solid;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .settings-badge-ok {
        color: #166534;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .settings-badge-warn {
        color: #92400e;
        background: #fffbeb;
        border-color: #fde68a;
    }

    .settings-help {
        display: block;
        margin-top: 8px;
        color: #7186a8;
        font-size: 10px;
        line-height: 1.7;
    }

    .settings-help::before {
        content: "ⓘ";
        margin-right: 6px;
        color: #2878f0;
        font-weight: 800;
    }

    .settings-actions {
        display: flex;
        justify-content: flex-end;
        padding-top: 17px;
        border-top: 1px solid #eaf0fa;
    }

    .settings-save {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        padding: 0 19px;
        border: 1px solid #1769e8;
        border-radius: 11px;
        background: linear-gradient(135deg, #3284ff, #145ed5);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 8px 18px rgba(23,105,232,.2);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .settings-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 11px 22px rgba(23,105,232,.27);
    }

    .settings-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .settings-info-item {
        min-width: 0;
        padding: 15px;
        border: 1px solid #e0eaf8;
        border-radius: 13px;
        background: linear-gradient(145deg, #fbfdff, #f3f7ff);
    }

    .settings-info-label {
        margin-bottom: 7px;
        color: #7186a8;
        font-size: 10px;
        line-height: 1.5;
    }

    .settings-info-value {
        color: #173968;
        font-size: 13px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .settings-status {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 15px;
        margin-top: 15px;
        border: 1px solid #cfe9dc;
        border-radius: 13px;
        background: #f1fbf6;
    }

    .settings-status-dot {
        flex-shrink: 0;
        width: 9px;
        height: 9px;
        margin-top: 4px;
        border-radius: 50%;
        background: #16b879;
        box-shadow: 0 0 0 4px rgba(22,184,121,.12);
    }

    .settings-status strong {
        display: block;
        margin-bottom: 4px;
        color: #176b4b;
        font-size: 11px;
    }

    .settings-status p {
        margin: 0;
        color: #4c806d;
        font-size: 10px;
        line-height: 1.7;
    }

    .settings-note {
        padding: 15px;
        border: 1px solid #dbe8fb;
        border-radius: 13px;
        background: #f5f9ff;
        color: #58769f;
        font-size: 11px;
        line-height: 1.75;
    }

    .settings-note strong {
        color: #214b83;
    }

    @media (max-width: 1000px) {
        .settings-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .settings-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 21px;
        }

        .settings-card-header,
        .settings-card-body {
            padding: 17px;
        }

        .settings-info-grid {
            grid-template-columns: 1fr;
        }

        .settings-actions {
            justify-content: stretch;
        }

        .settings-save {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .settings-form-group select,
        .settings-save {
            transition: none;
        }
    }
</style>
@endsection

@section('content')
<div class="settings-page">
    <header class="settings-hero">
        <div>
            <div class="settings-eyebrow">Pengaturan Akun</div>
            <h1>Pengaturan NETMONITOR</h1>
            <p>Setelan berikut hanya berlaku untuk akun ini. Kelola jadwal pengukuran QoS, penyegaran dashboard, dan notifikasi WhatsApp sesuai kebutuhan akun Anda.</p>
        </div>
        <div class="settings-date">{{ now()->translatedFormat('d F Y') }}</div>
    </header>

    @if (session('success'))
        <div class="settings-alert settings-alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="settings-alert settings-alert-error" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="settings-alert settings-alert-error" role="alert">
            <strong>Pengaturan gagal disimpan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @unless ($canEdit)
        <div class="settings-alert settings-alert-error" role="alert" style="color:#315b94;background:#f2f7ff;border-color:#cfe0fa;">
            Pengaturan interval hanya dapat diubah oleh administrator. Anda dapat melihat konfigurasi yang sedang berlaku.
        </div>
    @endunless

    <div class="settings-layout">
        <div>
        <section class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon" aria-hidden="true">⚙</div>
                <div>
                    <h2>Konfigurasi Monitoring</h2>
                    <p>Atur frekuensi pengukuran dan pembaruan data dashboard.</p>
                </div>
            </div>

            <div class="settings-card-body">
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="settings-form-group">
                        <label for="monitoring_interval">Interval Monitoring QoS</label>
                        <select id="monitoring_interval" name="monitoring_interval" required @disabled(!$canEdit)>
                            <option value="1" @selected((string) old('monitoring_interval', $interval) === '1')>Setiap 1 menit</option>
                            <option value="5" @selected((string) old('monitoring_interval', $interval) === '5')>Setiap 5 menit</option>
                            <option value="10" @selected((string) old('monitoring_interval', $interval) === '10')>Setiap 10 menit</option>
                            <option value="15" @selected((string) old('monitoring_interval', $interval) === '15')>Setiap 15 menit</option>
                        </select>
                        <small class="settings-help">Mengatur jadwal pengukuran QoS otomatis oleh Laravel Scheduler. Scheduler harus tetap aktif agar pengukuran berjalan.</small>
                    </div>

                    <div class="settings-form-group">
                        <label for="dashboard_refresh_interval">Interval Penyegaran Dashboard</label>
                        <select id="dashboard_refresh_interval" name="dashboard_refresh_interval" required @disabled(!$canEdit)>
                            <option value="15" @selected((string) old('dashboard_refresh_interval', $refreshInterval) === '15')>Setiap 15 detik</option>
                            <option value="30" @selected((string) old('dashboard_refresh_interval', $refreshInterval) === '30')>Setiap 30 detik</option>
                            <option value="60" @selected((string) old('dashboard_refresh_interval', $refreshInterval) === '60')>Setiap 1 menit</option>
                            <option value="120" @selected((string) old('dashboard_refresh_interval', $refreshInterval) === '120')>Setiap 2 menit</option>
                        </select>
                        <small class="settings-help">Mengatur seberapa sering dashboard memeriksa data terbaru. Ini berbeda dari interval pengukuran QoS.</small>
                    </div>

                    @if ($canEdit)
                        <div class="settings-actions">
                            <button type="submit" class="settings-save">
                                <span aria-hidden="true">�o"</span>
                                Simpan Pengaturan
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </section>

        <section class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon" aria-hidden="true">WA</div>
                <div>
                    <h2>Notifikasi WhatsApp</h2>
                    <p>Kirim peringatan otomatis ke WhatsApp saat kondisi QoS berubah atau pengukuran gagal.</p>
                </div>
            </div>

            <div class="settings-card-body">
                @if ($fonnteConfigured)
                    <span class="settings-badge settings-badge-ok">Token Fonnte terpasang</span>
                @else
                    <span class="settings-badge settings-badge-warn">Token Fonnte belum diatur di .env</span>
                @endif

                <form method="POST" action="{{ route('settings.whatsapp.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="settings-form-group">
                        <label for="whatsapp_number">Nomor WhatsApp Penerima</label>
                        <input
                            type="text"
                            id="whatsapp_number"
                            name="whatsapp_number"
                            value="{{ old('whatsapp_number', $whatsappNumber) }}"
                            placeholder="08xxxxxxxxxx"
                            autocomplete="tel"
                        >
                        <small class="settings-help">Gunakan format 08xxxxxxxxxx atau 628xxxxxxxxxx. Nomor ini hanya menerima notifikasi untuk akun Anda.</small>
                    </div>

                    <div class="settings-form-group">
                        <label class="settings-toggle" for="whatsapp_notifications">
                            <input
                                type="checkbox"
                                id="whatsapp_notifications"
                                name="whatsapp_notifications"
                                value="1"
                                @checked(old('whatsapp_notifications', $whatsappNotifications))
                            >
                            <span>
                                <strong>Aktifkan notifikasi WhatsApp</strong>
                                Notif dikirim saat kondisi berubah (mis. BAIK ke BURUK), kembali membaik, atau pengukuran gagal.
                            </span>
                        </label>
                    </div>

                    <div class="settings-inline-actions">
                        <button type="submit" class="settings-save">Simpan Notifikasi</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('settings.whatsapp.test') }}" style="margin-top:12px;">
                    @csrf
                    <div class="settings-inline-actions" style="border-top:0;padding-top:0;">
                        <button type="submit" class="settings-test">Tes Kirim WhatsApp</button>
                    </div>
                </form>
            </div>
        </section>
        </div>

        <div>
            <section class="settings-card">
                <div class="settings-card-header">
                    <div class="settings-card-icon" aria-hidden="true">⌘</div>
                    <div>
                        <h2>Informasi Sistem</h2>
                        <p>Ringkasan konfigurasi NETMONITOR saat ini.</p>
                    </div>
                </div>

                <div class="settings-card-body">
                    <div class="settings-info-grid">
                        <div class="settings-info-item">
                            <div class="settings-info-label">Nama Aplikasi</div>
                            <div class="settings-info-value">NETMONITOR</div>
                        </div>

                        <div class="settings-info-item">
                            <div class="settings-info-label">Metode Monitoring</div>
                            <div class="settings-info-value">Laravel Scheduler</div>
                        </div>

                        <div class="settings-info-item">
                            <div class="settings-info-label">Interval Monitoring QoS</div>
                            <div class="settings-info-value">Setiap {{ $interval }} menit</div>
                        </div>

                        <div class="settings-info-item">
                            <div class="settings-info-label">Penyegaran Dashboard</div>
                            <div class="settings-info-value">
                                @if ($refreshInterval < 60)
                                    Setiap {{ $refreshInterval }} detik
                                @else
                                    Setiap {{ (int) ($refreshInterval / 60) }} menit
                                @endif
                            </div>
                        </div>

                        <div class="settings-info-item">
                            <div class="settings-info-label">Klasifikasi QoS</div>
                            <div class="settings-info-value">TIPHON</div>
                        </div>
                    </div>

                    <div class="settings-status">
                        <span class="settings-status-dot" aria-hidden="true"></span>
                        <div>
                            <strong>Konfigurasi tersedia</strong>
                            <p>Nilai di atas merangkum pengaturan aplikasi yang sedang ditampilkan. Pengukuran otomatis tetap bergantung pada proses Laravel Scheduler yang aktif.</p>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="settings-note">
                <strong>Catatan:</strong> interval pengukuran QoS menentukan kapan sistem menjalankan pengukuran baru, sedangkan interval penyegaran dashboard hanya menentukan seberapa sering tampilan dashboard meminta data terbaru. Keduanya memiliki fungsi berbeda.
            </aside>
        </div>
    </div>
</div>
@endsection
