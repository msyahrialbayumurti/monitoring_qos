@extends('layouts.app')

@section('title', 'Rekomendasi')

@section('styles')
<style>
    .recommendation-page {
        --rec-ink: #102b59;
        --rec-muted: #6f82a3;
        --rec-line: #dce7f8;
        --rec-blue: #1768e5;
        width: 100%;
        min-width: 0;
        color: var(--rec-ink);
    }

    .recommendation-page *,
    .recommendation-page *::before,
    .recommendation-page *::after {
        box-sizing: border-box;
    }

    .recommendation-page .page-header {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        overflow: hidden;
        margin-bottom: 22px;
        padding: 25px 28px;
        border: 1px solid rgba(190, 213, 249, .9);
        border-radius: 20px;
        background:
            radial-gradient(circle at 90% 0%, rgba(91,157,255,.20), transparent 30%),
            linear-gradient(120deg, rgba(255,255,255,.97), rgba(239,246,255,.94));
        box-shadow: 0 12px 30px rgba(34,83,153,.07);
    }

    .recommendation-page .page-header::after {
        content: "";
        position: absolute;
        right: -42px;
        bottom: -94px;
        width: 190px;
        height: 190px;
        border: 1px solid rgba(23,104,229,.12);
        border-radius: 50%;
        box-shadow: 0 0 0 20px rgba(23,104,229,.035), 0 0 0 42px rgba(23,104,229,.025);
        pointer-events: none;
    }

    .recommendation-page .page-header > div:first-child {
        position: relative;
        z-index: 1;
    }

    .recommendation-page h1 {
        margin: 0 0 8px;
        color: var(--rec-ink);
        font-size: clamp(25px, 2.5vw, 32px);
        font-weight: 800;
        letter-spacing: -.035em;
        line-height: 1.2;
    }

    .recommendation-page .subtitle {
        max-width: 700px;
        margin: 0;
        color: var(--rec-muted);
        font-size: 13px;
        line-height: 1.7;
    }

    .recommendation-page .date-label {
        position: relative;
        z-index: 1;
        flex-shrink: 0;
        padding: 12px 16px;
        border: 1px solid #d4e2f8;
        border-radius: 12px;
        background: rgba(255,255,255,.9);
        color: #315783;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: 0 5px 14px rgba(36,83,150,.05);
    }

    .recommendation-page .panel {
        overflow: hidden;
        margin-bottom: 20px;
        border: 1px solid var(--rec-line);
        border-radius: 18px;
        background: rgba(255,255,255,.96);
        box-shadow: 0 12px 30px rgba(34,83,153,.06);
    }

    .recommendation-page .panel h2 {
        margin: 0;
        padding: 21px 24px;
        border-bottom: 1px solid #e8effa;
        background: linear-gradient(120deg, #fff, #f7faff);
        color: var(--rec-ink);
        font-size: 16px;
        font-weight: 800;
    }

    .recommendation-page .filter-form {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 12px;
        padding: 20px 24px 23px;
    }

    .recommendation-page .form-group {
        display: flex;
        flex: 1 1 280px;
        flex-direction: column;
        gap: 8px;
        min-width: 0;
        max-width: 480px;
    }

    .recommendation-page label {
        color: #496486;
        font-size: 12px;
        font-weight: 700;
    }

    .recommendation-page select,
    .recommendation-page .btn {
        min-height: 44px;
        padding: 10px 16px;
        border-radius: 11px;
        font-size: 12px;
    }

    .recommendation-page select {
        width: 100%;
        border: 1px solid #d6e1ef;
        outline: none;
        background: #fbfdff;
        color: #17345f;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .recommendation-page select:focus {
        border-color: #4389f4;
        box-shadow: 0 0 0 4px rgba(40,119,235,.11);
    }

    .recommendation-page .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        font-weight: 800;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .recommendation-page .btn:hover {
        transform: translateY(-1px);
    }

    .recommendation-page .btn-primary {
        border-color: #1768e5;
        background: linear-gradient(135deg, #3185ff, #125bd0);
        color: #fff;
        box-shadow: 0 7px 15px rgba(23,104,229,.16);
    }

    .recommendation-page .btn-primary:hover {
        box-shadow: 0 10px 20px rgba(23,104,229,.22);
    }

    .recommendation-page .btn-reset {
        border-color: #d8e3f2;
        background: #fff;
        color: #496486;
    }

    .recommendation-page .btn-reset:hover {
        background: #f4f8ff;
    }

    .recommendation-page .stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .recommendation-page .stat-card {
        position: relative;
        overflow: hidden;
        padding: 22px;
        border: 1px solid var(--rec-line);
        border-radius: 17px;
        background: linear-gradient(145deg, #fff, #f8fbff);
        box-shadow: 0 10px 25px rgba(34,83,153,.055);
    }

    .recommendation-page .stat-card::after {
        content: "";
        position: absolute;
        top: -30px;
        right: -30px;
        width: 95px;
        height: 95px;
        border-radius: 50%;
        background: rgba(55,130,245,.09);
        pointer-events: none;
    }

    .recommendation-page .stat-card:nth-child(2)::after {
        background: rgba(239,83,91,.09);
    }

    .recommendation-page .stat-card:nth-child(3)::after {
        background: rgba(245,165,36,.13);
    }

    .recommendation-page .stat-label {
        position: relative;
        z-index: 1;
        margin-bottom: 10px;
        color: #6b82a3;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .09em;
        line-height: 1.5;
        text-transform: uppercase;
    }

    .recommendation-page .stat-value {
        position: relative;
        z-index: 1;
        color: #173f78;
        font-size: 30px;
        font-weight: 800;
        letter-spacing: -.03em;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
    }

    .recommendation-page .stat-note {
        position: relative;
        z-index: 1;
        margin-top: 8px;
        color: #7187a4;
        font-size: 11px;
        line-height: 1.6;
    }

    .recommendation-page .recommendation-list {
        display: grid;
        gap: 13px;
        padding: 22px 24px;
    }

    .recommendation-page .recommendation-card {
        position: relative;
        padding: 20px;
        border: 1px solid #dce7f8;
        border-radius: 15px;
        background: linear-gradient(135deg, #fff, #f8fbff);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .recommendation-page .recommendation-card:hover {
        transform: translateY(-2px);
        border-color: #bfd5f7;
        box-shadow: 0 10px 24px rgba(34,83,153,.07);
    }

    .recommendation-page .card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .recommendation-page .school-name {
        color: #173f78;
        font-size: 14px;
        font-weight: 800;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .recommendation-page .meta {
        margin-top: 5px;
        color: #7187a4;
        font-size: 11px;
        line-height: 1.6;
    }

    .recommendation-page .priority {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        padding: 7px 10px;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .035em;
    }

    .recommendation-page .priority-kritis {
        border-color: #ffd4d8;
        background: #ffe8ea;
        color: #b42323;
    }

    .recommendation-page .priority-tinggi {
        border-color: #ffdfb5;
        background: #fff0dc;
        color: #a85e00;
    }

    .recommendation-page .priority-sedang {
        border-color: #f5e3a8;
        background: #fff5d9;
        color: #956500;
    }

    .recommendation-page .priority-rendah {
        border-color: #c9efd7;
        background: #e3f6e9;
        color: #197341;
    }

    .recommendation-page .action-label {
        margin: 15px 0 6px;
        color: #617b9c;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .recommendation-page .action-text {
        margin: 0;
        color: #425d7f;
        font-size: 12px;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    .recommendation-page .qos-info {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 15px;
        padding-top: 13px;
        border-top: 1px solid #e6eef9;
    }

    .recommendation-page .qos-chip {
        display: inline-flex;
        align-items: center;
        padding: 7px 10px;
        border: 1px solid #dce8f8;
        border-radius: 9px;
        background: #f0f6ff;
        color: #4b6688;
        font-size: 10px;
        font-weight: 700;
    }

    .recommendation-page .empty-state {
        margin: 22px 24px;
        padding: 40px 22px;
        border: 1px dashed #cbdcf2;
        border-radius: 15px;
        background: #f8fbff;
        color: #7187a4;
        line-height: 1.8;
        text-align: center;
    }

    .recommendation-page .empty-state strong {
        display: block;
        margin-bottom: 5px;
        color: #294568;
        font-size: 14px;
    }

    .recommendation-page .notification-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        width: 100%;
        margin-top: 0;
        padding: 17px 24px;
        border-top: 1px solid #e3eaf4;
        background: #fbfdff;
    }

    .recommendation-page .pagination-summary {
        color: #7183a2;
        font-size: 11px;
        line-height: 1.6;
        text-align: left;
    }

    .recommendation-page .pagination {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 6px;
        margin-left: auto;
    }

    .recommendation-page .pagination a,
    .recommendation-page .pagination span {
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
        border-radius: 9px;
        background: #fff;
        color: #17345f;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        line-height: 1;
        text-decoration: none;
        box-sizing: border-box;
    }

    .recommendation-page .pagination .pagination-active {
        border-color: #1765dc;
        background: #1765dc;
        color: #fff;
        font-weight: 800;
        box-shadow: 0 5px 12px rgba(23,101,220,.18);
    }

    .recommendation-page .pagination a:hover {
        border-color: #1765dc;
        background: #eaf2ff;
        color: #1765dc;
    }

    .recommendation-page .pagination .pagination-disabled {
        background: #f7f9fc;
        color: #a0aec0;
        cursor: not-allowed;
    }

    @media (max-width: 900px) {
        .recommendation-page .stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .recommendation-page .page-header {
            align-items: flex-start;
            flex-direction: column;
            padding: 21px;
        }

        .recommendation-page .filter-form {
            align-items: stretch;
            flex-direction: column;
            padding: 18px;
        }

        .recommendation-page .form-group {
            min-width: 0;
            width: 100%;
            flex-basis: auto;
        }

        .recommendation-page .filter-form .btn {
            width: 100%;
        }

        .recommendation-page .panel h2 {
            padding: 18px;
        }

        .recommendation-page .stats {
            grid-template-columns: 1fr;
        }

        .recommendation-page .card-top {
            flex-direction: column;
        }

        .recommendation-page .recommendation-list {
            padding: 16px;
        }

        .recommendation-page .empty-state {
            margin: 16px;
        }

        .recommendation-page .notification-pagination {
            align-items: flex-start;
            flex-direction: column;
            padding: 17px;
        }

        .recommendation-page .pagination-summary {
            width: 100%;
        }

        .recommendation-page .pagination {
            justify-content: flex-start;
            gap: 4px;
            margin-left: 0;
        }

        .recommendation-page .pagination a,
        .recommendation-page .pagination span {
            flex-basis: 32px;
            width: 32px;
            min-width: 32px;
            height: 32px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .recommendation-page .btn,
        .recommendation-page .recommendation-card {
            transition: none;
        }
    }
</style>
@endsection

@section('content')

<div class="recommendation-page">



    <div class="page-header">

        <div>

            <h1>Rekomendasi Tindakan</h1>

            <p class="subtitle">

                Daftar tindakan perbaikan berdasarkan analisis QoS, RCA, dan DSS.

            </p>

        </div>



        <div class="date-label">

            {{ now()->translatedFormat('d F Y') }}

        </div>

    </div>



    <section class="panel">

        <h2>Filter Sekolah</h2>



        <form method="GET"

              action="{{ route('recommendations.index') }}"

              class="filter-form">



            <div class="form-group">

                <label for="school_id">Sekolah</label>



                <select name="school_id" id="school_id">

                    <option value="">Semua Sekolah</option>



                    @foreach ($schools as $school)

                        <option

                            value="{{ $school->id }}"

                            @selected((string) $selectedSchoolId === (string) $school->id)

                        >

                            {{ $school->nama_sekolah }}

                        </option>

                    @endforeach

                </select>

            </div>



            <button type="submit" class="btn btn-primary">

                Tampilkan

            </button>



            <a href="{{ route('recommendations.index') }}"

               class="btn btn-reset">

                Reset

            </a>

        </form>

    </section>



    @php

        $allMeasurements = $measurements->getCollection();



        $recommendationCount = $allMeasurements->sum(

            fn ($measurement) =>

                count($measurement->dss['recommendations'] ?? [])

        );



        $criticalCount = $allMeasurements->sum(

            fn ($measurement) =>

                collect($measurement->dss['recommendations'] ?? [])

                    ->filter(fn ($item) => ($item['priority'] ?? '') === 'KRITIS')

                    ->count()

        );



        $highCount = $allMeasurements->sum(

            fn ($measurement) =>

                collect($measurement->dss['recommendations'] ?? [])

                    ->filter(fn ($item) => ($item['priority'] ?? '') === 'TINGGI')

                    ->count()

        );

    @endphp



    <div class="stats">

        <div class="stat-card">

            <div class="stat-label">Rekomendasi pada halaman ini</div>

            <div class="stat-value">{{ $recommendationCount }}</div>

            <div class="stat-note">

                Berdasarkan data pengukuran yang ditampilkan

            </div>

        </div>



        <div class="stat-card">

            <div class="stat-label">Prioritas kritis</div>

            <div class="stat-value">{{ $criticalCount }}</div>

            <div class="stat-note">

                Tindakan dengan prioritas KRITIS

            </div>

        </div>



        <div class="stat-card">

            <div class="stat-label">Prioritas tinggi</div>

            <div class="stat-value">{{ $highCount }}</div>

            <div class="stat-note">

                Tindakan dengan prioritas TINGGI

            </div>

        </div>

    </div>



    <section class="panel">

        <h2>Daftar Rekomendasi</h2>



        @php

            $hasRecommendations = $allMeasurements->contains(

                fn ($measurement) =>

                    !empty($measurement->dss['recommendations'] ?? [])

            );

        @endphp



        @if ($hasRecommendations)

            <div class="recommendation-list">

                @foreach ($allMeasurements as $measurement)

                    @php

                        $items = $measurement->dss['recommendations'] ?? [];

                    @endphp



                    @foreach ($items as $item)

                        @php

                            $priority = strtoupper($item['priority'] ?? 'SEDANG');



                            $priorityClass = match ($priority) {

                                'KRITIS' => 'priority-kritis',

                                'TINGGI' => 'priority-tinggi',

                                'RENDAH' => 'priority-rendah',

                                default => 'priority-sedang',

                            };



                            $condition =

                                $measurement->qos_classification['condition']

                                ?? 'BELUM DIKLASIFIKASI';



                            $qosIndex =

                                $measurement->qos_classification['qos_index']

                                ?? null;

                        @endphp



                        <article class="recommendation-card">

                            <div class="card-top">

                                <div>

                                    <div class="school-name">

                                        {{ $measurement->school->nama_sekolah ?? 'Sekolah tidak diketahui' }}

                                        — {{ $item['parameter'] ?? 'QoS' }}

                                    </div>



                                    <div class="meta">

                                        Waktu pengukuran:

                                        {{ $measurement->measured_at

                                            ? \Illuminate\Support\Carbon::parse($measurement->measured_at)->format('d/m/Y H:i:s')

                                            : '-' }}

                                    </div>

                                </div>



                                <span class="priority {{ $priorityClass }}">

                                    {{ $priority }}

                                </span>

                            </div>



                            <div class="action-label">

                                Tindakan yang disarankan

                            </div>



                            <p class="action-text">

                                {{ $item['action'] ?? 'Lakukan pemeriksaan jaringan lebih lanjut.' }}

                            </p>



                            <div class="qos-info">

                                <span class="qos-chip">

                                    Kondisi QoS: {{ $condition }}

                                </span>



                                @if ($qosIndex !== null)

                                    <span class="qos-chip">

                                        Indeks QoS:

                                        {{ number_format((float) $qosIndex, 2) }}

                                    </span>

                                @endif

                            </div>

                        </article>

                    @endforeach

                @endforeach

            </div>

        @else

            <div class="empty-state">

                <strong>Belum ada rekomendasi tindakan.</strong>

                <br>

                Rekomendasi akan ditampilkan ketika hasil analisis QoS membutuhkan tindakan.

            </div>

        @endif



        {{-- PAGINATION --}}

        @if ($measurements->hasPages())

            <div class="notification-pagination">



                <div class="pagination-summary">

                    Menampilkan {{ $measurements->firstItem() }}

                    sampai {{ $measurements->lastItem() }}

                    dari {{ $measurements->total() }} data

                </div>



                <div class="pagination">

                    {{-- Previous --}}

                    @if ($measurements->onFirstPage())

                        <span class="pagination-disabled"

                              aria-label="Sebelumnya">‹</span>

                    @else

                        <a href="{{ $measurements->previousPageUrl() }}"

                           aria-label="Sebelumnya">‹</a>

                    @endif



                    {{-- Nomor halaman: maksimal 3 nomor --}}

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



                    @foreach ($measurements->getUrlRange($startPage, $endPage) as $page => $url)

                        @if ($page == $currentPage)

                            <span class="pagination-active"

                                  aria-current="page">{{ $page }}</span>

                        @else

                            <a href="{{ $url }}">{{ $page }}</a>

                        @endif

                    @endforeach



                    {{-- Next --}}

                    @if ($measurements->hasMorePages())

                        <a href="{{ $measurements->nextPageUrl() }}"

                           aria-label="Berikutnya">›</a>

                    @else

                        <span class="pagination-disabled"

                              aria-label="Berikutnya">›</span>

                    @endif

                </div>



            </div>

        @endif

    </section>



</div>

@endsection
