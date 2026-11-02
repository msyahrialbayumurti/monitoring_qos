@extends('layouts.app')

@section('title', 'Data Sekolah')

@section('styles')
<style>
    .school-page {
        width: 100%;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 20px;
    }

    .page-header h1 {
        font-size: 24px;
        line-height: 1.25;
        color: #10295c;
        margin: 0 0 6px;
    }

    .page-header p {
        font-size: 12px;
        color: #718096;
        margin: 0;
    }

    .btn-primary,
    .btn-secondary,
    .btn-danger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 15px;
        border-radius: 7px;
        text-decoration: none;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid transparent;
        cursor: pointer;
        box-sizing: border-box;
    }

    .btn-primary {
        background: #1266d8;
        color: #ffffff;
        border-color: #1266d8;
    }

    .btn-primary:hover {
        background: #0f58bd;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border-color: #e2e8f0;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    .alert-success {
        margin-bottom: 18px;
        padding: 12px 14px;
        background: #ecfdf5;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        color: #15803d;
        font-size: 11px;
    }

    .school-card {
        background: #ffffff;
        border: 1px solid #e3e8ef;
        border-radius: 10px;
        overflow: hidden;
    }

    .school-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 17px 20px;
        border-bottom: 1px solid #e5eaf0;
    }

    .school-card-title {
        font-size: 14px;
        font-weight: 700;
        color: #243b64;
    }

    .school-card-subtitle {
        margin-top: 4px;
        font-size: 10px;
        color: #8a94a6;
    }

    .school-count {
        font-size: 10px;
        color: #718096;
        white-space: nowrap;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .school-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
    }

    .school-table th {
        background: #f8fafc;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        color: #718096;
        padding: 12px 14px;
        border-bottom: 1px solid #e5eaf0;
        white-space: nowrap;
    }

    .school-table td {
        font-size: 11px;
        padding: 14px;
        border-bottom: 1px solid #edf0f4;
        color: #334155;
        white-space: nowrap;
        vertical-align: middle;
    }

    .school-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .school-name {
        font-weight: 700;
        color: #173a70;
    }

    .school-ip {
        font-family: Consolas, monospace;
        color: #475569;
    }

    .bandwidth {
        font-weight: 600;
    }

    .status-active,
    .status-inactive {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 6px;
        font-size: 9px;
        font-weight: 700;
    }

    .status-active {
        background: #dcfce7;
        color: #15803d;
    }

    .status-inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    .status-agent-online,
    .status-agent-offline,
    .status-agent-none {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 6px;
        font-size: 9px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-agent-online {
        background: #dcfce7;
        color: #15803d;
    }

    .status-agent-offline {
        background: #fff1f2;
        color: #be123c;
    }

    .status-agent-none {
        background: #f1f5f9;
        color: #64748b;
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .action-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        padding: 0 9px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 10px;
        font-weight: 700;
        border: 1px solid;
        cursor: pointer;
    }

    .action-edit {
        color: #1266d8;
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .action-edit:hover {
        background: #dbeafe;
    }

    .action-delete {
        color: #dc2626;
        background: #fff1f2;
        border-color: #fecdd3;
    }

    .action-delete:hover {
        background: #ffe4e6;
    }

    .empty-state {
        text-align: center;
        padding: 45px 15px;
        color: #94a3b8;
        font-size: 11px;
    }

    .empty-state strong {
        display: block;
        margin-bottom: 5px;
        color: #64748b;
        font-size: 12px;
    }

    @media (max-width: 800px) {
        .page-header,
        .school-card-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }


    /* NETMONITOR Modern Blue - Data Sekolah */
    .school-page { color: #19345f; }
    .page-header { align-items: center; margin-bottom: 24px; }
    .school-eyebrow { display:flex; align-items:center; gap:8px; margin-bottom:9px; color:#6482b4; font-size:10px; font-weight:800; letter-spacing:1.8px; }
    .eyebrow-dot { width:7px; height:7px; border-radius:50%; background:#1672f9; box-shadow:0 0 0 4px rgba(22,114,249,.12); }
    .page-header h1 { font-size:clamp(26px, 2.2vw, 34px); letter-spacing:-.8px; font-weight:800; color:#102d5d; }
    .page-header p { font-size:13px; line-height:1.6; color:#6e83a6; }
    .btn-primary { min-height:44px; padding:0 19px; border-radius:12px; background:linear-gradient(135deg,#2580ff,#125bd1); border:0; box-shadow:0 8px 18px rgba(18,102,216,.22); font-size:12px; transition:transform .18s ease,box-shadow .18s ease; }
    .btn-primary:hover { background:linear-gradient(135deg,#176ce8,#0f4fb8); transform:translateY(-1px); box-shadow:0 11px 22px rgba(18,102,216,.28); }
    .alert-success { padding:14px 16px; border-radius:12px; font-size:12px; box-shadow:0 5px 16px rgba(22,163,74,.05); }
    .school-overview { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; margin:0 0 22px; }
    .overview-card { position:relative; display:flex; align-items:center; gap:15px; min-height:132px; padding:22px; overflow:hidden; background:rgba(255,255,255,.88); border:1px solid #dbe7f8; border-radius:18px; box-shadow:0 10px 28px rgba(41,83,145,.055); isolation:isolate; }
    .overview-card:before { content:""; position:absolute; z-index:-1; width:125px; height:125px; right:-42px; top:-54px; border-radius:50%; background:var(--accent-soft); }
    .overview-icon { display:flex; align-items:center; justify-content:center; flex:0 0 48px; width:48px; height:48px; border-radius:15px; color:var(--accent); background:var(--accent-soft); font-size:22px; font-weight:800; }
    .overview-copy { display:flex; flex-direction:column; align-items:flex-start; gap:4px; min-width:0; }
    .overview-label { color:#7890b7; font-size:9px; letter-spacing:1.15px; font-weight:800; }
    .overview-copy strong { color:#15396f; font-size:30px; line-height:1.15; font-weight:800; letter-spacing:-.8px; }
    .overview-copy small { color:#8192ad; font-size:11px; }
    .overview-total { --accent:#1672f9; --accent-soft:#e6f0ff; }
    .overview-active { --accent:#10a86b; --accent-soft:#ddf8eb; }
    .overview-inactive { --accent:#e89a20; --accent-soft:#fff0d7; }
    .overview-decoration { position:absolute; left:0; top:20px; bottom:20px; width:3px; border-radius:0 4px 4px 0; background:var(--accent); }
    .school-card { border:1px solid #d9e6f7; border-radius:18px; background:rgba(255,255,255,.94); box-shadow:0 12px 32px rgba(35,76,137,.065); }
    .school-card-header { padding:22px 24px; background:linear-gradient(110deg,rgba(244,248,255,.96),rgba(255,255,255,.96)); border-bottom:1px solid #e7eef9; }
    .school-card-title { color:#17396f; font-size:15px; letter-spacing:-.2px; }
    .school-card-subtitle { color:#8295b4; font-size:11px; }
    .school-count { display:inline-flex; align-items:center; gap:8px; padding:8px 11px; border:1px solid #dce9fb; border-radius:999px; background:#f4f8ff; color:#5274a7; font-size:11px; font-weight:700; }
    .count-pulse { width:7px; height:7px; border-radius:50%; background:#2878f0; box-shadow:0 0 0 3px rgba(40,120,240,.12); }
    .school-table { min-width:780px; border-collapse:separate; border-spacing:0; }
    .school-table th { padding:15px 16px; background:#f3f7ff; color:#6d86b0; font-size:9px; letter-spacing:.8px; text-transform:uppercase; border-bottom:1px solid #e3ecfa; }
    .school-table td { padding:17px 16px; font-size:12px; border-bottom:1px solid #edf2fa; }
    .school-table tbody tr { transition:background .16s ease; }
    .school-table tbody tr:hover { background:#f7faff; }
    .school-name { color:#183b70; font-size:12px; font-weight:750; }
    .school-ip { display:inline-block; padding:5px 8px; border:1px solid #e6edf7; border-radius:7px; background:#f7f9fd; color:#536987; font-size:11px; }
    .bandwidth { color:#244a7e; font-variant-numeric:tabular-nums; }
    .status-active,.status-inactive { padding:6px 10px; border-radius:999px; font-size:9px; letter-spacing:.25px; }
    .status-active { background:#dcf9eb; color:#078451; }
    .status-inactive { background:#eef2f7; color:#66758c; }
    .action-link { min-height:32px; padding:0 11px; border-radius:8px; transition:background .16s ease,transform .16s ease; }
    .action-link:hover { transform:translateY(-1px); }
    .empty-state { padding:55px 15px; font-size:12px; }
    .empty-state strong { font-size:14px; }
    @media (max-width: 1000px) { .school-overview { grid-template-columns:repeat(2,minmax(0,1fr)); } .overview-inactive { grid-column:1/-1; } }
    @media (max-width: 800px) { .school-overview { grid-template-columns:1fr; gap:12px; } .overview-inactive { grid-column:auto; } .overview-card { min-height:110px; padding:18px; } .page-header { align-items:flex-start; } }
    @media (prefers-reduced-motion: reduce) { .btn-primary,.school-table tbody tr,.action-link { transition:none; } }

</style>
@endsection

@section('content')

<div class="school-page">

    <div class="page-header">

        <div class="school-heading">
            <div class="school-eyebrow"><span class="eyebrow-dot"></span> MANAJEMEN INFRASTRUKTUR</div>
            <h1>Data Sekolah</h1>

            <p>
                Data sekolah yang menjadi objek pemantauan kualitas jaringan QoS.
            </p>
        </div>

        <a
            href="{{ route('schools.create') }}"
            class="btn-primary"
        >
            + Tambah Sekolah
        </a>

    </div>

    @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="school-overview">
        <div class="overview-card overview-total">
            <div class="overview-icon">🏫</div>
            <div class="overview-copy">
                <span class="overview-label">TOTAL SEKOLAH</span>
                <strong>{{ $schools->count() }}</strong>
                <small>Terdaftar dalam sistem</small>
            </div>
            <span class="overview-decoration"></span>
        </div>
        <div class="overview-card overview-active">
            <div class="overview-icon">✓</div>
            <div class="overview-copy">
                <span class="overview-label">SEKOLAH AKTIF</span>
                <strong>{{ $schools->where('status', true)->count() }}</strong>
                <small>Siap dipantau</small>
            </div>
            <span class="overview-decoration"></span>
        </div>
        <div class="overview-card overview-inactive">
            <div class="overview-icon">Ⅱ</div>
            <div class="overview-copy">
                <span class="overview-label">TIDAK AKTIF</span>
                <strong>{{ $schools->where('status', false)->count() }}</strong>
                <small>Belum ikut pemantauan</small>
            </div>
            <span class="overview-decoration"></span>
        </div>
    </div>

    <div class="school-card">

        <div class="school-card-header">

            <div>
                <div class="school-card-title">
                    Sekolah yang Dipantau
                </div>

                <div class="school-card-subtitle">
                    Konfigurasi objek monitoring jaringan
                </div>
            </div>

            <div class="school-count">
                <span class="count-pulse"></span>{{ $schools->count() }} sekolah terdaftar
            </div>

        </div>

        <div class="table-wrapper">

            <table class="school-table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Sekolah</th>
                        <th>IP Target</th>
                        <th>Bandwidth</th>
                        <th>Paket ISP</th>
                        <th>Agent</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($schools as $school)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                <span class="school-name">
                                    {{ $school->nama_sekolah }}
                                </span>
                            </td>

                            <td>
                                <span class="school-ip">
                                    {{ $school->ip_target ?: 'Menunggu agent' }}
                                </span>
                            </td>

                            <td>
                                <span class="bandwidth">
                                    {{ $school->bandwidth !== null && (float) $school->bandwidth > 0
                                        ? number_format((float) $school->bandwidth, 2) . ' Mbps'
                                        : '-' }}
                                </span>
                            </td>

                            <td>
                                {{ $school->paket_isp ?: ($school->agent_isp ?: '-') }}
                            </td>

                            <td>
                                @if ($school->agent_last_seen_at)
                                    @if ($school->agentIsOnline())
                                        <span
                                            class="status-agent-online"
                                            title="Terakhir terhubung {{ $school->agent_last_seen_at->format('d M Y H:i') }}"
                                        >
                                            Online
                                        </span>
                                    @else
                                        <span
                                            class="status-agent-offline"
                                            title="Terakhir terhubung {{ $school->agent_last_seen_at->format('d M Y H:i') }}"
                                        >
                                            Offline
                                        </span>
                                    @endif
                                @else
                                    <span class="status-agent-none">Belum terpasang</span>
                                @endif
                            </td>

                            <td>

                                @if ($school->status)
                                    <span class="status-active">
                                        Aktif
                                    </span>
                                @else
                                    <span class="status-inactive">
                                        Tidak Aktif
                                    </span>
                                @endif

                            </td>

                            <td>

                                <div class="action-group">

                                    <a
                                        href="{{ route('schools.edit', $school) }}"
                                        class="action-link action-edit"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('schools.destroy', $school) }}"
                                        method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data sekolah ini?')"
                                        style="margin: 0;"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="action-link action-delete"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8">

                                <div class="empty-state">

                                    <strong>
                                        Belum ada data sekolah
                                    </strong>

                                    Tambahkan sekolah terlebih dahulu
                                    untuk memulai monitoring QoS.

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
