<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'NETMONITOR')</title>

    <style>
        :root {
            --navy-950: #08152f;
            --navy-900: #0d2148;
            --navy-800: #12386d;
            --blue-600: #1765dc;
            --blue-500: #3288ff;
            --page-bg: #f3f6fc;
            --card-bg: #ffffff;
            --text-main: #172b4d;
            --text-muted: #7183a2;
            --border: #e5ebf4;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        html {
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            background: var(--page-bg);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* ========================================
           LAYOUT UTAMA
        ======================================== */

        .app-layout {
            width: 100%;
            min-height: 100vh;
        }

        /* ========================================
           SIDEBAR
        ======================================== */

        .app-layout > .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;

            width: var(--sidebar-width) !important;
            height: 100vh !important;
            height: 100dvh !important;

            display: flex !important;
            flex-direction: column !important;
            flex-wrap: nowrap !important;

            padding: 25px 16px 18px !important;

            overflow-x: hidden !important;
            overflow-y: auto !important;
            overscroll-behavior: contain;

            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.25) transparent;

            z-index: 1000 !important;

            color: #fff !important;

            background: linear-gradient(
                180deg,
                var(--navy-950),
                var(--navy-900) 52%,
                var(--navy-800)
            ) !important;

            border-right: 1px solid rgba(255,255,255,.08);
            box-shadow: 8px 0 28px rgba(8,21,47,.10);
        }

        .app-layout > .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .app-layout > .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .app-layout > .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,.25);
            border-radius: 10px;
        }

        /* ========================================
           BRAND NETMONITOR
        ======================================== */

        .app-layout .brand {
            display: flex !important;
            align-items: center !important;
            flex: 0 0 auto !important;

            gap: 12px !important;
            padding: 4px 8px 30px !important;
        }

        .app-layout .brand-icon {
            width: 43px !important;
            height: 43px !important;
            min-width: 43px !important;
            flex: 0 0 43px !important;

            display: flex !important;
            align-items: center !important;
            justify-content: center !important;

            border-radius: 13px !important;

            color: var(--blue-600) !important;

            background: linear-gradient(
                135deg,
                #fff,
                #dbeafe
            ) !important;

            font-size: 23px !important;
            font-weight: 800 !important;

            box-shadow: 0 5px 15px rgba(0,0,0,.15);
        }

        .app-layout .brand-text {
            min-width: 0;
        }

        .app-layout .brand-text h2 {
            color: #fff !important;
            font-size: 16px !important;
            line-height: 1.3 !important;
            letter-spacing: .5px !important;
            font-weight: 800 !important;
        }

        .app-layout .brand-text p {
            color: #a9c4ec !important;
            font-size: 9px !important;
            line-height: 1.5 !important;
            margin-top: 4px !important;
        }

        /* ========================================
           JUDUL MENU
        ======================================== */

        .app-layout .menu-title {
            flex: 0 0 auto !important;

            padding: 10px 12px !important;
            margin-bottom: 9px !important;

            color: #83a8dd !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            letter-spacing: 1.2px !important;
            text-transform: uppercase !important;
        }

        /* ========================================
           DAFTAR MENU
        ======================================== */

        .app-layout .menu {
            display: flex !important;
            flex-direction: column !important;
            flex-wrap: nowrap !important;

            flex: 0 0 auto !important;
            gap: 7px !important;

            width: 100% !important;
        }

        .app-layout .menu a {
            width: 100% !important;
            min-width: 0 !important;

            height: 45px !important;
            min-height: 45px !important;
            flex: 0 0 45px !important;

            display: flex !important;
            align-items: center !important;

            gap: 12px !important;
            padding: 11px 13px !important;

            border: 1px solid transparent !important;
            border-radius: 11px !important;

            color: #cbdaf2 !important;
            text-decoration: none !important;

            font-size: 13px !important;
            font-weight: 500 !important;
            line-height: 1.3 !important;

            transition:
                background .2s ease,
                color .2s ease,
                border-color .2s ease !important;
        }

        .app-layout .menu-icon {
            width: 21px !important;
            min-width: 21px !important;
            height: 21px !important;
            flex: 0 0 21px !important;

            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;

            color: #91b8ed !important;
            font-size: 16px !important;
            line-height: 1 !important;
        }

        .app-layout .menu-label {
            min-width: 0;
        }

        .app-layout .menu a:hover {
            color: #fff !important;
            background: rgba(119,169,255,.13) !important;
            border-color: rgba(147,197,253,.12) !important;
        }

        .app-layout .menu a.active {
            color: #fff !important;
            font-weight: 700 !important;

            background: linear-gradient(
                135deg,
                var(--blue-500),
                var(--blue-600)
            ) !important;

            border-color: rgba(255,255,255,.18) !important;
            box-shadow: 0 6px 16px rgba(12,91,207,.28) !important;
        }

        .app-layout .menu a.active .menu-icon {
            color: #fff !important;
        }

        /* ========================================
           STATUS SISTEM
        ======================================== */

        .app-layout .sidebar-status {
            position: relative !important;
            top: auto !important;
            right: auto !important;
            bottom: auto !important;
            left: auto !important;

            width: 100% !important;
            flex: 0 0 auto !important;

            margin-top: auto !important;
            padding: 14px !important;

            border: 1px solid rgba(255,255,255,.12) !important;
            border-radius: 12px !important;

            background: rgba(255,255,255,.07) !important;
        }

        .app-layout .status-online {
            display: flex !important;
            align-items: center !important;
            flex: 0 0 auto !important;

            gap: 8px !important;
            margin-bottom: 7px !important;

            color: #fff !important;
            font-size: 11px !important;
            font-weight: 700 !important;
        }

        .app-layout .online-dot {
            width: 8px !important;
            height: 8px !important;
            min-width: 8px !important;
            flex: 0 0 8px !important;

            border-radius: 50% !important;
            background: #34d399 !important;

            box-shadow: 0 0 9px rgba(52,211,153,.45);
        }

        .app-layout .sidebar-status p {
            color: #b8cbed !important;
            font-size: 9px !important;
            line-height: 1.6 !important;
        }

        .app-layout .sidebar-last-update {
            margin-top: 6px !important;
        }

        /* ========================================
           KONTEN UTAMA
        ======================================== */

        .app-layout > .app-content {
            display: block !important;

            width: calc(100% - var(--sidebar-width)) !important;
            min-width: 0 !important;
            min-height: 100vh !important;

            margin-left: var(--sidebar-width) !important;
            padding: 28px !important;

            box-sizing: border-box !important;
        }

        /*
         * Mencegah margin tambahan dari pembungkus
         * konten yang memakai class content atau main.
         */

        .app-layout .app-content > .content,
        .app-layout .app-content > .main {
            width: 100% !important;
            max-width: 100% !important;

            margin-left: 0 !important;
            padding: 0 !important;
        }

        /* ========================================
           KOMPONEN KARTU UMUM
        ======================================== */

        .app-layout .app-content .card,
        .app-layout .app-content .metric-card,
        .app-layout .app-content .stat-card {
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--card-bg);
            box-shadow: 0 4px 16px rgba(26,48,86,.04);
        }

        /* ========================================
           TABLET
        ======================================== */

        @media (min-width: 651px) and (max-width: 900px) {
            :root {
                --sidebar-width: 220px;
            }

            .app-layout > .app-content {
                padding: 18px !important;
            }
        }

        /* ========================================
           PONSEL
        ======================================== */

        @media (max-width: 650px) {
            .app-layout > .sidebar {
                position: relative !important;
                top: auto !important;
                left: auto !important;
                bottom: auto !important;

                width: 100% !important;
                height: auto !important;
                min-height: 0 !important;

                overflow: visible !important;
                padding: 18px 16px !important;
            }

            .app-layout .brand {
                padding-bottom: 20px !important;
            }

            .app-layout .sidebar-status {
                margin-top: 20px !important;
            }

            .app-layout > .app-content {
                width: 100% !important;
                min-height: auto !important;

                margin-left: 0 !important;
                padding: 16px !important;
            }
        }
    

        /* ===== NETMONITOR GLOBAL: WAVE TECHNOLOGY ===== */

        html {
            min-height: 100%;
            background: #07152d;
        }

        body {
            min-height: 100vh;
            position: relative;
            isolation: isolate;
            overflow-x: hidden;
            color: var(--text-main);
            background:
                radial-gradient(ellipse at 12% 8%, rgba(31,112,235,.30), transparent 36rem),
                radial-gradient(ellipse at 88% 25%, rgba(0,190,255,.15), transparent 32rem),
                linear-gradient(135deg, #061126 0%, #0a1c3b 45%, #102d59 75%, #07152d 100%) !important;
            background-attachment: fixed !important;
        }

        /* Gelombang dekoratif, selalu di belakang konten dan tidak menghalangi klik */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background:
                radial-gradient(ellipse at 14% 78%, transparent 32%, rgba(45,139,255,.12) 32.2%, transparent 32.7%),
                radial-gradient(ellipse at 14% 78%, transparent 40%, rgba(45,139,255,.08) 40.2%, transparent 40.7%),
                radial-gradient(ellipse at 88% 18%, transparent 27%, rgba(0,195,255,.10) 27.2%, transparent 27.7%),
                radial-gradient(ellipse at 88% 18%, transparent 35%, rgba(0,195,255,.07) 35.2%, transparent 35.7%);
        }

        .app-layout > .app-content {
            position: relative;
            z-index: 1;
            min-width: 0;
            background: transparent !important;
            color: var(--text-main);
        }

        .app-layout > .sidebar {
            background:
                radial-gradient(ellipse at 50% 100%, rgba(0,117,255,.22), transparent 38%),
                linear-gradient(180deg, #061632 0%, #0b2148 55%, #10366c 100%) !important;
            border-right: 1px solid rgba(255,255,255,.12) !important;
            box-shadow: 12px 0 36px rgba(5,26,63,.16) !important;
        }

        .app-layout .menu a {
            border-radius: 12px !important;
            transition: transform .2s ease, background .2s ease, box-shadow .2s ease !important;
        }

        .app-layout .menu a:hover { transform: translateX(3px); }

        .app-layout .menu a.active {
            background: linear-gradient(110deg, #2487ff, #075be0) !important;
            box-shadow: 0 8px 22px rgba(0,103,255,.32) !important;
        }

        .app-layout .sidebar-status {
            background: linear-gradient(145deg, rgba(41,117,219,.23), rgba(255,255,255,.055)) !important;
            border-color: rgba(143,190,255,.24) !important;
            box-shadow: inset 0 1px rgba(255,255,255,.08);
        }

        /* Efek kartu global tanpa memaksa warna teks atau background kartu */
        .app-layout .app-content .panel,
        .app-layout .app-content .card,
        .app-layout .app-content .metric-card,
        .app-layout .app-content .stat-card,
        .app-layout .app-content .filter-card,
        .app-layout .app-content .summary-card,
        .app-layout .app-content .table-card,
        .app-layout .app-content .content-card,
        .app-layout .app-content .notification,
        .app-layout .app-content .form-card,
        .app-layout .app-content .data-card,
        .app-layout .app-content .chart-card,
        .app-layout .app-content .school-card {
            border-radius: 16px;
            box-shadow: 0 12px 32px rgba(0,12,35,.12), inset 0 1px 0 rgba(255,255,255,.50);
            transition: border-color .22s ease, box-shadow .22s ease;
        }

        .app-layout .app-content .panel:hover,
        .app-layout .app-content .card:hover,
        .app-layout .app-content .filter-card:hover,
        .app-layout .app-content .summary-card:hover,
        .app-layout .app-content .table-card:hover,
        .app-layout .app-content .content-card:hover {
            border-color: rgba(89,157,255,.42) !important;
            box-shadow: 0 16px 36px rgba(0,20,55,.18) !important;
        }

        .app-layout .app-content button,
        .app-layout .app-content .btn {
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }

        .app-layout .app-content button:hover,
        .app-layout .app-content .btn:hover {
            filter: brightness(1.03);
        }

        /* Jaga keterbacaan judul di atas latar gelap */
        .app-layout .app-content h1,
        .app-layout .app-content h2,
        .app-layout .app-content h3,
        .app-layout .app-content h4 {
            color: #102f60;
        }

        .app-layout .app-content p,
        .app-layout .app-content label {
            color: #536f98;
        }

        @media (max-width: 900px) {
            .app-layout > .app-content { padding: 22px !important; }
        }

        @media (max-width: 600px) {
            .app-layout > .app-content { padding: 14px !important; }
            .app-layout .app-content .panel,
            .app-layout .app-content .card,
            .app-layout .app-content .table-card,
            .app-layout .app-content .form-card { border-radius: 12px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .app-layout *,
            .app-layout *::before,
            .app-layout *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }



        /* =========================================
           NETMONITOR: PERBAIKAN KONTRAS TEKS GLOBAL
           Berlaku pada Dashboard hingga Pengaturan
           Tanpa mengubah route, data, atau logika Blade
        ========================================= */

        .app-layout > .app-content {
            color: #17345f !important;
        }

        /* Judul utama halaman */
        .app-layout > .app-content h1,
        .app-layout > .app-content .page-header h1,
        .app-layout > .app-content .dashboard-header h1,
        .app-layout > .app-content .dashboard-title {
            color: #10346b !important;
            opacity: 1 !important;
            visibility: visible !important;
            text-shadow: none !important;
            font-weight: 800 !important;
        }

        /* Judul panel, kartu, dan bagian */
        .app-layout > .app-content h2,
        .app-layout > .app-content h3,
        .app-layout > .app-content h4,
        .app-layout > .app-content .card-title,
        .app-layout > .app-content .panel-title {
            color: #173d73 !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Teks deskripsi dan label umum */
        .app-layout > .app-content p,
        .app-layout > .app-content .description,
        .app-layout > .app-content .subtitle,
        .app-layout > .app-content .page-description {
            color: #526b91;
            opacity: 1;
        }

        .app-layout > .app-content label {
            color: #244875 !important;
            opacity: 1 !important;
            font-weight: 600;
        }

        /* Formulir */
        .app-layout > .app-content input,
        .app-layout > .app-content select,
        .app-layout > .app-content textarea {
            color: #17345f;
        }

        .app-layout > .app-content input::placeholder,
        .app-layout > .app-content textarea::placeholder {
            color: #8495af !important;
            opacity: 1 !important;
        }

        .app-layout > .app-content input:focus,
        .app-layout > .app-content select:focus,
        .app-layout > .app-content textarea:focus {
            border-color: #287cff !important;
            box-shadow: 0 0 0 3px rgba(40, 124, 255, .15) !important;
            outline: none;
        }

        /* Tabel: heading dan isi */
        .app-layout > .app-content table thead th {
            color: #355984;
            font-weight: 700;
        }

        .app-layout > .app-content table tbody td {
            color: #29466e;
        }

        /* Angka utama pada kartu statistik */
        .app-layout > .app-content .metric-card h2,
        .app-layout > .app-content .metric-card h3,
        .app-layout > .app-content .stat-card h2,
        .app-layout > .app-content .stat-card h3 {
            color: #12366b !important;
            opacity: 1 !important;
        }

        /* Jaga keterbacaan pada layar kecil */
        @media (max-width: 650px) {
            .app-layout > .app-content h1 {
                line-height: 1.3;
                overflow-wrap: anywhere;
            }

            .app-layout > .app-content {
                color: #17345f !important;
            }
        }


        
/* Perbaikan kontras judul dashboard dan semua halaman */
html body .app-layout > .app-content h1,
html body .app-layout > .app-content .dashboard-header h1,
html body .app-layout > .app-content .page-header h1,
html body .app-layout > .app-content .dashboard-title {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    opacity: 1 !important;
    visibility: visible !important;
    text-shadow: 0 2px 8px rgba(0, 25, 70, 0.45) !important;
}

/* Label sambutan dan deskripsi tetap kontras */
html body .app-layout > .app-content .dashboard-header,
html body .app-layout > .app-content .page-header {
    color: #eaf3ff;
}

html body .app-layout > .app-content .dashboard-header p,
html body .app-layout > .app-content .page-header p {
    color: #eaf3ff !important;
    opacity: 1 !important;
}

    </style>

    @yield('styles')
</head>

<body>
    <div class="app-layout">
        @include('layouts.sidebar')

        <main class="app-content">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
