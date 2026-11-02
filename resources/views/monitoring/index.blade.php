@extends('layouts.app')



@section('title', 'Monitoring QoS')



@section('styles')

<style>

    .monitoring-page {

        width: 100%;

    }



    .monitoring-header {

        display: flex;

        justify-content: space-between;

        align-items: flex-start;

        gap: 20px;

        margin-bottom: 20px;

    }



    .page-title h1 {

        font-size: 24px;

        line-height: 1.25;

        color: #10295c;

        margin: 0 0 6px;

    }



    .page-title p {

        font-size: 12px;

        color: #718096;

        margin: 0;

    }



    .school-select {

        background: #ffffff;

        border: 1px solid #dfe6ef;

        border-radius: 8px;

        padding: 10px 13px;

        font-size: 12px;

        color: #334155;

        white-space: nowrap;

    }



    .school-select form {

        display: inline;

    }



    .school-select select {

        border: 0;

        outline: 0;

        background: transparent;

        font-size: 12px;

        color: #334155;

        cursor: pointer;

    }



    .summary-grid {

        display: grid;

        grid-template-columns: repeat(3, minmax(0, 1fr));

        gap: 14px;

        margin-bottom: 20px;

    }



    .summary-card {

        background: #ffffff;

        border: 1px solid #e3e8ef;

        border-radius: 10px;

        padding: 18px;

        min-height: 112px;

    }



    .summary-label {

        font-size: 10px;

        font-weight: 700;

        color: #718096;

        text-transform: uppercase;

        margin-bottom: 10px;

    }



    .summary-value {

        font-size: 24px;

        line-height: 1.2;

        font-weight: 700;

        color: #243b64;

    }



    .summary-unit {

        margin-top: 4px;

        font-size: 11px;

        color: #718096;

    }



    .status-active,

    .status-check,

    .status-wait {

        display: inline-block;

        padding: 6px 10px;

        border-radius: 6px;

        font-size: 10px;

        font-weight: 700;

    }



    .status-success {

    display: inline-block;

    padding: 6px 10px;

    border-radius: 6px;

    font-size: 10px;

    font-weight: 700;

    background: #dcfce7;

    color: #15803d;

}



