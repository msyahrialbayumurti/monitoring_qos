@extends('layouts.app')

@section('title', 'Notifikasi')

@section('styles')
<style>
    .notification-page {
        --notif-ink: #102b59;
        --notif-muted: #6f82a3;
        --notif-line: #dce7f8;
        --notif-blue: #1768e5;
        width: 100%;
        min-width: 0;
        color: var(--notif-ink);
    }

    .notification-page *,
    .notification-page *::before,
    .notification-page *::after {
        box-sizing: border-box;
    }

    .notification-page .notification-header {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        overflow: hidden;
        margin-bottom: 22px;
        padding: 25px 28px;
        border: 1px solid rgba(190,213,249,.9);
        border-radius: 20px;
        background:
            radial-gradient(circle at 90% 0%, rgba(91,157,255,.20), transparent 30%),
            linear-gradient(120deg, rgba(255,255,255,.97), rgba(239,246,255,.94));
        box-shadow: 0 12px 30px rgba(34,83,153,.07);
    }

    .notification-page .notification-header::after {
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

    .notification-page .notification-header > div:first-child {
        position: relative;
        z-index: 1;
    }

    .notification-page .notification-header h1 {
        margin: 0 0 8px;
        color: var(--notif-ink);
        font-size: clamp(25px, 2.5vw, 32px);
        font-weight: 800;
        letter-spacing: -.035em;
        line-height: 1.2;
    }

    .notification-page .notification-header p {
        max-width: 700px;
        margin: 0;
        color: var(--notif-muted);
        font-size: 13px;
        line-height: 1.7;
    }

    .notification-page .notification-date {
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

    .notification-page .notification-filter,
    .notification-page .notification-panel {
        overflow: hidden;
        margin-bottom: 20px;
        border: 1px solid var(--notif-line);
        border-radius: 18px;
        background: rgba(255,255,255,.96);
        box-shadow: 0 12px 30px rgba(34,83,153,.06);
    }

    .notification-page .notification-filter {
        padding: 22px 24px;
    }

    .notification-page .filter-heading {
        display: flex;
        align-items: center;
        gap: 11px;
        color: var(--notif-ink);
        font-size: 15px;
        font-weight: 800;
    }

    .notification-page .filter-heading::before {
        content: "⌕";
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border: 1px solid #d7e6ff;
        border-radius: 11px;
        background: #eaf2ff;
        color: var(--notif-blue);
        font-size: 19px;
    }

    .notification-page .notification-filter form {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 18px;
    }

    .notification-page .filter-field {
        display: flex;
        flex: 1 1 280px;
        flex-direction: column;
        gap: 8px;
        min-width: 0;
        max-width: 480px;
    }

    .notification-page .notification-filter label {
        color: #496486;
        font-size: 12px;
        font-weight: 700;
    }

    .notification-page .notification-filter select {
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

    .notification-page .notification-filter select:focus {
        border-color: #4389f4;
        box-shadow: 0 0 0 4px rgba(40,119,235,.11);
    }

    .notification-page .notification-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
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

    .notification-page .notification-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(23,104,229,.22);
    }

    .notification-page .notification-button.button-reset {
        border-color: #d8e3f2;
        background: #fff;
        color: #496486;
        box-shadow: none;
    }

    .notification-page .notification-button.button-reset:hover {
        background: #f4f8ff;
        box-shadow: none;
    }

    .notification-page .notification-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 21px 24px;
        border-bottom: 1px solid #e8effa;
        background: linear-gradient(120deg, #fff, #f7faff);
    }

    .notification-page .notification-panel-header strong {
        color: var(--notif-ink);
        font-size: 16px;
        font-weight: 800;
    }

    .notification-page .notification-total {
        padding: 7px 11px;
        border: 1px solid #dce9ff;
        border-radius: 999px;
        background: #f3f7ff;
        color: #426aab;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .notification-page .notification-list {
        padding: 0 24px;
    }

    .notification-page .notification-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 20px 0;
        border-bottom: 1px solid #edf2fa;
    }

    .notification-page .notification-item:last-child {
        border-bottom: 0;
    }

    .notification-page .notification-symbol {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        border: 1px solid #d5e5ff;
        border-radius: 13px;
        background: #eaf2ff;
        color: #1765dc;
        font-size: 18px;
        font-weight: 800;
    }

    .notification-page .notification-symbol.danger {
        border-color: #ffd4d8;
        background: #ffe8ea;
        color: #b42323;
    }

    .notification-page .notification-symbol.warning {
        border-color: #f5e3a8;
        background: #fff5d9;
        color: #956500;
    }

    .notification-page .notification-content {
        flex: 1;
        min-width: 0;
        padding-top: 1px;
    }

    .notification-page .notification-item h3 {
        margin: 0 0 6px;
        color: #173f78;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .notification-page .notification-item p {
        margin: 0;
        color: #526b8c;
        font-size: 12px;
        line-height: 1.75;
        overflow-wrap: anywhere;
    }

    .notification-page .notification-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 9px;
        color: #8796ad;
        font-size: 11px;
        line-height: 1.6;
    }

    .notification-page .notification-empty {
        padding: 50px 18px;
        color: #7183a2;
        font-size: 12px;
        line-height: 1.8;
        text-align: center;
    }

    .notification-page .notification-empty strong {
        display: block;
        margin-bottom: 6px;
        color: #294568;
        font-size: 15px;
    }

    .notification-page .notification-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        width: 100%;
        padding: 17px 24px;
        border-top: 1px solid #e3eaf4;
        background: #fbfdff;
    }

    .notification-page .pagination-summary {
        color: #7183a2;
        font-size: 11px;
        line-height: 1.6;
        text-align: left;
    }

    .notification-page .pagination {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 6px;
        margin-left: auto;
    }

    .notification-page .pagination a,
    .notification-page .pagination span {
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

    .notification-page .pagination .pagination-active {
        border-color: #1765dc;
        background: #1765dc;
        color: #fff;
        font-weight: 800;
        box-shadow: 0 5px 12px rgba(23,101,220,.18);
    }

    .notification-page .pagination a:hover {
        border-color: #1765dc;
        background: #eaf2ff;
        color: #1765dc;
    }

    .notification-page .pagination .pagination-disabled {
        background: #f7f9fc;
        color: #a0aec0;
        cursor: not-allowed;
    }

    @media (max-width: 900px) {
        .notification-page .filter-field {
            max-width: none;
        }
    }

    @media (max-width: 650px) {
        .notification-page .notification-header {
            align-items: flex-start;
            flex-direction: column;
            padding: 21px;
        }

        .notification-page .notification-filter {
            padding: 18px;
        }

        .notification-page .notification-filter form {
            align-items: stretch;
            flex-direction: column;
        }

        .notification-page .filter-field {
            width: 100%;
            max-width: none;
            flex-basis: auto;
        }

        .notification-page .notification-filter form > div,
        .notification-page .notification-filter form > a,
        .notification-page .notification-filter form > button {
            width: 100%;
        }

        .notification-page .notification-button {
            width: 100%;
        }

        .notification-page .notification-panel-header {
            align-items: flex-start;
            flex-direction: column;
            padding: 18px;
        }

        .notification-page .notification-list {
            padding: 0 17px;
        }

        .notification-page .notification-item {
            gap: 10px;
        }

        .notification-page .notification-symbol {
            width: 38px;
            height: 38px;
            flex-basis: 38px;
            border-radius: 11px;
        }

        .notification-page .notification-pagination {
            align-items: flex-start;
            flex-direction: column;
            padding: 17px;
        }

        .notification-page .pagination-summary {
            width: 100%;
            text-align: left;
        }

        .notification-page .pagination {
            justify-content: flex-start;
            gap: 4px;
            max-width: 100%;
            margin-left: 0;
        }

        .notification-page .pagination a,
        .notification-page .pagination span {
            flex-basis: 32px;
            width: 32px;
            min-width: 32px;
            height: 32px;
        }
    }

    .notification-page .action-alert {
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

    .notification-page .action-alert.error {
        border-color: #ffd4d8;
        background: #fff5f5;
        color: #b42323;
    }

    .notification-page .notification-panel-header {
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .notification-page .notification-panel-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 8px;
    }

    .notification-page .selection-hint {
        margin-top: 5px;
        color: #7183a2;
        font-size: 11px;
        font-weight: 400;
        line-height: 1.5;
    }

    .notification-page .notification-select-all {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: 8px;
        color: #496486;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }

    .notification-page input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #1768e5;
        cursor: pointer;
    }

    .notification-page .notification-button.notification-button-sm {
        min-height: 36px;
        padding: 0 13px;
        border-radius: 9px;
        font-size: 11px;
    }

    .notification-page .notification-button.button-danger {
        border-color: #f1b5bb;
        background: #fff;
        color: #b42323;
        box-shadow: none;
    }

    .notification-page .notification-button.button-danger:hover {
        background: #fff5f5;
        box-shadow: none;
    }

    .notification-page .notification-button.button-danger-solid {
        border-color: #c62828;
        background: linear-gradient(135deg, #ef5350, #c62828);
        box-shadow: 0 7px 15px rgba(198, 40, 40, .16);
    }

    .notification-page .notification-button.button-danger-solid:hover {
        box-shadow: 0 10px 20px rgba(198, 40, 40, .22);
    }

    .notification-page .notification-button:disabled {
        opacity: .45;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .notification-page .notification-checkbox {
        flex: 0 0 16px;
        margin-top: 15px;
    }

    .notification-page .notification-body {
        flex: 1;
        min-width: 0;
        padding-top: 1px;
    }

    .notification-page .notification-delete-form {
        display: flex;
        align-self: center;
        flex: 0 0 auto;
        margin: 0;
    }

    @media (prefers-reduced-motion: reduce) {
        .notification-page .notification-button {
            transition: none;
        }
    }
</style>
@endsection

@section('content')

<div class="notification-page">



    <div class="notification-header">

        <div>

            <h1>Notifikasi &amp; Alarm</h1>

            <p>Informasi hasil pengukuran dan kondisi monitoring QoS.</p>

        </div>



        <div class="notification-date">

            {{ now()->format('d F Y') }}

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
        id="notificationBulkForm"
        method="POST"
        action="{{ route('notifications.destroy') }}"
        onsubmit="return confirm('Hapus notifikasi yang dipilih?');"
    >

        @csrf

        @method('DELETE')

        <input type="hidden" name="school_id" value="{{ request('school_id') }}">

    </form>



    <div class="notification-filter">

        <div class="filter-heading">Filter Notifikasi</div>



        <form method="GET" action="{{ route('notifications.index') }}">

            <div>

                <label for="school_id">Sekolah</label>



                <select name="school_id" id="school_id">

                    <option value="">Semua Sekolah</option>



                    @foreach ($schools as $school)

                        <option

                            value="{{ $school->id }}"

                            @selected((string) request('school_id') === (string) $school->id)

                        >

                            {{ $school->nama_sekolah }}

                        </option>

                    @endforeach

                </select>

            </div>



            <button type="submit" class="notification-button">

                Tampilkan

            </button>



            <a href="{{ route('notifications.index') }}"

               class="notification-button"

               style="background:#edf2f8;color:#304563;">

                Reset

            </a>

        </form>

    </div>



    <div class="notification-panel">



        <div class="notification-panel-header">

            <div>

                <strong>Riwayat Notifikasi</strong>



                @if ($notifications->count() > 0)

                    <div class="selection-hint">

                        Centang notifikasi untuk menghapus sebagian atau gunakan Hapus Semua.

                    </div>



                    <label class="notification-select-all">

                        <input
                            type="checkbox"
                            id="notificationSelectAll"
                            aria-label="Pilih semua notifikasi di halaman ini"
                        >

                        <span>Pilih semua di halaman ini</span>

                    </label>

                @endif

            </div>



            <div class="notification-panel-actions">

                <span class="notification-total">

                    {{ $measurements->total() }} data pengukuran

                </span>



                @if ($notifications->count() > 0)

                    <button
                        type="submit"
                        form="notificationBulkForm"
                        id="notificationDeleteSelected"
                        class="notification-button notification-button-sm button-danger-solid"
                        disabled
                    >

                        Hapus Terpilih

                    </button>



                    <form
                        method="POST"
                        action="{{ route('notifications.destroy') }}"
                        onsubmit="return confirm('Hapus SEMUA notifikasi sesuai filter? Tindakan ini tidak dapat dibatalkan.');"
                    >

                        @csrf

                        @method('DELETE')

                        <input type="hidden" name="school_id" value="{{ request('school_id') }}">

                        <button type="submit" name="delete_all" value="1" class="notification-button notification-button-sm button-danger">

                            Hapus Semua

                        </button>

                    </form>

                @endif

            </div>

        </div>



        <div class="notification-list">
        @forelse ($notifications as $notification)

            <div class="notification-item">



                <input
                    type="checkbox"
                    name="ids[]"
                    value="{{ $notification['id'] }}"
                    form="notificationBulkForm"
                    class="notification-checkbox"
                    aria-label="Pilih notifikasi {{ $notification['measured_at']?->format('d/m/Y H:i:s') }}"
                >



                <div class="notification-symbol {{ $notification['level'] }}">

                    @if ($notification['level'] === 'danger')

                        !

                    @elseif ($notification['level'] === 'warning')

                        ⚠

                    @else

                        ✓

                    @endif

                </div>



                <div class="notification-body">

                    <h3>{{ $notification['title'] }}</h3>



                    <p>{{ $notification['message'] }}</p>



                    <div class="notification-meta">

                        {{ $notification['school'] }}

                        &middot;

                        {{ $notification['measured_at']?->format('d/m/Y H:i:s') ?? 'Waktu tidak tersedia' }}

                    </div>

                </div>



                <form
                    method="POST"
                    action="{{ route('notifications.destroy') }}"
                    class="notification-delete-form"
                    onsubmit="return confirm('Hapus notifikasi ini?');"
                >

                    @csrf

                    @method('DELETE')

                    <input type="hidden" name="school_id" value="{{ request('school_id') }}">

                    <button type="submit" name="ids[]" value="{{ $notification['id'] }}" class="notification-button notification-button-sm button-danger">

                        Hapus

                    </button>

                </form>



            </div>

        @empty

            <div class="notification-empty">

                Belum ada data pengukuran untuk ditampilkan.

            </div>

        @endforelse



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



                    {{-- Nomor halaman --}}

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



    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('notificationSelectAll');
        const deleteButton = document.getElementById('notificationDeleteSelected');
        const checkboxes = Array.from(document.querySelectorAll('.notification-checkbox'));

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
