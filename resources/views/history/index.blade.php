@extends('layouts.app')

@section('title', 'Riwayat Monitoring QoS')

@section('styles')
<style>
    .history-page {
        --history-ink: #102b59;
        --history-muted: #6f82a3;
        --history-line: #dce7f8;
        --history-blue: #1768e5;
        width: 100%;
        min-width: 0;
        color: var(--history-ink);
        font-family: Arial, Helvetica, sans-serif;
    }

    .history-page *,
    .history-page *::before,
    .history-page *::after {
        box-sizing: border-box;
    }

    .history-page .header {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 22px;
        padding: 25px 28px;
        overflow: hidden;
        border: 1px solid rgba(190, 213, 249, .9);
        border-radius: 20px;
        background:
            radial-gradient(circle at 90% 0%, rgba(91, 157, 255, .20), transparent 30%),
            linear-gradient(120deg, rgba(255,255,255,.97), rgba(239,246,255,.94));
        box-shadow: 0 12px 30px rgba(34, 83, 153, .07);
    }

    .history-page .header::after {
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

    .history-page .header > div:first-child {
        position: relative;
        z-index: 1;
    }

    .history-page .header h1 {
        margin: 0;
        color: var(--history-ink);
        font-size: clamp(25px, 2.5vw, 32px);
        font-weight: 800;
        letter-spacing: -.035em;
        line-height: 1.2;
    }

    .history-page .header p {
        max-width: 650px;
        margin: 8px 0 0;
        color: var(--history-muted);
        font-size: 13px;
        line-height: 1.7;
    }

    .history-page .date-box {
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
        box-shadow: 0 5px 14px rgba(36, 83, 150, .05);
    }

    .history-page .filter-card,
    .history-page .table-card {
        overflow: hidden;
        border: 1px solid var(--history-line);
        border-radius: 18px;
        background: rgba(255,255,255,.95);
        box-shadow: 0 12px 30px rgba(34, 83, 153, .06);
    }

    .history-page .filter-card {
        margin-bottom: 20px;
        padding: 22px 24px;
    }

    .history-page .filter-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 17px;
        color: var(--history-ink);
        font-size: 15px;
        font-weight: 800;
    }

    .history-page .filter-title::before {
        content: "⌕";
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border: 1px solid #d7e6ff;
        border-radius: 11px;
        background: #eaf2ff;
        color: var(--history-blue);
        font-size: 19px;
    }

    .history-page .filter-form {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 12px;
    }

    .history-page .form-group {
        display: flex;
        flex: 1 1 280px;
        flex-direction: column;
        gap: 8px;
        min-width: 0;
        max-width: 480px;
    }

    .history-page .form-group label {
        color: #496486;
        font-size: 12px;
        font-weight: 700;
    }

    .history-page .form-group select {
        width: 100%;
        min-height: 44px;
        padding: 0 13px;
        border: 1px solid #d8e3f2;
        border-radius: 11px;
        outline: none;
        background: #fbfdff;
        color: #284467;
        font-size: 12px;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .history-page .form-group select:focus {
        border-color: #4389f4;
        box-shadow: 0 0 0 4px rgba(40,119,235,.11);
    }

    .history-page .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 112px;
        min-height: 44px;
        padding: 0 17px;
        border: 1px solid #1768e5;
        border-radius: 11px;
        background: linear-gradient(135deg, #3185ff, #125bd0);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        box-shadow: 0 7px 15px rgba(23,104,229,.16);
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .history-page .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(23,104,229,.22);
    }

    .history-page .btn-reset {
        min-width: 90px;
        border-color: #d8e3f2;
        background: #fff;
        color: #496486;
        box-shadow: none;
    }

    .history-page .btn-reset:hover {
        background: #f4f8ff;
        box-shadow: none;
    }

    .history-page .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 21px 24px;
        border-bottom: 1px solid #e8effa;
        background: linear-gradient(120deg, #fff, #f7faff);
    }

    .history-page .table-title {
        color: var(--history-ink);
        font-size: 16px;
        font-weight: 800;
    }

    .history-page .table-info {
        padding: 7px 11px;
        border: 1px solid #dce9ff;
        border-radius: 999px;
        background: #f3f7ff;
        color: #426aab;
        font-size: 11px;
        font-weight: 700;
    }

    .history-page .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .history-page table {
        width: 100%;
        min-width: 1050px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .history-page thead th {
        padding: 15px 16px;
        border-bottom: 1px solid #e1eaf8;
        background: #f0f5ff;
        color: #6b83a8;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .07em;
        text-align: left;
        white-space: nowrap;
    }

    .history-page tbody td {
        padding: 16px;
        border-bottom: 1px solid #edf2fa;
        color: #3e5776;
        font-size: 12px;
        white-space: nowrap;
    }

    .history-page tbody tr:last-child td {
        border-bottom: 0;
    }

    .history-page tbody tr {
        transition: background .15s ease;
    }

    .history-page tbody tr:hover {
        background: #f8fbff;
    }

    .history-page .school-name {
        color: #183d70;
        font-weight: 800;
    }

    .history-page .number {
        color: #294e7d;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    .history-page .ping-connected {
        color: #15965b;
        font-weight: 800;
    }

    .history-page .ping-timeout {
        color: #d84b4b;
        font-weight: 800;
    }

    .history-page .ping-unknown {
        color: #718096;
        font-weight: 700;
    }

    .history-page .status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .history-page .status-baik {
        background: #ddf8e9;
        color: #137d4f;
    }

    .history-page .status-sedang {
        background: #fff1c9;
        color: #9b6900;
    }

    .history-page .status-buruk {
        background: #ffe3e5;
        color: #b82f3b;
    }

    .history-page .status-null {
        background: #eef2f7;
        color: #718096;
    }

    .history-page .qos-index {
        margin-top: 6px;
        color: #7a8ca5;
        font-size: 10px;
    }

    .history-page .pagination-area {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        padding: 17px 22px;
        border-top: 1px solid #e8effa;
        background: #fbfdff;
    }

    .history-page .pagination-info {
        color: #7b8ca5;
        font-size: 11px;
        line-height: 1.6;
    }

    .history-page .pagination {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 5px;
        margin-left: auto;
    }

    .history-page .pagination a,
    .history-page .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 35px;
        width: 35px;
        min-width: 35px;
        height: 35px;
        padding: 0;
        margin: 0;
        border: 1px solid #dce4ee;
        border-radius: 9px;
        background: #fff;
        color: #48627f;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        line-height: 1;
        text-align: center;
        text-decoration: none;
    }

    .history-page .pagination a:hover {
        border-color: #1765dc;
        background: #edf4ff;
        color: #1765dc;
    }

    .history-page .pagination span.active {
        border-color: #1469dd;
        background: #1469dd;
        color: #fff;
        font-weight: 800;
        box-shadow: 0 5px 12px rgba(20,105,221,.18);
    }

    .history-page .pagination span.disabled {
        background: #f7f9fc;
        color: #a0aec0;
        opacity: .7;
        cursor: not-allowed;
    }

    .history-page .empty {
        padding: 62px 20px;
        text-align: center;
    }

    .history-page .empty-title {
        margin-bottom: 7px;
        color: #294568;
        font-size: 15px;
        font-weight: 800;
    }

    .history-page .empty-text {
        color: #7a8ca5;
        font-size: 12px;
        line-height: 1.7;
    }

    @media (max-width: 900px) {
        .history-page .header h1 {
            font-size: 25px;
        }

        .history-page .filter-form {
            align-items: stretch;
        }

        .history-page .form-group {
            flex-basis: 100%;
            max-width: none;
        }
    }

    @media (max-width: 650px) {
        .history-page .header {
            align-items: flex-start;
            flex-direction: column;
            gap: 14px;
            padding: 21px;
        }

        .history-page .header h1 {
            font-size: 23px;
        }

        .history-page .date-box {
            align-self: flex-start;
        }

        .history-page .filter-card {
            padding: 17px;
        }

        .history-page .filter-form {
            flex-direction: column;
            align-items: stretch;
        }

        .history-page .form-group {
            width: 100%;
            flex-basis: auto;
        }

        .history-page .filter-form .btn {
            width: 100%;
        }

        .history-page .table-header {
            align-items: flex-start;
            flex-direction: column;
            padding: 17px;
        }

        .history-page .pagination-area {
            align-items: flex-start;
            flex-direction: column;
            padding: 17px;
        }

        .history-page .pagination {
            justify-content: flex-start;
            width: 100%;
            margin-left: 0;
        }
    }

    .history-page .action-alert {
        margin-bottom: 18px;
        padding: 14px 16px;
        border: 1px solid #cfe7d9;
        border-radius: 12px;
        background: #f0fbf5;
        color: #137d4f;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.6;
    }

    .history-page .action-alert.error {
        border-color: #ffd4d8;
        background: #fff5f5;
        color: #b42323;
    }

    .history-page .table-header-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 8px;
    }

    .history-page .selection-hint {
        margin-top: 5px;
        color: #7183a2;
        font-size: 11px;
        line-height: 1.5;
    }

    .history-page .btn-sm {
        min-width: auto;
        min-height: 36px;
        padding: 0 13px;
        border-radius: 9px;
        font-size: 11px;
    }

    .history-page .btn-danger {
        border-color: #c62828;
        background: linear-gradient(135deg, #ef5350, #c62828);
        box-shadow: 0 7px 15px rgba(198, 40, 40, .16);
    }

    .history-page .btn-danger:hover {
        box-shadow: 0 10px 20px rgba(198, 40, 40, .22);
    }

    .history-page .btn-danger:disabled {
        opacity: .45;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .history-page .btn-outline-danger {
        border-color: #f1b5bb;
        background: #fff;
        color: #b42323;
        box-shadow: none;
    }

    .history-page .btn-outline-danger:hover {
        background: #fff5f5;
        box-shadow: none;
    }

    .history-page .select-cell {
        width: 42px;
        text-align: center;
    }

    .history-page .select-cell input {
        width: 16px;
        height: 16px;
        accent-color: #1768e5;
        cursor: pointer;
    }

    .history-page .action-form {
        display: inline;
        margin: 0;
    }

    @media (prefers-reduced-motion: reduce) {
        .history-page .btn,
        .history-page tbody tr {
            transition: none;
        }
    }
</style>
@endsection

@section('content')



<div class="history-page">



    {{-- HEADER --}}

    <div class="header">

        <div>

            <h1>Riwayat Monitoring QoS</h1>

            <p>

                Histori hasil pengukuran QoS jaringan

                yang tersimpan pada database.

            </p>

        </div>



        <div class="date-box">

            {{ now()->translatedFormat('d F Y') }}

        </div>

    </div>



    @if (session('success'))

        <div class="action-alert">{{ session('success') }}</div>

    @endif



    @if (session('error') || $errors->any())

        <div class="action-alert error">

            {{ session('error') ?? $errors->first() }}

        </div>

    @endif



    <form
        id="historyBulkForm"
        method="POST"
        action="{{ route('monitoring.history.destroy') }}"
        onsubmit="return confirm('Hapus data monitoring yang dipilih?');"
    >

        @csrf

        @method('DELETE')

        <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">

    </form>



    {{-- FILTER --}}

    <div class="filter-card">

        <div class="filter-title">

            Filter Riwayat Monitoring

        </div>



        <form

            method="GET"

            action="{{ route('monitoring.history') }}"

            class="filter-form"

        >

            <div class="form-group">

                <label for="school_id">Sekolah</label>



                <select name="school_id" id="school_id">

                    <option value="">Semua Sekolah</option>



                    @foreach ($schools as $school)

                        <option

                            value="{{ $school->id }}"

                            {{ (string) ($selectedSchoolId ?? request('school_id')) === (string) $school->id ? 'selected' : '' }}

                        >

                            {{ $school->nama_sekolah }}

                        </option>

                    @endforeach

                </select>

            </div>



            <button type="submit" class="btn">

                Tampilkan

            </button>



            <a

                href="{{ route('monitoring.history') }}"

                class="btn btn-reset"

            >

                Reset

            </a>

        </form>

    </div>



    {{-- TABEL --}}

    <div class="table-card">

        <div class="table-header">

            <div>

                <div class="table-title">

                    Data Hasil Monitoring

                </div>



                @if ($measurements->count() > 0)

                    <div class="selection-hint">

                        Centang data untuk menghapus sebagian atau gunakan Hapus Semua.

                    </div>

                @endif

            </div>



            <div class="table-header-actions">

                <div class="table-info">

                    {{ $measurements->total() }} data monitoring

                </div>



                @if ($measurements->count() > 0)

                    <button
                        type="submit"
                        form="historyBulkForm"
                        id="historyDeleteSelected"
                        class="btn btn-danger btn-sm"
                        disabled
                    >

                        Hapus Terpilih

                    </button>



                    <form
                        method="POST"
                        action="{{ route('monitoring.history.destroy') }}"
                        onsubmit="return confirm('Hapus SEMUA data monitoring sesuai filter? Tindakan ini tidak dapat dibatalkan.');"
                    >

                        @csrf

                        @method('DELETE')

                        <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">

                        <button type="submit" name="delete_all" value="1" class="btn btn-outline-danger btn-sm">

                            Hapus Semua

                        </button>

                    </form>

                @endif

            </div>

        </div>



        @if ($measurements->count() > 0)



            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th class="select-cell">

                                <input
                                    type="checkbox"
                                    id="historySelectAll"
                                    aria-label="Pilih semua data di halaman ini"
                                >

                            </th>

                            <th>WAKTU</th>

                            <th>SEKOLAH</th>

                            <th>THROUGHPUT</th>

                            <th>DELAY</th>

                            <th>JITTER</th>

                            <th>PACKET LOSS</th>

                            <th>PING</th>

                            <th>STATUS</th>

                            <th>AKSI</th>

                        </tr>

                    </thead>



                    <tbody>

                        @foreach ($measurements as $measurement)

                            <tr>

                                <td class="select-cell">

                                    <input
                                        type="checkbox"
                                        name="ids[]"
                                        value="{{ $measurement->id }}"
                                        form="historyBulkForm"
                                        class="history-checkbox"
                                        aria-label="Pilih data monitoring {{ $measurement->measured_at?->format('d/m/Y H:i:s') }}"
                                    >

                                </td>



                                {{-- WAKTU --}}

                                <td>

                                    {{ $measurement->measured_at?->format('d/m/Y H:i:s') ?? '-' }}

                                </td>



                                {{-- SEKOLAH --}}

                                <td>

                                    <div class="school-name">

                                        {{ $measurement->school?->nama_sekolah ?? '-' }}

                                    </div>

                                </td>



                                {{-- THROUGHPUT --}}

                                <td>

                                    <span class="number">

                                        {{ $measurement->throughput !== null

                                            ? number_format((float) $measurement->throughput, 2)

                                            : '-' }}

                                    </span>

                                    Mbps

                                </td>



                                {{-- DELAY --}}

                                <td>

                                    <span class="number">

                                        {{ $measurement->delay !== null

                                            ? number_format((float) $measurement->delay, 2)

                                            : '-' }}

                                    </span>

                                    ms

                                </td>



                                {{-- JITTER --}}

                                <td>

                                    <span class="number">

                                        {{ $measurement->jitter !== null

                                            ? number_format((float) $measurement->jitter, 2)

                                            : '-' }}

                                    </span>

                                    ms

                                </td>



                                {{-- PACKET LOSS --}}

                                <td>

                                    <span class="number">

                                        {{ $measurement->packet_loss !== null

                                            ? number_format((float) $measurement->packet_loss, 2)

                                            : '-' }}

                                    </span>

                                    %

                                </td>



                                {{-- PING --}}

                                <td>

                                    @if ($measurement->ping_status === 'TERHUBUNG')

                                        <span class="ping-connected">

                                            {{ $measurement->ping !== null

                                                ? number_format((float) $measurement->ping, 2) . ' ms'

                                                : 'TERHUBUNG' }}

                                        </span>



                                    @elseif ($measurement->ping_status === 'TIMEOUT')

                                        <span class="ping-timeout">

                                            TIMEOUT

                                        </span>



                                    @elseif ($measurement->ping_status === 'DATA')

                                        <span class="number">

                                            {{ $measurement->ping !== null

                                                ? number_format((float) $measurement->ping, 2) . ' ms'

                                                : '-' }}

                                        </span>



                                    @else

                                        <span class="ping-unknown">

                                            TIDAK DIKETAHUI

                                        </span>

                                    @endif

                                </td>



                                {{-- STATUS QOS --}}

                                <td>

                                    @php

                                        $classification = $measurement->qos_classification ?? [];

                                        $condition = $classification['condition'] ?? null;

                                        $qosIndex = $classification['qos_index'] ?? null;

                                    @endphp



                                    @if ($condition === 'BAIK')

                                        <span class="status status-baik">

                                            BAIK

                                        </span>



                                    @elseif ($condition === 'SEDANG')

                                        <span class="status status-sedang">

                                            SEDANG

                                        </span>



                                    @elseif ($condition === 'BURUK')

                                        <span class="status status-buruk">

                                            BURUK

                                        </span>



                                    @else

                                        <span class="status status-null">

                                            BELUM DIKLASIFIKASI

                                        </span>

                                    @endif



                                    @if ($qosIndex !== null)

                                        <div class="qos-index">

                                            Indeks QoS:

                                            {{ number_format((float) $qosIndex, 2) }}

                                        </div>

                                    @endif

                                </td>



                                {{-- AKSI --}}

                                <td>

                                    <form
                                        method="POST"
                                        action="{{ route('monitoring.history.destroy') }}"
                                        class="action-form"
                                        onsubmit="return confirm('Hapus data monitoring ini?');"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">

                                        <button type="submit" name="ids[]" value="{{ $measurement->id }}" class="btn btn-outline-danger btn-sm">

                                            Hapus

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>



            {{-- PAGINATION --}}

            <div class="pagination-area">

                <div class="pagination-info">

                    Menampilkan

                    {{ $measurements->firstItem() ?? 0 }}

                    -

                    {{ $measurements->lastItem() ?? 0 }}

                    dari

                    {{ $measurements->total() }}

                    data

                </div>



                <div class="pagination">

                    {{-- PREVIOUS --}}

                    @if ($measurements->onFirstPage())

                        <span class="disabled" aria-disabled="true">‹</span>

                    @else

                        <a

                            href="{{ $measurements->previousPageUrl() }}"

                            rel="prev"

                            aria-label="Halaman sebelumnya"

                        >‹</a>

                    @endif



                    {{-- NOMOR HALAMAN --}}

                    @foreach (

                        $measurements->getUrlRange(

                            max(1, $measurements->currentPage() - 2),

                            min(

                                $measurements->lastPage(),

                                $measurements->currentPage() + 2

                            )

                        ) as $page => $url

                    )

                        @if ($page == $measurements->currentPage())

                            <span class="active" aria-current="page">

                                {{ $page }}

                            </span>

                        @else

                            <a href="{{ $url }}">

                                {{ $page }}

                            </a>

                        @endif

                    @endforeach



                    {{-- NEXT --}}

                    @if ($measurements->hasMorePages())

                        <a

                            href="{{ $measurements->nextPageUrl() }}"

                            rel="next"

                            aria-label="Halaman berikutnya"

                        >›</a>

                    @else

                        <span class="disabled" aria-disabled="true">›</span>

                    @endif

                </div>

            </div>



        @else



            {{-- DATA KOSONG --}}

            <div class="empty">

                <div class="empty-title">

                    Belum ada data monitoring

                </div>



                <div class="empty-text">

                    Data akan muncul setelah Laravel Scheduler

                    menjalankan monitoring QoS.

                </div>

            </div>



        @endif

    </div>



</div>



@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('historySelectAll');
        const deleteButton = document.getElementById('historyDeleteSelected');
        const checkboxes = Array.from(document.querySelectorAll('.history-checkbox'));

        if (!selectAll || !deleteButton) {
            return;
        }

        const syncSelection = function () {
            const selected = checkboxes.filter((checkbox) => checkbox.checked).length;

            deleteButton.disabled = selected === 0;
            deleteButton.textContent = selected > 0
                ? `Hapus Terpilih (${selected})`
                : 'Hapus Terpilih';

            selectAll.checked = checkboxes.length > 0 && selected === checkboxes.length;
            selectAll.indeterminate = selected > 0 && selected < checkboxes.length;
        };

        selectAll.addEventListener('change', function () {
            checkboxes.forEach((checkbox) => {
                checkbox.checked = selectAll.checked;
            });

            syncSelection();
        });

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', syncSelection);
        });

        syncSelection();
    });
</script>
@endpush

@endsection