.status-failed {

    display: inline-block;

    padding: 6px 10px;

    border-radius: 6px;

    font-size: 10px;

    font-weight: 700;

    background: #fee2e2;

    color: #b91c1c;

}



    .status-active {

        background: #dcfce7;

        color: #15803d;

    }



    .status-check {

        background: #fef3c7;

        color: #b45309;

    }



    .status-wait {

        background: #f1f5f9;

        color: #64748b;

    }



    .panel {

        background: #ffffff;

        border: 1px solid #e3e8ef;

        border-radius: 10px;

        margin-bottom: 20px;

        overflow: hidden;

    }



    .panel-header {

        padding: 17px 20px 12px;

    }



    .panel-title {

        font-size: 14px;

        font-weight: 700;

        color: #243b64;

    }



    .panel-subtitle {

        margin-top: 4px;

        font-size: 10px;

        color: #8a94a6;

    }



    .info-box {

        margin: 0 20px 18px;

        background: #f8fafc;

        border: 1px solid #edf0f4;

        border-radius: 8px;

        padding: 13px 14px;

        font-size: 11px;

        line-height: 1.55;

        color: #64748b;

    }



    .table-wrapper {

        width: 100%;

        overflow-x: auto;

    }



    .monitoring-table {

        width: 100%;

        min-width: 850px;

        border-collapse: collapse;

    }



    .monitoring-table th {

        background: #f8fafc;

        text-align: left;

        font-size: 10px;

        font-weight: 700;

        color: #718096;

        padding: 12px 14px;

        border-top: 1px solid #edf0f4;

        border-bottom: 1px solid #e5eaf0;

        white-space: nowrap;

    }



    .monitoring-table td {

        font-size: 11px;

        padding: 14px;

        border-bottom: 1px solid #edf0f4;

        color: #334155;

        white-space: nowrap;

    }



    .monitoring-table tbody tr:last-child td {

        border-bottom: 0;

    }



    .school-name {

        font-weight: 700;

        color: #173a70;

    }



    .empty-state {

        text-align: center;

        padding: 35px 10px;

        color: #94a3b8;

        font-size: 11px;

    }



    /\* PAGINATION - DESAIN SERAGAM \*/

    .monitoring-pagination {

        display: flex;

        align-items: center;

        justify-content: space-between;

        flex-wrap: wrap;

        gap: 12px;

        width: 100%;

        padding: 18px 20px 20px;

        border-top: 1px solid #edf0f4;

        box-sizing: border-box;

    }



    .monitoring-pagination-summary {

        color: #7183a2;

        font-size: 12px;

    }



    .monitoring-pagination-buttons {

        display: flex;

        align-items: center;

        justify-content: flex-end;

        gap: 6px;

        margin-left: auto;

    }



    .monitoring-pagination-buttons a,

    .monitoring-pagination-buttons span {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        flex: 0 0 35px;

        width: 35px;

        min-width: 35px;

        height: 35px;

        padding: 0;

        margin: 0;

        border: 1px solid #dce5f1;

        border-radius: 8px;

        background: #ffffff;

        color: #17345f;

        font-size: 12px;

        line-height: 1;

        text-decoration: none;

        box-sizing: border-box;

    }



    .monitoring-pagination-buttons a:hover {

        background: #eaf2ff;

        border-color: #1765dc;

        color: #1765dc;

    }



    .monitoring-pagination-buttons .pagination-active {

        background: #1765dc;

        color: #ffffff;

        border-color: #1765dc;

        font-weight: 600;

    }



    .monitoring-pagination-buttons .pagination-disabled {

        color: #a0aec0;

        background: #f7f9fc;

        cursor: not-allowed;

    }



    .monitoring-footer {

        text-align: center;

        padding: 0 0 8px;

        font-size: 10px;

        color: #94a3b8;

    }



    @media (max-width: 900px) {

        .summary-grid {

            grid-template-columns: 1fr;

        }



        .monitoring-header {

            flex-direction: column;

            align-items: stretch;

        }



        .school-select {

            width: fit-content;

        }

    }



    @media (max-width: 650px) {

        .monitoring-pagination {

            flex-direction: column;

            align-items: flex-end;

        }



        .monitoring-pagination-summary {

            width: 100%;

            text-align: right;

        }



        .monitoring-pagination-buttons {

            gap: 4px;

        }



        .monitoring-pagination-buttons a,

        .monitoring-pagination-buttons span {

            flex-basis: 32px;

            width: 32px;

            min-width: 32px;

            height: 32px;

        }

    }


    /* =========================================================
       NETMONITOR MODERN BLUE — Monitoring QoS
       Lapisan visual tambahan; tidak mengubah data atau route.
       ========================================================= */
    .monitoring-page {
        --nm-blue: #1765dc;
        --nm-blue-dark: #10295c;
        --nm-muted: #7183a2;
        --nm-border: rgba(170, 194, 229, .48);
        color: #203b68;
        padding-bottom: 18px;
    }

    .monitoring-page .monitoring-header {
        align-items: center;
        padding: 8px 2px 22px;
        margin-bottom: 4px;
    }

    .monitoring-page .page-title h1 {
        font-size: clamp(25px, 2.2vw, 34px);
        font-weight: 800;
        letter-spacing: -.8px;
        color: #10295c;
    }

    .monitoring-page .page-title p {
        font-size: 13px;
        line-height: 1.7;
        color: #627da7;
    }

    .monitoring-page .school-select {
        padding: 10px 14px;
        background: rgba(255,255,255,.82);
        border: 1px solid var(--nm-border);
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(38, 89, 160, .06);
        backdrop-filter: blur(12px);
    }

    .monitoring-page .school-select select {
        max-width: 250px;
        padding: 2px 4px;
        font-weight: 600;
    }

    .monitoring-page .summary-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .monitoring-page .summary-card {
        position: relative;
        min-height: 132px;
        padding: 21px 22px;
        overflow: hidden;
        background: linear-gradient(145deg, rgba(255,255,255,.98), rgba(246,250,255,.9));
        border: 1px solid var(--nm-border);
        border-radius: 18px;
        box-shadow: 0 12px 30px rgba(37, 85, 151, .07);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .monitoring-page .summary-card::after {
        content: "";
        position: absolute;
        width: 94px;
        height: 94px;
        right: -38px;
        top: -42px;
        border-radius: 50%;
        background: rgba(47, 128, 237, .07);
        pointer-events: none;
    }

    .monitoring-page .summary-card:hover {
        transform: translateY(-3px);
        border-color: rgba(23,101,220,.3);
        box-shadow: 0 18px 34px rgba(37, 85, 151, .12);
    }

    .monitoring-page .summary-label {
        position: relative;
        z-index: 1;
        font-size: 10px;
        letter-spacing: 1.15px;
        color: #6c83a9;
        margin-bottom: 14px;
    }

    .monitoring-page .summary-value {
        position: relative;
        z-index: 1;
        font-size: clamp(24px, 2vw, 31px);
        letter-spacing: -.6px;
        color: #15366f;
    }

    .monitoring-page .summary-unit {
        position: relative;
        z-index: 1;
        margin-top: 7px;
        font-size: 12px;
    }

    .monitoring-page .status-active,
    .monitoring-page .status-check,
    .monitoring-page .status-wait,
    .monitoring-page .status-success,
    .monitoring-page .status-failed {
        border-radius: 999px;
        padding: 7px 12px;
        letter-spacing: .35px;
    }

    .monitoring-page .panel {
        margin-bottom: 22px;
        background: rgba(255,255,255,.88);
        border: 1px solid var(--nm-border);
        border-radius: 20px;
        box-shadow: 0 14px 34px rgba(37, 85, 151, .065);
        backdrop-filter: blur(14px);
    }

    .monitoring-page .panel-header {
        padding: 22px 24px 16px;
    }

    .monitoring-page .panel-title {
        font-size: 16px;
        font-weight: 800;
        letter-spacing: -.2px;
        color: #173968;
    }

    .monitoring-page .panel-subtitle {
        font-size: 11px;
        line-height: 1.6;
        color: #7c91b2;
    }

    .monitoring-page .info-box {
        margin: 0 24px 22px;
        padding: 16px 18px;
        border: 1px solid #dceaff;
        border-radius: 14px;
        background: linear-gradient(115deg, #eff6ff, #f8fbff);
        color: #58739b;
        font-size: 12px;
        line-height: 1.8;
    }

    .monitoring-page .table-wrapper {
        padding: 0 18px 4px;
    }

    .monitoring-page .monitoring-table {
        min-width: 850px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .monitoring-page .monitoring-table th {
        padding: 14px 15px;
        background: #f1f6ff;
        border-top: 1px solid #e2ebf9;
        border-bottom: 1px solid #e2ebf9;
        color: #6e84a8;
        font-size: 10px;
        letter-spacing: .45px;
        text-transform: uppercase;
    }

    .monitoring-page .monitoring-table th:first-child {
        border-radius: 10px 0 0 10px;
    }

    .monitoring-page .monitoring-table th:last-child {
        border-radius: 0 10px 10px 0;
    }

    .monitoring-page .monitoring-table td {
        padding: 16px 15px;
        border-bottom: 1px solid #edf2fa;
        color: #3b5277;
        font-size: 12px;
    }

    .monitoring-page .monitoring-table tbody tr {
        transition: background .18s ease;
    }

    .monitoring-page .monitoring-table tbody tr:hover {
        background: #f6f9ff;
    }

    .monitoring-page .school-name {
        color: #183c73;
        font-weight: 700;
    }

    .monitoring-page .empty-state {
        padding: 48px 14px;
        color: #8195b4;
        font-size: 13px;
    }

    .monitoring-page .monitoring-pagination {
        padding: 18px 24px 22px;
        border-top-color: #eaf0f9;
    }

    .monitoring-page .monitoring-pagination-buttons a,
    .monitoring-page .monitoring-pagination-buttons span {
        border-radius: 10px;
        transition: all .18s ease;
    }

    .monitoring-page .monitoring-pagination-buttons .pagination-active {
        background: linear-gradient(135deg, #2878ed, #1458c8);
        box-shadow: 0 5px 12px rgba(23,101,220,.2);
    }

    .monitoring-page .monitoring-footer {
        padding: 8px 0 12px;
        color: #8094b3;
        font-size: 11px;
    }

    @media (max-width: 900px) {
        .monitoring-page .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .monitoring-page .monitoring-header {
            align-items: stretch;
            padding-bottom: 18px;
        }

        .monitoring-page .summary-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .monitoring-page .summary-card {
            min-height: 112px;
        }

        .monitoring-page .panel-header {
            padding: 18px 18px 14px;
        }

        .monitoring-page .info-box {
            margin: 0 16px 18px;
        }

        .monitoring-page .table-wrapper {
            padding: 0 10px 4px;
        }

        .monitoring-page .monitoring-pagination {
            padding: 16px;
            align-items: flex-start;
        }

        .monitoring-page .monitoring-pagination-summary {
            text-align: left;
        }
    }


/* ===== PERBAIKAN KONTRAS HALAMAN MONITORING QoS ===== */
.monitoring-page h1,
.monitoring-page .page-title h1,
.monitoring-page .header h1 {
    color: #ffffff !important;
    font-weight: 800 !important;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.35) !important;
    opacity: 1 !important;
}

.monitoring-page p,
.monitoring-page .page-title p,
.monitoring-page .header p {
    color: #e2edff !important;
    opacity: 1 !important;
}

/* Jika judul tidak memakai class monitoring-page */
.monitoring-header h1,
.monitoring-header p {
    color: #ffffff !important;
    opacity: 1 !important;
}

</style>

@endsection



@section('content')

<div class="monitoring-page">



    <div class="monitoring-header">

        <div class="page-title">

            <h1>Monitoring QoS</h1>

            <p>

                Monitoring kualitas jaringan internet secara otomatis

                menggunakan Laravel Scheduler.

            </p>

        </div>



        <div class="school-select">

            🏫

            <form method="GET" action="{{ route('monitoring.index') }}">

                <select name="school_id" onchange="this.form.submit()">

                    <option value="">Semua Sekolah</option>



                    @foreach ($schools as $school)

                        <option

                            value="{{ $school->id }}"

                            @selected($selectedSchoolId && $selectedSchoolId == $school->id)

                        >

                            {{ $school->nama_sekolah }}

                        </option>

                    @endforeach

                </select>

            </form>

        </div>

    </div>



    <div class="summary-grid">

        <div class="summary-card">

            <div class="summary-label">Status Monitoring</div>

            <div class="summary-value">

                @if ($monitoringStatus === 'AKTIF')

                    <span class="status-active">AKTIF</span>

                @elseif ($monitoringStatus === 'PERLU DICEK')

                    <span class="status-check">PERLU DICEK</span>

                @else

                    <span class="status-wait">MENUNGGU DATA</span>

                @endif

            </div>

        </div>



        <div class="summary-card">

            <div class="summary-label">Sekolah Aktif</div>

            <div class="summary-value">{{ $activeSchools }}</div>

            <div class="summary-unit">sekolah</div>

        </div>



        <div class="summary-card">

            <div class="summary-label">Pengukuran Terakhir</div>

            <div class="summary-value">

                @if ($latestSelectedMeasurement)

                    {{ $latestSelectedMeasurement->measured_at->format('H:i:s') }}

                @elseif ($latestMeasurement)

                    {{ $latestMeasurement->measured_at->format('H:i:s') }}

                @else

                    --

                @endif

            </div>



            <div class="summary-unit">

                @if ($latestSelectedMeasurement)

                    {{ $latestSelectedMeasurement->measured_at->format('d M Y') }}

                @elseif ($latestMeasurement)

                    {{ $latestMeasurement->measured_at->format('d M Y') }}

                @else

                    Belum ada pengukuran

                @endif

            </div>

        </div>

    </div>



    <div class="panel">

        <div class="panel-header">

            <div class="panel-title">Status Sistem Monitoring</div>

            <div class="panel-subtitle">Informasi proses monitoring otomatis</div>

        </div>



        <div class="info-box">

            Sistem monitoring QoS dijalankan secara otomatis menggunakan

            Laravel Scheduler dengan interval pengukuran setiap 5 menit.

            Sistem mengambil data sekolah aktif, melakukan pengukuran

            parameter QoS, menyimpan hasil pengukuran ke database, dan

            menggunakan data tersebut untuk proses klasifikasi serta

            analisis QoS.

        </div>

    </div>



    <div class="panel">

        <div class="panel-header">

            <div class="panel-title">Pengukuran QoS Terbaru</div>

            <div class="panel-subtitle">

                Data hasil pengukuran yang tersimpan pada sistem

            </div>

        </div>



        <div class="table-wrapper">

            <table class="monitoring-table">

                <thead>

                    <tr>

                        <th>Sekolah</th>

                        <th>Waktu</th>

                        <th>Throughput</th>

                        <th>Delay</th>

                        <th>Jitter</th>

                        <th>Packet Loss</th>

                        <th>Ping</th>

                        <th>Status</th>

                    </tr>

                </thead>



                <tbody>

                    @forelse ($measurements as $measurement)

                        <tr>

                            <td>

                                @if ($measurement->school)

                                    <span class="school-name">

                                        {{ $measurement->school->nama_sekolah }}

                                    </span>

                                @else

                                    -

                                @endif

                            </td>



                            <td>

                                {{ $measurement->measured_at->format('d M Y H:i:s') }}

                            </td>



                            <td>

                                @if ($measurement->throughput !== null)

                                    {{ number_format($measurement->throughput, 2) }} Mbps

                                @else

                                    --

                                @endif

                            </td>



                            <td>

                                @if ($measurement->delay !== null)

                                    {{ number_format($measurement->delay, 2) }} ms

                                @else

                                    --

                                @endif

                            </td>



                            <td>

                                @if ($measurement->jitter !== null)

                                    {{ number_format($measurement->jitter, 2) }} ms

                                @else

                                    --

                                @endif

                            </td>



                            <td>

                                @if ($measurement->packet_loss !== null)

                                    {{ number_format($measurement->packet_loss, 2) }} %

                                @else

                                    --

                                @endif

                            </td>



                            <td>

                                @if ($measurement->ping !== null)

                                    {{ number_format($measurement->ping, 2) }} ms

                                @else

                                    --

                                @endif

                            </td>



                            <td>

                                @if ((int) $measurement->success === 1)

                                 <span class="status-success">BERHASIL</span>

                                @else

                                  <span class="status-failed">GAGAL</span>

                                @endif

                                </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8">

                                <div class="empty-state">

                                    Belum ada data pengukuran QoS.

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



        {{-- PAGINATION --}}

        @if ($measurements->hasPages())

            @php

                $currentPage = $measurements->currentPage();

                $lastPage = $measurements->lastPage();



                $startPage = max(1, $currentPage - 1);

                $endPage = min($lastPage, $currentPage + 1);



                if ($currentPage === 1) {

                    $endPage = min(3, $lastPage);

                } elseif ($currentPage === $lastPage) {

                    $startPage = max(1, $lastPage - 2);

                }

            @endphp



            <div class="monitoring-pagination">

                <div class="monitoring-pagination-summary">

                    Menampilkan {{ $measurements->firstItem() }}

                    sampai {{ $measurements->lastItem() }}

                    dari {{ $measurements->total() }} data

                </div>



                <div class="monitoring-pagination-buttons">

                    @if ($measurements->onFirstPage())

                        <span class="pagination-disabled" aria-label="Sebelumnya">‹</span>

                    @else

                        <a href="{{ $measurements->previousPageUrl() }}"

                           aria-label="Sebelumnya">‹</a>

                    @endif



                    @foreach ($measurements->getUrlRange($startPage, $endPage) as $page => $url)

                        @if ($page == $currentPage)

                            <span class="pagination-active" aria-current="page">

                                {{ $page }}

                            </span>

                        @else

                            <a href="{{ $url }}">{{ $page }}</a>

                        @endif

                    @endforeach



                    @if ($measurements->hasMorePages())

                        <a href="{{ $measurements->nextPageUrl() }}"

                           aria-label="Berikutnya">›</a>

                    @else

                        <span class="pagination-disabled" aria-label="Berikutnya">›</span>

                    @endif

                </div>

            </div>

        @endif

    </div>



    <div class="monitoring-footer">

        Sistem Monitoring QoS Jaringan — Data diperbarui otomatis oleh Laravel Scheduler.

    </div>

</div>

@endsection
