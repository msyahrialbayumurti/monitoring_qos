@extends('layouts.app')

@section('title', 'Dashboard Monitoring QoS')

@section('styles')
<style>
* {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f4f7fb;
            color: #1f2937;
        }

        

        /* =========================
           SIDEBAR
        ========================== */

        

        

        

        

        

        

        

        

        .menu a:hover,
        .menu a.active {
            background: #1266d8;
            color: white;
        }

        

        

        

        

        .sidebar-status p {
            font-size: 9px;
            line-height: 1.5;
            color: #b8cbed;
        }


        /* =========================
           MAIN
        ========================== */

        


        /* =========================
           HEADER
        ========================== */

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }

        .page-title h1 {
            font-size: 24px;
            color: #10295c;
            margin-bottom: 5px;
        }

        .page-title p {
            font-size: 12px;
            color: #718096;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .date-box,
        .school-select {
            background: white;
            border: 1px solid #e1e7ef;
            border-radius: 8px;
            padding: 9px 13px;
            font-size: 11px;
            color: #4a5568;
        }

        .school-select {
            min-width: 200px;
        }

        .school-select select {
            border: none;
            outline: none;
            background: transparent;
            font-size: 11px;
            color: #4a5568;
            cursor: pointer;
        }

        .school-select-static {
            padding: 9px 13px;
            border: 1px solid #d7e6fb;
            border-radius: 8px;
            background: #f5f9ff;
            color: #5b7397;
            font-size: 11px;
            white-space: nowrap;
        }

        .school-select-static strong {
            color: #17396f;
            font-weight: 750;
        }

        .school-info-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            padding: 16px 18px;
            margin-bottom: 18px;
            border: 1px solid #dde9fa;
            border-radius: 14px;
            background:
                radial-gradient(circle at 100% 0, rgba(70, 143, 255, .10), transparent 32%),
                linear-gradient(120deg, #ffffff, #f7faff);
            box-shadow: 0 8px 22px rgba(31, 76, 145, .05);
        }

        .school-info-main {
            flex: 1;
            min-width: 230px;
        }

        .school-info-title {
            margin-bottom: 9px;
            color: #17396f;
            font-size: 13px;
            font-weight: 800;
        }

        .school-info-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .school-info-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border: 1px solid #e0ebfa;
            border-radius: 8px;
            background: #f8fbff;
            color: #6c84a6;
            font-size: 10px;
        }

        .school-info-chip strong {
            color: #264a7d;
            font-weight: 750;
        }

        .school-test-btn {
            border: 0;
            border-radius: 9px;
            padding: 10px 16px;
            color: #fff;
            background: linear-gradient(135deg, #3284ff, #1266d8);
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: 0 7px 16px rgba(18, 102, 216, .22);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .school-test-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(18, 102, 216, .28);
        }

        .school-test-btn:disabled {
            opacity: .7;
            cursor: not-allowed;
            transform: none;
        }

        .school-agent-note {
            display: inline-flex;
            flex-direction: column;
            gap: 3px;
            padding: 9px 13px;
            border: 1px solid #d8e8d9;
            border-radius: 9px;
            background: #f2fbf4;
            color: #2f6b45;
            font-size: 10px;
            font-weight: 700;
            text-align: right;
            white-space: nowrap;
        }

        .school-agent-note small {
            color: #729581;
            font-size: 9px;
            font-weight: 600;
        }

        .school-agent-status {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }

        .school-agent-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 9px;
            flex-wrap: wrap;
        }

        .school-agent-actions a.school-test-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }


        /* =========================
           METRICS
        ========================== */

        .metrics {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 13px;
            margin-bottom: 18px;
        }

        .metric-card {
            background: white;
            border: 1px solid #e7ebf1;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,.03);
        }

        .metric-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .metric-name {
            font-size: 10px;
            font-weight: bold;
            color: #718096;
            text-transform: uppercase;
        }

        .metric-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            background: #edf5ff;
            color: #1266d8;
        }

        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #253858;
        }

        .metric-unit {
            font-size: 11px;
            color: #718096;
        }

        .metric-bottom {
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
        }

        .trend {
            color: #64748b;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-good {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-wait {
            background: #f1f5f9;
            color: #64748b;
        }

        .badge-good { background: #dcfce7; color: #15803d; }
        .badge-medium { background: #fef3c7; color: #b45309; }
        .badge-bad { background: #fee2e2; color: #b91c1c; }
        .qos-score { margin-top: 4px; font-size: 9px; color: #64748b; }


        /* =========================
           CONTENT
        ========================== */

        .content-grid {
            display: grid;
            grid-template-columns: 1.65fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .panel {
            background: white;
            border: 1px solid #e7ebf1;
            border-radius: 10px;
            padding: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,.03);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .panel-title {
            font-size: 13px;
            font-weight: bold;
            color: #243b64;
        }

        .panel-subtitle {
            font-size: 9px;
            color: #8a94a6;
        }


        /* =========================
           CHART
        ========================== */

        .chart {
            height: 250px;
            position: relative;
            border-left: 1px solid #dce2ea;
            border-bottom: 1px solid #dce2ea;
            margin: 8px 10px 5px 30px;
            background:
                linear-gradient(
                    to bottom,
                    transparent 24%,
                    #edf1f6 25%,
                    transparent 26%
                ),
                linear-gradient(
                    to bottom,
                    transparent 49%,
                    #edf1f6 50%,
                    transparent 51%
                ),
                linear-gradient(
                    to bottom,
                    transparent 74%,
                    #edf1f6 75%,
                    transparent 76%
                );
        }

        .chart svg {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
        }

        .chart-label {
            position: absolute;
            left: -28px;
            font-size: 8px;
            color: #9aa5b5;
        }

        .chart-label.one {
            top: -4px;
        }

        .chart-label.two {
            top: 118px;
        }

        .chart-label.three {
            bottom: -4px;
        }

        .chart-times {
            display: flex;
            justify-content: space-between;
            margin-left: 30px;
            margin-right: 5px;
            font-size: 8px;
            color: #8a94a6;
        }

        .legend {
            display: flex;
            gap: 15px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 9px;
            color: #667085;
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #1266d8;
        }

        .legend-dot.delay {
            background: #16a34a;
        }

        .legend-dot.jitter {
            background: #f59e0b;
        }

        .legend-dot.loss {
            background: #ef4444;
        }

        .legend-dot.ping {
            background: #8b5cf6;
        }


        /* =========================
           SCHOOL TABLE
        ========================== */

        .school-table {
            width: 100%;
            border-collapse: collapse;
        }

        .school-table th {
            text-align: left;
            font-size: 9px;
            color: #8a94a6;
            padding: 9px 5px;
            border-bottom: 1px solid #edf0f4;
        }

        .school-table td {
            font-size: 10px;
            padding: 12px 5px;
            border-bottom: 1px solid #f0f2f5;
        }

        .school-name {
            font-weight: bold;
            color: #334155;
        }

        .school-icon {
            width: 27px;
            height: 27px;
            border-radius: 7px;
            background: #e8f1ff;
            color: #1266d8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 7px;
            font-size: 10px;
            font-weight: bold;
        }

        .school-cell {
            display: flex;
            align-items: center;
        }

        .score {
            font-weight: bold;
        }


        /* =========================
           RCA
        ========================== */

        .rca-item {
            display: grid;
            grid-template-columns: 1fr 1.4fr 55px;
            gap: 10px;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #edf0f4;
        }

        .rca-school {
            font-size: 10px;
            font-weight: bold;
            color: #334155;
        }

        .rca-cause {
            font-size: 9px;
            color: #64748b;
            line-height: 1.4;
        }

        .confidence {
            text-align: right;
            font-size: 11px;
            font-weight: bold;
            color: #94a3b8;
        }


        /* =========================
           DSS
        ========================== */

        .dss-item {
            display: flex;
            gap: 11px;
            padding: 12px 0;
            border-bottom: 1px solid #edf0f4;
        }

        .dss-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #ecfdf5;
            color: #16a34a;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .dss-content strong {
            display: block;
            font-size: 10px;
            margin-bottom: 4px;
            color: #334155;
        }

        .dss-content p {
            font-size: 9px;
            color: #64748b;
            line-height: 1.4;
        }


        /* =========================
           NOTIFICATION
        ========================== */

        .notification {
            margin-top: 16px;
            background: white;
            border: 1px solid #e7ebf1;
            border-radius: 10px;
            padding: 13px 16px;
        }

        .notification-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .notification-item {
            display: flex;
            align-items: center;
            gap: 9px;
            border: 1px solid #edf0f4;
            border-radius: 7px;
            padding: 10px;
        }

        .notification-icon {
            width: 27px;
            height: 27px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff7ed;
            color: #f97316;
            flex-shrink: 0;
        }

        .notification-text {
            font-size: 9px;
            color: #475569;
            line-height: 1.4;
        }

        .notification-time {
            display: block;
            color: #9ca3af;
            margin-top: 2px;
            font-size: 8px;
        }


        /* =========================
           FOOTER
        ========================== */

        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 9px;
            margin-top: 18px;
            padding-bottom: 5px;
        }


        /* =========================
           RESPONSIVE
        ========================== */


        /* =========================
           POLISH UI + ANIMASI
           Tidak mengubah query atau logika Blade.
        ========================== */
        :root {
            --ui-primary: #1765dc;
            --ui-primary-dark: #1049a5;
            --ui-border: #e5ebf4;
            --ui-shadow: 0 8px 24px rgba(24, 49, 89, .055);
        }

        body {
            background: radial-gradient(circle at 10% 0%, #edf4ff 0, transparent 32%), #f4f7fb;
        }

        .topbar { gap: 16px; flex-wrap: wrap; }
        .page-title h1 { letter-spacing: -.55px; font-weight: 800; }
        .date-box, .school-select { box-shadow: 0 3px 10px rgba(24,49,89,.035); }
        .date-box { white-space: nowrap; }
        .school-select select { max-width: 100%; }

        .metric-card, .panel, .notification,
        .top-actions > *, .school-select, .date-box {
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }
        .metric-card {
            position: relative;
            overflow: hidden;
            border-radius: 14px;
            border-color: #e4eaf4;
            box-shadow: var(--ui-shadow);
            animation: dashboard-enter .45s both;
        }
        .metric-card::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 3px;
            background: linear-gradient(180deg, #3288ff, #1765dc);
            opacity: .0;
            transition: opacity .2s ease;
        }
        .metric-card:hover {
            transform: translateY(-4px);
            border-color: #c9dcfb;
            box-shadow: 0 14px 28px rgba(23,101,220,.11);
        }
        .metric-card:hover::before { opacity: 1; }
        .metric-card:nth-child(2) { animation-delay: .04s; }
        .metric-card:nth-child(3) { animation-delay: .08s; }
        .metric-card:nth-child(4) { animation-delay: .12s; }
        .metric-card:nth-child(5) { animation-delay: .16s; }
        .metric-icon { transition: transform .25s ease, background .25s ease; }
        .metric-card:hover .metric-icon { transform: scale(1.08) rotate(-4deg); background: #dceaff; }
        .metric-value { letter-spacing: -.7px; }

        .panel, .notification {
            border-radius: 14px;
            border-color: #e5ebf4;
            box-shadow: var(--ui-shadow);
        }
        .panel { animation: dashboard-enter .5s both; }
        .panel-header { gap: 12px; }
        .panel-title { font-size: 13px; letter-spacing: -.15px; }
        .panel-subtitle { line-height: 1.5; }

        .chart {
            height: 260px;
            overflow: hidden;
            border-radius: 8px 8px 0 0;
            border-left-color: #d7e1ef;
            border-bottom-color: #d7e1ef;
            background-color: #fbfdff;
            background-image: linear-gradient(to bottom, rgba(219,230,244,.65) 1px, transparent 1px);
            background-size: 100% 25%;
        }
        .chart svg { overflow: visible; }
        .qos-chart-tooltip {
            position: absolute; z-index: 20; display: none; pointer-events: none;
            min-width: 165px; max-width: 230px; padding: 10px 12px;
            color: #eaf2ff; background: rgba(8, 21, 47, .96);
            border: 1px solid rgba(255,255,255,.16); border-radius: 9px;
            box-shadow: 0 10px 28px rgba(8,21,47,.22); font-size: 11px;
            line-height: 1.55;
        }
        .qos-chart-tooltip strong { display:block; color:#fff; font-size:12px; margin-bottom:3px; }
        .qos-data-point { cursor: crosshair; fill: #fff; stroke-width: 2.5; }
        .qos-data-point:hover, .qos-data-point:focus { r: 6; stroke-width: 3.5; outline: none; }
        .chart polyline {
            stroke-linecap: round;
            stroke-linejoin: round;
            vector-effect: non-scaling-stroke;
            filter: drop-shadow(0 2px 2px rgba(20, 50, 90, .10));
            /* Animasi garis digambar saat grafik muncul */
            stroke-dasharray: 1200;
            stroke-dashoffset: 1200;
            animation: qos-line-draw 1.35s ease-out forwards;
        }
        .chart polyline:nth-of-type(2) { animation-delay: .12s; }
        .chart polyline:nth-of-type(3) { animation-delay: .24s; }
        .chart polyline:nth-of-type(4) { animation-delay: .36s; }
        @keyframes qos-line-draw {
            to { stroke-dashoffset: 0; }
        }
        .chart-times { gap: 5px; overflow: hidden; }
        .chart-times span { flex: 1; text-align: center; white-space: nowrap; }
        .legend { gap: 13px; }
        .legend-item { font-size: 10px; }
        .legend-dot { box-shadow: 0 0 0 3px rgba(18,102,216,.08); }
        .legend-dot.delay { box-shadow: 0 0 0 3px rgba(22,163,74,.08); }
        .legend-dot.jitter { box-shadow: 0 0 0 3px rgba(245,158,11,.10); }
        .legend-dot.loss { box-shadow: 0 0 0 3px rgba(239,68,68,.08); }
        .legend-dot.ping { box-shadow: 0 0 0 3px rgba(139,92,246,.09); }

        .school-table th { background: #f8faff; }
        .school-table tbody tr { transition: background .18s ease; }
        .school-table tbody tr:hover { background: #f7faff; }
        .school-icon { flex-shrink: 0; box-shadow: 0 2px 5px rgba(18,102,216,.08); }
        .badge { display: inline-block; letter-spacing: .2px; }
        .rca-item, .dss-item { transition: background .18s ease, padding .18s ease; }
        .rca-item:hover, .dss-item:hover { background: #fbfdff; }
        .notification-item { transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease; }
        .notification-item:hover { transform: translateY(-2px); border-color: #d5e2f4; box-shadow: 0 5px 14px rgba(24,49,89,.05); }
        button[type="submit"] { box-shadow: 0 4px 10px rgba(18,102,216,.18); }
        button[type="submit"]:hover:not(:disabled) { transform: translateY(-1px); background: #0f56bd !important; box-shadow: 0 7px 15px rgba(18,102,216,.24); }
        button[type="submit"]:active:not(:disabled) { transform: translateY(0); }
        select:focus-visible, button:focus-visible, a:focus-visible { outline: 3px solid rgba(50,136,255,.35); outline-offset: 2px; }

        @keyframes dashboard-enter {
            from { opacity: 0; transform: translateY(9px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1100px) {
            .metrics { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .content-grid { grid-template-columns: minmax(0, 1fr); }
        }
        @media (max-width: 760px) {
            .metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
            .bottom-grid { grid-template-columns: minmax(0, 1fr); }
            .notification-list { grid-template-columns: minmax(0, 1fr); }
            .topbar { align-items: flex-start; }
            .page-title h1 { font-size: 21px; }
            .top-actions { flex-wrap: wrap; width: 100%; }
            .school-select { min-width: 0; max-width: 100%; }
            .panel { padding: 13px; }
            .chart { height: 220px; margin-left: 27px; }
            .chart-times { margin-left: 27px; }
            .rca-item { grid-template-columns: 1fr; gap: 6px; }
            .confidence { text-align: left; }
        }
        @media (max-width: 420px) {
            .metrics { grid-template-columns: minmax(0, 1fr); }
            .metric-card { padding: 14px; }
            .date-box, .school-select { font-size: 10px; padding: 8px 10px; }
            .chart { height: 190px; }
            .legend { gap: 9px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; transition-duration: .01ms !important; }
            .chart polyline { stroke-dasharray: none; stroke-dashoffset: 0; }
        }


        /* Kontrol grafik tambahan tanpa mengubah data dan animasi yang sudah berjalan */
        .qos-chart-actions { display:flex; align-items:center; gap:9px; flex-wrap:wrap; }
        .qos-replay { border:1px solid #cfe0f8; background:#eff6ff; color:#1554a4; border-radius:9px; padding:8px 12px; font-size:11px; font-weight:700; cursor:pointer; transition:transform .2s, background .2s; }
        .qos-replay:hover { background:#dbeafe; transform:translateY(-2px); }
        .qos-replay:focus-visible { outline:3px solid #93c5fd; outline-offset:2px; }
        .chart .qos-data-point { fill:white; stroke-width:2.8; vector-effect:non-scaling-stroke; cursor:help; transition:r .18s ease, fill .18s ease; }
        .chart .qos-data-point:hover { r:7; fill:#eaf3ff; }
        .qos-chart-hint { font-size:10px; color:#64748b; margin-top:10px; }
        @media (prefers-reduced-motion: reduce) { .qos-replay { transition:none; } .chart .qos-data-point { transition:none; } }
        
        /* NETMONITOR visual refresh: clean, compact, animated, responsive */
        :root {
            --net-primary: #1765dc;
            --net-ink: #183153;
            --net-muted: #72829b;
            --net-line: #e4ebf5;
            --net-shadow: 0 8px 24px rgba(24, 49, 89, .07);
        }
        body {
            color: var(--net-ink);
            background: radial-gradient(circle at 12% 0%, #eaf2ff 0, transparent 30%), #f4f7fc;
        }
        .topbar { gap: 16px; }
        .page-title h1 { font-weight: 800; letter-spacing: -.65px; }
        .page-title p { line-height: 1.6; }
        .date-box, .school-select {
            border-color: var(--net-line);
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(24,49,89,.04);
        }
        .metrics { gap: 12px; }
        .metric-card, .panel, .notification {
            border: 1px solid var(--net-line);
            border-radius: 14px;
            box-shadow: var(--net-shadow);
            background: rgba(255,255,255,.97);
        }
        .metric-card {
            padding: 15px;
            animation: net-card-in .48s both;
        }
        .metric-card:nth-child(2) { animation-delay: .05s; }
        .metric-card:nth-child(3) { animation-delay: .10s; }
        .metric-card:nth-child(4) { animation-delay: .15s; }
        .metric-card:nth-child(5) { animation-delay: .20s; }
        .metric-card:hover, .panel:hover, .notification:hover {
            border-color: #cfdef4;
            box-shadow: 0 12px 30px rgba(24,49,89,.09);
        }
        .metric-card { transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .metric-card:hover { transform: translateY(-3px); }
        .metric-icon { box-shadow: 0 0 0 4px #f3f7ff; }
        .metric-value { color: #1e3558; }
        .panel { padding: 16px; animation: net-card-in .5s both; }
        .panel-title { color: #203b65; font-size: 13px; font-weight: 800; }
        .panel-subtitle { margin-top: 2px; }
        .school-table th { background: #f6f9ff; }
        .school-table tbody tr { transition: background .18s ease; }
        .school-table tbody tr:hover { background: #f5f9ff; }
        .school-icon { box-shadow: 0 2px 7px rgba(23,101,220,.10); }
        .badge { letter-spacing: .15px; }
        .qos-chart-actions { display:flex; align-items:center; gap:9px; }
        .qos-replay {
            border: 1px solid #cfe0f8; background: #eff6ff; color: #1554a4;
            border-radius: 9px; padding: 8px 12px; font-size: 11px;
            font-weight: 700; cursor: pointer; transition: transform .2s, background .2s;
        }
        .qos-replay:hover { background: #dbeafe; transform: translateY(-1px); }
        .qos-latest-chart {
            height: 260px; overflow: hidden; border: 1px solid #e3ebf7;
            border-radius: 11px; background: #fbfdff;
            box-shadow: inset 0 1px 3px rgba(25,55,100,.025);
        }
        .qos-latest-chart svg { overflow: hidden; }
        .qos-latest-chart .qos-latest-point {
            filter: drop-shadow(0 2px 3px rgba(25,55,100,.16));
            transition: r .2s ease, filter .2s ease;
            animation: qos-point-enter .5s ease-out both;
        }
        .qos-latest-chart .qos-latest-point:hover,
        .qos-latest-chart .qos-latest-point:focus {
            r: 8; filter: drop-shadow(0 3px 6px rgba(25,55,100,.24));
        }
        .qos-chart-hint { line-height: 1.55; }
        .legend { gap: 13px; }
        .legend-item { font-size: 10px; }
        .legend-dot { box-shadow: 0 0 0 3px rgba(18,102,216,.08); }
        .legend-dot.delay { box-shadow: 0 0 0 3px rgba(22,163,74,.08); }
        .legend-dot.jitter { box-shadow: 0 0 0 3px rgba(245,158,11,.10); }
        .legend-dot.loss { box-shadow: 0 0 0 3px rgba(239,68,56,.08); }
        .legend-dot.ping { box-shadow: 0 0 0 3px rgba(139,92,246,.09); }
        .rca-item, .dss-item, .notification-item {
            transition: background .18s ease, transform .18s ease, border-color .18s ease;
        }
        .rca-item:hover, .dss-item:hover { background: #f8fbff; }
        .notification-item:hover { transform: translateY(-2px); border-color: #d5e2f4; }
        @keyframes net-card-in {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes qos-point-enter {
            from { opacity: .2; r: 2; }
            to { opacity: 1; r: 6; }
        }
        @media (max-width: 760px) {
            .qos-latest-chart { height: 230px; }
            .panel { padding: 13px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .metric-card, .panel, .qos-latest-chart .qos-latest-point {
                animation: none !important; transition: none !important;
            }
        }

        /* ===== Penyempurnaan visual dashboard NETMONITOR ===== */
        .metrics {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
        }
        .metric-card {
            min-width: 0;
            min-height: 132px;
            padding: 15px 14px 13px;
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }
        .metric-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 3px;
            opacity: .95;
            background: var(--metric-color, #1677ff);
        }
        .metric-card::after {
            content: "";
            position: absolute;
            z-index: -1;
            width: 95px;
            height: 95px;
            right: -44px;
            top: -43px;
            border-radius: 50%;
            background: var(--metric-tint, rgba(22,119,255,.07));
            transition: transform .3s ease;
        }
        .metric-card:hover::after { transform: scale(1.35); }
        .metric-card:nth-child(1) { --metric-color:#1677ff; --metric-tint:rgba(22,119,255,.09); }
        .metric-card:nth-child(2) { --metric-color:#12b76a; --metric-tint:rgba(18,183,106,.09); }
        .metric-card:nth-child(3) { --metric-color:#f59e0b; --metric-tint:rgba(245,158,11,.11); }
        .metric-card:nth-child(4) { --metric-color:#f04438; --metric-tint:rgba(240,68,56,.08); }
        .metric-card:nth-child(5) { --metric-color:#8b5cf6; --metric-tint:rgba(139,92,246,.09); }
        .metric-card .metric-icon {
            color: var(--metric-color, #1677ff);
            background: var(--metric-tint, #edf5ff);
            box-shadow: 0 0 0 4px rgba(242,247,255,.9);
        }
        .metric-name { letter-spacing: .45px; }
        .metric-value { font-size: clamp(21px, 1.6vw, 26px); line-height: 1.15; }
        .metric-bottom { position: relative; z-index: 1; }
        .metric-bottom .badge { border-radius: 6px; }
        .panel {
            min-width: 0;
            border-radius: 15px;
            border-color: #e3eaf5;
            box-shadow: 0 8px 24px rgba(24,49,89,.055);
        }
        .panel-header { min-height: 28px; }
        .panel-title { letter-spacing: -.18px; }
        .qos-latest-chart {
            background: linear-gradient(145deg, #f9fbff, #ffffff);
            border-color: #e0e9f7;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9), 0 3px 10px rgba(25,55,100,.025);
        }
        .qos-latest-chart .qos-latest-point {
            transform-box: fill-box;
            transform-origin: center;
            animation: qos-point-pulse 2.8s ease-in-out infinite;
        }
        .qos-latest-chart .qos-latest-point:nth-of-type(2) { animation-delay: .15s; }
        .qos-latest-chart .qos-latest-point:nth-of-type(3) { animation-delay: .3s; }
        .qos-latest-chart .qos-latest-point:nth-of-type(4) { animation-delay: .45s; }
        .qos-latest-chart .qos-latest-point:nth-of-type(5) { animation-delay: .6s; }
        .qos-chart-hint {
            padding: 0 2px;
            color: #6b7c98;
            font-size: 10px;
        }
        .legend-item { gap: 6px; }
        .legend-dot { width: 8px; height: 8px; flex: 0 0 8px; }
        .school-table { border-collapse: separate; border-spacing: 0; }
        .school-table th:first-child { border-radius: 7px 0 0 0; }
        .school-table th:last-child { border-radius: 0 7px 0 0; }
        .school-table tbody tr:last-child td { border-bottom-color: transparent; }
        .rca-item, .dss-item { padding-top: 13px; padding-bottom: 13px; }
        .notification { border-radius: 13px; }
        .notification-item { background: #fff; }
        .qos-replay { box-shadow: 0 3px 9px rgba(22,101,220,.08); }
        .qos-replay:active { transform: scale(.98); }
        @keyframes qos-point-pulse {
            0%, 100% { filter: drop-shadow(0 1px 2px rgba(25,55,100,.12)); }
            50% { filter: drop-shadow(0 2px 6px rgba(25,55,100,.26)); }
        }
        @media (max-width: 1100px) {
            .metrics { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (max-width: 760px) {
            .metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .metric-card { min-height: 125px; }
        }
        @media (max-width: 420px) {
            .metrics { grid-template-columns: minmax(0, 1fr); }
            .metric-card { min-height: 112px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .qos-latest-chart .qos-latest-point { animation: none !important; }
        }


        /* QoS card polish: single-value pulse indicators, not historical sparklines */
        .metrics { align-items: stretch; }
        .metric-card {
            min-width: 0;
            padding: 16px 15px 13px;
            border-radius: 15px;
            isolation: isolate;
        }
        .metric-card::after {
            content: "";
            position: absolute;
            top: -28px;
            right: -24px;
            width: 82px;
            height: 82px;
            border-radius: 50%;
            background: var(--metric-tint, rgba(22,119,255,.09));
            z-index: -1;
            transition: transform .3s ease;
        }
        .metric-card:hover::after { transform: scale(1.16); }
        .metric-card:nth-child(1) { --metric-color:#1677ff; --metric-tint:rgba(22,119,255,.09); }
        .metric-card:nth-child(2) { --metric-color:#12b76a; --metric-tint:rgba(18,183,106,.09); }
        .metric-card:nth-child(3) { --metric-color:#f59e0b; --metric-tint:rgba(245,158,11,.12); }
        .metric-card:nth-child(4) { --metric-color:#f04438; --metric-tint:rgba(240,68,56,.09); }
        .metric-card:nth-child(5) { --metric-color:#8b5cf6; --metric-tint:rgba(139,92,246,.09); }
        .metric-card::before {
            content:"";
            position:absolute;
            inset:0 auto 0 0;
            width:3px;
            background:var(--metric-color, #1677ff);
            border-radius:15px 0 0 15px;
            opacity:.95;
        }
        .metric-icon {
            color:var(--metric-color, #1677ff);
            background:var(--metric-tint, #edf5ff);
            box-shadow:0 0 0 5px rgba(255,255,255,.65);
        }
        .metric-value { font-size:25px; letter-spacing:-.8px; }
        .metric-unit { font-size:10px; }
        .metric-bottom { margin-top:9px; }
        .metric-pulse {
            display:flex;
            align-items:center;
            gap:7px;
            margin-top:11px;
            height:9px;
        }
        .metric-pulse-track {
            position:relative;
            height:4px;
            flex:1;
            overflow:visible;
            border-radius:99px;
            background:linear-gradient(90deg, var(--metric-tint), #edf2f8);
        }
        .metric-pulse-track::before {
            content:"";
            position:absolute;
            left:0; top:0; bottom:0;
            width:var(--metric-position, 0%);
            border-radius:99px;
            background:var(--metric-color);
            opacity:.48;
        }
        .metric-pulse-dot {
            position:absolute;
            left:var(--metric-position, 0%);
            top:50%;
            width:9px;
            height:9px;
            border:2px solid white;
            border-radius:50%;
            background:var(--metric-color);
            box-shadow:0 0 0 3px var(--metric-tint);
            transform:translate(-50%,-50%);
            animation:metric-pulse 2.1s ease-in-out infinite;
        }
        .metric-pulse-label {
            color:#8a94a6;
            font-size:8px;
            white-space:nowrap;
        }
        @keyframes metric-pulse {
            0%,100% { box-shadow:0 0 0 3px var(--metric-tint); }
            50% { box-shadow:0 0 0 6px var(--metric-tint); }
        }
        .panel { border-radius:15px; }
        .panel-header { margin-bottom:15px; }
        .school-table tbody tr:last-child td { border-bottom:0; }
        .qos-chart-hint { line-height:1.6; }
        @media (max-width:760px) {
            .metric-pulse { margin-top:9px; }
            .metric-value { font-size:23px; }
        }
        @media (prefers-reduced-motion:reduce) {
            .metric-pulse-dot { animation:none; }
            .metric-card, .metric-card::after { transition:none; }
        }



        /* =====================================================
           NETMONITOR V5 — BLUE GLASS / NETWORK WORKSPACE
           CSS visual only; query, routes, metrics, and animations stay intact.
        ====================================================== */
        :root {
            --nm-blue: #0878f9;
            --nm-navy: #092653;
            --nm-ink: #112c59;
            --nm-muted: #5f789f;
            --nm-line: rgba(133, 171, 224, .28);
            --nm-glass: rgba(255,255,255,.86);
        }
        body {
            color: var(--nm-ink);
            background:
                radial-gradient(ellipse at 82% 0%, rgba(61,145,255,.22), transparent 31%),
                radial-gradient(ellipse at 100% 45%, rgba(88,166,255,.15), transparent 34%),
                radial-gradient(ellipse at 48% 100%, rgba(117,180,255,.17), transparent 36%),
                linear-gradient(135deg, #edf5ff 0%, #e8f1ff 42%, #f7faff 100%);
            background-attachment: fixed;
        }
        body::before {
            content:""; position:fixed; z-index:-1; pointer-events:none;
            top:0; right:0; width:min(72vw,1100px); height:250px;
            background:
                radial-gradient(circle at 76% 22%, rgba(255,255,255,.96) 0 2px, transparent 3px),
                radial-gradient(circle at 81% 34%, rgba(255,255,255,.78) 0 2px, transparent 3px),
                radial-gradient(ellipse at 88% 0%, rgba(64,147,255,.19), transparent 53%),
                repeating-radial-gradient(ellipse at 75% -15%, transparent 0 19px, rgba(69,143,239,.075) 20px 21px, transparent 22px 35px);
            opacity:.9;
        }
        .topbar { position:relative; gap:18px; margin-bottom:22px; align-items:center; }
        .page-title h1 { color:#092653; font-size:clamp(23px,2vw,30px); letter-spacing:-.8px; font-weight:850; }
        .page-title p { color:#5d769c; font-size:12px; line-height:1.6; }
        .date-box, .school-select {
            border:1px solid rgba(133,171,224,.35); background:rgba(255,255,255,.82);
            color:#284a7c; border-radius:12px; box-shadow:0 8px 24px rgba(35,87,155,.07);
            backdrop-filter:blur(12px); padding:10px 14px;
        }
        .school-select select { color:#193967; font-weight:700; }
        .metrics { gap:14px; margin-bottom:16px; }
        .metric-card {
            background:linear-gradient(145deg,rgba(255,255,255,.97),rgba(248,251,255,.84));
            border:1px solid rgba(136,174,226,.38); border-radius:17px;
            box-shadow:0 10px 28px rgba(28,78,146,.085), inset 0 1px 0 rgba(255,255,255,.95);
            backdrop-filter:blur(12px); padding:17px 16px 14px;
        }
        .metric-card:hover { transform:translateY(-4px); box-shadow:0 16px 32px rgba(28,91,177,.15); }
        .metric-name { letter-spacing:.55px; color:#60799f; }
        .metric-value { color:#102d5d; font-size:26px; }
        .metric-icon { width:36px; height:36px; font-weight:800; }
        .metric-bottom { color:#6b83a5; }
        .badge-wait { background:#eef4fd; color:#547098; }
        .panel, .notification {
            background:linear-gradient(145deg,rgba(255,255,255,.96),rgba(255,255,255,.83));
            border:1px solid rgba(133,171,224,.36); border-radius:17px;
            box-shadow:0 12px 30px rgba(34,78,135,.075), inset 0 1px 0 rgba(255,255,255,.95);
            backdrop-filter:blur(12px);
        }
        .panel-title { color:#173968; font-size:14px; }
        .panel-subtitle { color:#6e85a8; }
        .chart {
            border:1px solid rgba(135,174,228,.3); border-radius:12px;
            background-color:rgba(248,251,255,.9);
            background-image:
                linear-gradient(to right,rgba(154,185,226,.16) 1px,transparent 1px),
                linear-gradient(to bottom,rgba(154,185,226,.24) 1px,transparent 1px);
            background-size:20% 100%,100% 25%;
            box-shadow:inset 0 1px 8px rgba(35,87,155,.025);
        }
        .qos-chart-tooltip { border:1px solid rgba(137,177,235,.4); box-shadow:0 16px 34px rgba(10,37,79,.24); }
        .qos-replay { border-radius:10px; padding:9px 13px; background:linear-gradient(135deg,#f1f7ff,#e7f1ff); box-shadow:0 4px 12px rgba(26,101,209,.08); }
        .qos-replay:hover { background:#dcecff; }
        .school-table { border-collapse:separate; border-spacing:0; overflow:hidden; }
        .school-table th { background:linear-gradient(90deg,#f0f6ff,#f8fbff); color:#7187a8; }
        .school-table th:first-child { border-radius:8px 0 0 0; }
        .school-table th:last-child { border-radius:0 8px 0 0; }
        .school-table td { border-bottom:1px solid rgba(139,171,216,.2); }
        .school-table tbody tr:hover { background:rgba(231,241,255,.62); }
        .school-icon { box-shadow:0 4px 10px rgba(18,102,216,.13); }
        .rca-item, .dss-item { border-bottom-color:rgba(139,171,216,.24); }
        .rca-item:hover, .dss-item:hover { background:rgba(238,246,255,.65); }
        .dss-icon { box-shadow:0 4px 12px rgba(22,163,74,.09); }
        .notification { margin-top:16px; }
        .notification-item { background:rgba(255,255,255,.7); border-color:rgba(139,171,216,.28); border-radius:11px; }
        .notification-item:hover { background:#fff; border-color:#b8d2f6; box-shadow:0 8px 18px rgba(35,87,155,.09); }
        .footer { color:#7890b2; }
        .qos-chart-hint { padding:9px 11px; border-radius:9px; background:linear-gradient(90deg,rgba(225,240,255,.85),rgba(243,248,255,.6)); color:#56749c; }
        .legend { padding-top:2px; }
        .legend-item { color:#536e95; }
        @media (max-width:1100px) {
            body::before { width:100vw; opacity:.55; }
            .panel, .notification { backdrop-filter:blur(8px); }
        }
        @media (max-width:760px) {
            .topbar { margin-bottom:16px; }
            .metric-card { border-radius:14px; }
            .panel, .notification { border-radius:14px; }
        }
        @media (prefers-reduced-motion:reduce) {
            .metric-card, .panel, .notification, .notification-item { transition:none !important; }
        }

        /* =====================================================
           NETMONITOR DASHBOARD — FINAL VISUAL POLISH
           Hanya CSS presentasi; Blade, route, query, dan JS dipertahankan.
        ====================================================== */
        :root {
            --nm-accent: #0866e8;
            --nm-deep: #0a2857;
            --nm-text: #142f5d;
            --nm-muted: #647da3;
            --nm-border: #dce8f8;
            --nm-card-shadow: 0 12px 32px rgba(29, 78, 145, .075);
        }
        body {
            color: var(--nm-text);
            background:
                radial-gradient(ellipse at 78% 2%, rgba(94, 164, 255, .25), transparent 32%),
                radial-gradient(ellipse at 100% 40%, rgba(107, 178, 255, .13), transparent 33%),
                linear-gradient(135deg, #eef5ff 0%, #eaf2ff 45%, #f7faff 100%);
            background-attachment: fixed;
        }
        .topbar {
            align-items: center;
            gap: 18px;
            margin-bottom: 22px;
        }
        .page-title h1 {
            color: #0b2858;
            font-size: clamp(24px, 2.1vw, 32px);
            line-height: 1.2;
            font-weight: 850;
            letter-spacing: -.9px;
        }
        .page-title h1::before {
            content: 'SELAMAT DATANG DI';
            display: block;
            margin-bottom: 8px;
            color: #6282b3;
            font-size: 10px;
            line-height: 1.4;
            letter-spacing: 1.8px;
            font-weight: 800;
        }
        .page-title p { margin-top: 7px; color: #5d789f; font-size: 12px; line-height: 1.65; }
        .top-actions { gap: 10px; }
        .date-box, .school-select {
            border: 1px solid rgba(130, 169, 224, .35);
            border-radius: 12px;
            background: rgba(255,255,255,.82);
            box-shadow: 0 6px 18px rgba(35, 87, 155, .055);
            backdrop-filter: blur(12px);
            padding: 10px 13px;
        }
        .school-select select { max-width: 250px; color: #183968; font-weight: 700; }
        /* Panel sekolah: mempercantik elemen inline yang sudah ada tanpa mengubah form Tes QoS. */
        .topbar + div[style*="background:white"],
        .topbar + div[style*="background: white"] {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px 20px;
            min-height: 76px;
            padding: 16px 18px !important;
            margin-bottom: 17px !important;
            border: 1px solid rgba(130, 169, 224, .35) !important;
            border-radius: 16px !important;
            background: rgba(255,255,255,.86) !important;
            box-shadow: var(--nm-card-shadow);
            backdrop-filter: blur(12px);
        }
        .topbar + div[style*="background:white"] form,
        .topbar + div[style*="background: white"] form { margin-left: auto !important; }
        .topbar + div[style*="background:white"] button,
        .topbar + div[style*="background: white"] button {
            min-height: 38px;
            padding: 10px 17px !important;
            border-radius: 10px !important;
            background: linear-gradient(135deg, #1680ff, #075bd4) !important;
            box-shadow: 0 7px 16px rgba(8, 102, 232, .2);
        }
        .metrics { gap: 14px; margin-bottom: 17px; align-items: stretch; }
        .metric-card {
            min-width: 0;
            min-height: 142px;
            padding: 17px 15px 14px;
            border: 1px solid rgba(132, 171, 226, .34);
            border-radius: 17px;
            background: linear-gradient(145deg, rgba(255,255,255,.98), rgba(247,250,255,.88));
            box-shadow: var(--nm-card-shadow), inset 0 1px 0 rgba(255,255,255,.95);
            backdrop-filter: blur(10px);
        }
        .metric-card::before { width: 3px; opacity: 1; }
        .metric-name { color: #617da6; letter-spacing: .7px; font-size: 10px; }
        .metric-icon { width: 36px; height: 36px; font-size: 15px; }
        .metric-value { color: #102e60; font-size: clamp(22px, 1.7vw, 27px); line-height: 1.2; letter-spacing: -.8px; }
        .metric-unit { font-weight: 600; color: #7085a5; }
        .metric-bottom { margin-top: 11px; gap: 6px; }
        .metric-pulse { margin-top: 12px; }
        .metric-card:hover { transform: translateY(-3px); border-color: #bfd7fb; box-shadow: 0 16px 34px rgba(28, 91, 177, .13); }
        .content-grid { grid-template-columns: minmax(0, 1.6fr) minmax(330px, 1fr); gap: 16px; align-items: stretch; }
        .panel, .notification {
            min-width: 0;
            padding: 18px;
            border: 1px solid rgba(130, 169, 224, .34);
            border-radius: 17px;
            background: linear-gradient(145deg, rgba(255,255,255,.97), rgba(255,255,255,.86));
            box-shadow: var(--nm-card-shadow), inset 0 1px 0 rgba(255,255,255,.95);
            backdrop-filter: blur(12px);
        }
        .panel-header { gap: 12px; margin-bottom: 16px; }
        .panel-title { color: #153868; font-size: 15px; font-weight: 800; letter-spacing: -.25px; }
        .panel-subtitle { margin-top: 3px; color: #7188aa; font-size: 10px; line-height: 1.5; }
        .qos-latest-chart {
            height: 260px;
            margin: 8px 0 0;
            border: 1px solid #dce8f8;
            border-radius: 13px;
            background: linear-gradient(145deg, #f7faff, #fff);
            box-shadow: inset 0 1px 4px rgba(30, 72, 130, .035);
        }
        .qos-latest-chart svg { overflow: hidden; }
        .qos-chart-hint {
            margin-top: 10px;
            padding: 10px 12px;
            border: 1px solid #e0ecfb;
            border-radius: 10px;
            background: linear-gradient(90deg, rgba(226,240,255,.8), rgba(246,250,255,.8));
            color: #5e789e;
            font-size: 10px;
            line-height: 1.65;
        }
        .qos-chart-actions { gap: 8px; flex-wrap: wrap; }
        .qos-replay { border-radius: 10px; padding: 9px 12px; color: #1458ad; background: #edf5ff; border-color: #d3e4fc; }
        .qos-replay:hover { background: #e0edff; }
        .legend { gap: 12px; }
        .legend-item { color: #59749a; font-size: 10px; }
        .school-table { border-collapse: separate; border-spacing: 0; }
        .school-table th { padding: 11px 8px; background: #f1f6ff; color: #7187a7; font-size: 10px; }
        .school-table th:first-child { border-radius: 8px 0 0 0; }
        .school-table th:last-child { border-radius: 0 8px 0 0; }
        .school-table td { padding: 12px 8px; border-bottom: 1px solid #eaf0f8; color: #34527c; font-size: 10px; }
        .school-table tbody tr { transition: background .18s ease; }
        .school-table tbody tr:hover { background: #f4f8ff; }
        .school-table tbody tr:last-child td { border-bottom: 0; }
        .school-icon { flex: 0 0 auto; border-radius: 9px; }
        .badge { border-radius: 7px; padding: 5px 8px; font-size: 9px; }
        .rca-item, .dss-item { padding: 13px 0; border-bottom-color: #e8eff8; }
        .notification-item { border-radius: 11px; border-color: #e5edf8; background: rgba(255,255,255,.78); }
        .footer { padding-top: 8px; color: #7e94b4; font-size: 10px; }
        @media (max-width: 1200px) {
            .metrics { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .content-grid { grid-template-columns: minmax(0, 1fr); }
        }
        @media (max-width: 760px) {
            .topbar { align-items: flex-start; }
            .page-title h1 { font-size: 23px; }
            .top-actions { width: 100%; flex-wrap: wrap; }
            .school-select { min-width: 0; max-width: 100%; }
            .school-select select { max-width: 100%; }
            .metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
            .metric-card { min-height: 130px; padding: 14px 12px; }
            .panel { padding: 13px; }
            .qos-latest-chart { height: 235px; }
            .school-table { min-width: 410px; }
            .panel:has(.school-table) { overflow-x: auto; }
        }
        @media (max-width: 420px) {
            .metrics { grid-template-columns: minmax(0, 1fr); }
            .metric-card { min-height: 112px; }
            .date-box, .school-select { width: 100%; }
            .topbar + div[style*="background:white"],
            .topbar + div[style*="background: white"] { align-items: stretch; }
            .topbar + div[style*="background:white"] form,
            .topbar + div[style*="background: white"] form { margin-left: 0 !important; }
            .qos-chart-actions { align-items: flex-start; }
        }
        @media (prefers-reduced-motion: reduce) {
            .metric-card, .panel, .notification, .notification-item, .qos-latest-point { animation: none !important; transition: none !important; }
        }

        

/* FIX KONTRAS JUDUL RIWAYAT MONITORING */
.app-content h1,
.app-content .page-header h1,
.app-content .page-title,
.app-content .history-title {
    color: #123b78 !important;
    -webkit-text-fill-color: #123b78 !important;
    text-shadow: none !important;
    opacity: 1 !important;
    visibility: visible !important;
}

/* Deskripsi di bawah judul */
.app-content .page-header p,
.app-content .page-description,
.app-content .history-subtitle {
    color: #526f98 !important;
    -webkit-text-fill-color: #526f98 !important;
    opacity: 1 !important;
}

/* Pertahankan keterbacaan tanggal */
.app-content .page-header .date,
.app-content .page-header .date-badge {
    color: #123b78 !important;
}


/* ===== PERBAIKAN KONTRAS JUDUL DASHBOARD ===== */
.page-title h1 {
    color: #ffffff !important;
    font-size: clamp(28px, 3vw, 38px) !important;
    font-weight: 800 !important;
    line-height: 1.25 !important;
    letter-spacing: -0.6px !important;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.35) !important;
    opacity: 1 !important;
}

.page-title p {
    color: #e2edff !important;
    font-size: 14px !important;
    line-height: 1.7 !important;
    opacity: 1 !important;
}

.page-title,
.page-title h1,
.page-title p {
    position: relative;
    z-index: 5;
}

/* Teks kecil di atas judul */
.page-title .eyebrow,
.page-title .welcome-label {
    color: #a8ceff !important;
    font-weight: 800 !important;
    letter-spacing: 2px !important;
    opacity: 1 !important;
}


        </style>
@endsection

@section('content')
<!-- =====================================================
         SIDEBAR
    ====================================================== -->

    



    <!-- =====================================================
         MAIN
    ====================================================== -->

    <!-- =================================================
             HEADER
        ================================================== -->

        <div class="topbar">


            <div class="page-title">

                <h1>
                    Dashboard Monitoring Jaringan
                </h1>


                <p>
                    Sistem Monitoring QoS Jaringan Internet
                    @if($selectedSchool)
                        · {{ $selectedSchool->nama_sekolah }}
                    @endif
                </p>

            </div>



            <div class="top-actions">


                <div class="date-box">

                    Tanggal:

                    {{ now()->format('d M Y') }}

                </div>



                <div class="school-select-static">
                    Sekolah: <strong>{{ $selectedSchool?->nama_sekolah ?? '-' }}</strong>
                </div>

            </div>

        </div>



        <!-- =================================================
             SCHOOL INFORMATION
        ================================================== -->

       @if($selectedSchool)

    @php
        $agentOnline = $selectedSchool->agent_last_seen_at
            && $selectedSchool->agent_last_seen_at->gte(now()->subMinutes(10));
    @endphp

    <div class="school-info-panel">

        <div class="school-info-main">

            <div class="school-info-title">
                {{ $selectedSchool->nama_sekolah }}
            </div>

            <div class="school-info-grid">
                <span class="school-info-chip">
                    <span>IP Target</span>
                    <strong>{{ $selectedSchool->ip_target ?: 'Menunggu agent' }}</strong>
                </span>

                <span class="school-info-chip">
                    <span>Bandwidth</span>
                    <strong>
                        {{ $selectedSchool->bandwidth !== null && (float) $selectedSchool->bandwidth > 0
                            ? number_format((float) $selectedSchool->bandwidth, 2) . ' Mbps'
                            : 'Belum diisi' }}
                    </strong>
                </span>

                <span class="school-info-chip">
                    <span>Paket ISP</span>
                    <strong>
                        {{ $selectedSchool->paket_isp ?: ($selectedSchool->agent_isp ?: 'Belum diisi') }}
                    </strong>
                </span>

                @if($selectedSchool->agent_isp && $selectedSchool->agent_isp !== $selectedSchool->paket_isp)
                    <span class="school-info-chip">
                        <span>Provider</span>
                        <strong>{{ $selectedSchool->agent_isp }}</strong>
                    </span>
                @endif

                @if($selectedSchool->agent_public_ip)
                    <span class="school-info-chip">
                        <span>IP Publik</span>
                        <strong>{{ $selectedSchool->agent_public_ip }}</strong>
                    </span>
                @endif

                <span class="school-info-chip">
                    <span>Agent</span>
                    <strong style="color: {{ $agentOnline ? '#0f8a5f' : '#b45309' }};">
                        {{ $selectedSchool->agent_last_seen_at ? ($agentOnline ? 'Online' : 'Offline') : 'Belum terpasang' }}
                    </strong>
                </span>
            </div>

        </div>

        <div class="school-agent-status">
            @if($selectedSchool->agent_last_seen_at)
                <div class="school-agent-note">
                    Monitoring oleh agent sekolah
                    <small>
                        Terakhir terhubung {{ $selectedSchool->agent_last_seen_at->diffForHumans() }}
                    </small>
                </div>
            @endif

            <div class="school-agent-actions">
                <a href="{{ route('agent.setup.cmd', $selectedSchool) }}" class="school-test-btn">
                    Download Installer Agent
                </a>

                @if($selectedSchool->ip_target)
                    <form
                        action="{{ route('qos.test', $selectedSchool) }}"
                        method="POST"
                        style="margin:0;"
                        onsubmit="
                            this.querySelector('button').disabled = true;
                            this.querySelector('button').innerHTML = 'Sedang melakukan pengukuran...';
                        "
                    >

                        @csrf

                        <button type="submit" class="school-test-btn">
                            Tes QoS Sekarang
                        </button>

                    </form>
                @endif
            </div>
        </div>

    </div>

@endif


        <!-- =================================================
             METRICS
        ================================================== -->

        <section class="metrics">


            <!-- THROUGHPUT -->

            <div class="metric-card">

                <div class="metric-top">

                    <span class="metric-name">
                        Throughput
                    </span>

                    <span class="metric-icon">
                        &#8593;
                    </span>

                </div>


                <div>

                    <span class="metric-value">

                        @if(
                            $latestMeasurement &&
                            $latestMeasurement->throughput !== null
                        )

                            {{ number_format(
                                $latestMeasurement->throughput,
                                2
                            ) }}

                        @else

                            --

                        @endif

                    </span>


                    <span class="metric-unit">
                        Mbps
                    </span>

                </div>


                


                                <div class="metric-pulse" aria-label="Indikator visual untuk pengukuran terbaru">


                                    @php
                                        $indicatorValue = (float) ($latestMeasurement?->throughput ?? 0);
                                        $indicatorPercent = max(0, min(100, ($indicatorValue / 10) * 100));
                                    @endphp
                                    <div class="metric-pulse-track"
                                         title="{{ number_format($indicatorValue, 2) }} Mbps"
                                         style="--metric-position: {{ number_format($indicatorPercent, 2, '.', '') }}%;">
                                        <span class="metric-pulse-dot"></span>
                                    </div>


                                    <span class="metric-pulse-label">Nilai terbaru</span>


                                </div>



                <div class="metric-bottom">

                    <span class="trend">
                        Hasil monitoring
                    </span>

                    <span class="badge badge-wait">
                        @if(
                            $latestMeasurement &&
                            $latestMeasurement->throughput !== null
                        )
                            TERSEDIA
                        @else
                            BELUM ADA
                        @endif
                    </span>

                </div>

            </div>



            <!-- DELAY -->

            <div class="metric-card">

                <div class="metric-top">

                    <span class="metric-name">
                        Delay
                    </span>

                    <span class="metric-icon">
                        &#9675;
                    </span>

                </div>


                <div>

                    <span class="metric-value">

                        {{ $latestMeasurement?->delay !== null
                            ? number_format(
                                $latestMeasurement->delay,
                                2
                            )
                            : '--'
                        }}

                    </span>


                    <span class="metric-unit">
                        ms
                    </span>

                </div>


                


                                <div class="metric-pulse" aria-label="Indikator visual untuk pengukuran terbaru">


                                    @php
                                        $indicatorValue = (float) ($latestMeasurement?->delay ?? 0);
                                        $indicatorPercent = max(0, min(100, ($indicatorValue / 150) * 100));
                                    @endphp
                                    <div class="metric-pulse-track"
                                         title="{{ number_format($indicatorValue, 2) }} ms"
                                         style="--metric-position: {{ number_format($indicatorPercent, 2, '.', '') }}%;">
                                        <span class="metric-pulse-dot"></span>
                                    </div>


                                    <span class="metric-pulse-label">Nilai terbaru</span>


                                </div>



                <div class="metric-bottom">

                    <span class="trend">
                        Hasil monitoring
                    </span>

                    <span class="badge badge-wait">
                        DATA
                    </span>

                </div>

            </div>



            <!-- JITTER -->

            <div class="metric-card">

                <div class="metric-top">

                    <span class="metric-name">
                        Jitter
                    </span>

                    <span class="metric-icon">
                        &#8776;
                    </span>

                </div>


                <div>

                    <span class="metric-value">

                        {{ $latestMeasurement?->jitter !== null
                            ? number_format(
                                $latestMeasurement->jitter,
                                2
                            )
                            : '--'
                        }}

                    </span>


                    <span class="metric-unit">
                        ms
                    </span>

                </div>


                


                                <div class="metric-pulse" aria-label="Indikator visual untuk pengukuran terbaru">


                                    @php
                                        $indicatorValue = (float) ($latestMeasurement?->jitter ?? 0);
                                        $indicatorPercent = max(0, min(100, ($indicatorValue / 30) * 100));
                                    @endphp
                                    <div class="metric-pulse-track"
                                         title="{{ number_format($indicatorValue, 2) }} ms"
                                         style="--metric-position: {{ number_format($indicatorPercent, 2, '.', '') }}%;">
                                        <span class="metric-pulse-dot"></span>
                                    </div>


                                    <span class="metric-pulse-label">Nilai terbaru</span>


                                </div>



                <div class="metric-bottom">

                    <span class="trend">
                        Hasil monitoring
                    </span>

                    <span class="badge badge-wait">
                        DATA
                    </span>

                </div>

            </div>



            <!-- PACKET LOSS -->

            <div class="metric-card">

                <div class="metric-top">

                    <span class="metric-name">
                        Packet Loss
                    </span>

                    <span class="metric-icon">
                        %
                    </span>

                </div>


                <div>

                    <span class="metric-value">

                        {{ $latestMeasurement?->packet_loss !== null
                            ? number_format(
                                $latestMeasurement->packet_loss,
                                2
                            )
                            : '--'
                        }}

                    </span>


                    <span class="metric-unit">
                        %
                    </span>

                </div>


                


                                <div class="metric-pulse" aria-label="Indikator visual untuk pengukuran terbaru">


                                    @php
                                        $indicatorValue = (float) ($latestMeasurement?->packet_loss ?? 0);
                                        $indicatorPercent = max(0, min(100, ($indicatorValue / 5) * 100));
                                    @endphp
                                    <div class="metric-pulse-track"
                                         title="{{ number_format($indicatorValue, 2) }} %"
                                         style="--metric-position: {{ number_format($indicatorPercent, 2, '.', '') }}%;">
                                        <span class="metric-pulse-dot"></span>
                                    </div>


                                    <span class="metric-pulse-label">Nilai terbaru</span>


                                </div>



                <div class="metric-bottom">

                    <span class="trend">
                        Hasil monitoring
                    </span>

                    <span class="badge badge-wait">
                        DATA
                    </span>

                </div>

            </div>



            <!-- PING -->

            <div class="metric-card">

                <div class="metric-top">

                    <span class="metric-name">
                        Ping
                    </span>

                    <span class="metric-icon">
                        &#9679;
                    </span>

                </div>


                <div>

                    <span class="metric-value">

                        {{ $latestMeasurement?->ping !== null
                            ? number_format(
                                $latestMeasurement->ping,
                                2
                            )
                            : '--'
                        }}

                    </span>


                    <span class="metric-unit">
                        ms
                    </span>

                </div>


                


                                <div class="metric-pulse" aria-label="Indikator visual untuk pengukuran terbaru">


                                    @php
                                        $indicatorValue = (float) ($latestMeasurement?->ping ?? 0);
                                        $indicatorPercent = max(0, min(100, ($indicatorValue / 100) * 100));
                                    @endphp
                                    <div class="metric-pulse-track"
                                         title="{{ number_format($indicatorValue, 2) }} ms"
                                         style="--metric-position: {{ number_format($indicatorPercent, 2, '.', '') }}%;">
                                        <span class="metric-pulse-dot"></span>
                                    </div>


                                    <span class="metric-pulse-label">Nilai terbaru</span>


                                </div>



                <div class="metric-bottom">

                    <span class="trend">
                        Hasil monitoring
                    </span>

                    <span class="badge badge-wait">
                        DATA
                    </span>

                </div>

            </div>

        </section>



        <!-- =================================================
             CHART + STATUS
        ================================================== -->

        <section class="content-grid">


            <!-- GRAFIK -->

            <div class="panel">

                <div class="panel-header">

                    <div>

                        <div class="panel-title">
                            Grafik Monitoring QoS
                        </div>

                        <div class="panel-subtitle">
                            Data pengukuran terbaru
                        </div>

                    </div>


                    <div class="qos-chart-actions">
                        <span class="panel-subtitle">{{ $chartMeasurements->count() }} data</span>
                        <button type="button" id="qosReplayButton" class="qos-replay" aria-label="Putar ulang animasi grafik">&#8635; Putar animasi</button>
                    </div>

                </div>


                <div class="chart qos-latest-chart">
    <div class="qos-chart-tooltip" id="qosChartTooltip" role="status" aria-live="polite"></div>

    @php
        $measurement = $chartMeasurements->first();
        $qosSeries = [
            ['label' => 'Throughput', 'field' => 'throughput', 'unit' => 'Mbps', 'color' => '#1677ff'],
            ['label' => 'Delay', 'field' => 'delay', 'unit' => 'ms', 'color' => '#12b76a'],
            ['label' => 'Jitter', 'field' => 'jitter', 'unit' => 'ms', 'color' => '#f59e0b'],
            ['label' => 'Packet Loss', 'field' => 'packet_loss', 'unit' => '%', 'color' => '#f04438'],
            ['label' => 'Ping', 'field' => 'ping', 'unit' => 'ms', 'color' => '#8b5cf6'],
        ];
        $pointTime = $measurement?->measured_at
            ? $measurement->measured_at->format('d M Y H:i')
            : '-';
    @endphp

    <svg viewBox="0 0 700 250" preserveAspectRatio="none"
         role="img" aria-label="Lima parameter dari satu pengukuran QoS terbaru">
        <defs>
            <linearGradient id="qosLatestBackground" x1="0" y1="0" x2="1" y2="0">
                <stop offset="0%" stop-color="#f7faff"/>
                <stop offset="100%" stop-color="#ffffff"/>
            </linearGradient>
        </defs>
        <rect width="700" height="250" rx="10" fill="url(#qosLatestBackground)"/>

        @foreach($qosSeries as $index => $series)
            @php
                $y = 25 + ($index * 50);
                $value = $measurement ? $measurement->{$series['field']} : null;
            @endphp

            <rect x="5" y="{{ $y - 18 }}" width="690" height="38" rx="8"
                  fill="{{ $index % 2 === 0 ? '#f0f6ff' : '#ffffff' }}"/>
            <line x1="10" y1="{{ $y + 22 }}" x2="690" y2="{{ $y + 22 }}"
                  stroke="#e4ecf7" stroke-width="1"/>

            <circle cx="22" cy="{{ $y }}" r="4.5" fill="{{ $series['color'] }}"/>
            <text x="35" y="{{ $y + 4 }}" font-size="12" font-weight="600" fill="#475569">
                {{ $series['label'] }}
            </text>

            @if($value !== null)
                <line x1="260" y1="{{ $y }}" x2="425" y2="{{ $y }}"
                      stroke="{{ $series['color'] }}" stroke-width="3"
                      stroke-linecap="round" stroke-opacity=".28"/>
                <circle class="qos-data-point qos-latest-point"
                        cx="425" cy="{{ $y }}" r="6"
                        fill="#fff" stroke="{{ $series['color'] }}"
                        tabindex="0" role="img"
                        aria-label="{{ $series['label'] }} {{ number_format((float) $value, 2) }} {{ $series['unit'] }}, {{ $pointTime }}"
                        data-label="{{ $series['label'] }}"
                        data-value="{{ number_format((float) $value, 2, '.', '') }}"
                        data-unit="{{ $series['unit'] }}"
                        data-time="{{ $pointTime }}"
                        data-color="{{ $series['color'] }}">
                    <title>{{ $series['label'] }}: {{ number_format((float) $value, 2) }} {{ $series['unit'] }} | {{ $pointTime }}</title>
                </circle>
                <text x="445" y="{{ $y + 4 }}" font-size="12" font-weight="700" fill="#243b64">
                    {{ number_format((float) $value, 2) }} {{ $series['unit'] }}
                </text>
            @else
                <text x="425" y="{{ $y + 4 }}" font-size="12" fill="#94a3b8">Belum ada data</text>
            @endif
        @endforeach
    </svg>
</div>


                <p class="qos-chart-hint">Lima parameter dari satu pengukuran QoS terbaru. Arahkan kursor ke titik untuk melihat nilai asli dan waktu pengukuran. Indikator pada kartu adalah visualisasi nilai terbaru, bukan riwayat pengukuran.</p>
                <div class="chart-times">

                    @foreach($chartMeasurements as $measurement)

                        <span>

                            {{ $measurement->measured_at
                                ->format('H:i')
                            }}

                        </span>

                    @endforeach

                </div>


                <div class="legend">

                    <div class="legend-item">

                        <span class="legend-dot"></span>

                        Throughput

                    </div>


                    <div class="legend-item">

                        <span class="legend-dot delay"></span>

                        Delay

                    </div>


                    <div class="legend-item">

                        <span class="legend-dot jitter"></span>

                        Jitter

                    </div>


                    <div class="legend-item">

                        <span class="legend-dot loss"></span>

                        Packet Loss

                    </div>


                    <div class="legend-item">

                        <span class="legend-dot ping"></span>

                        Ping

                    </div>

                </div>

            </div>



            <!-- STATUS SEKOLAH -->

            <div class="panel">

                <div class="panel-header">

                    <div>

                        <div class="panel-title">

                            Status Jaringan per Sekolah

                        </div>

                        <div class="panel-subtitle">

                            Klasifikasi berdasarkan TIPHON

                        </div>

                    </div>

                </div>


                <table class="school-table">

                    <thead>

                        <tr>

                            <th>
                                Sekolah
                            </th>

                            <th>
                                Ping
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($schoolStatuses as $item)

                            @php

                                $school = $item['school'];

                                $measurement = $item['measurement'];

                            @endphp


                            <tr>

                                <td>

                                    <div class="school-cell">

                                        <span class="school-icon">

                                            {{ strtoupper(
                                                substr(
                                                    $school->nama_sekolah,
                                                    0,
                                                    2
                                                )
                                            ) }}

                                        </span>


                                        <span class="school-name">

                                            {{ $school->nama_sekolah }}

                                        </span>

                                    </div>

                                </td>


                                <td>

                                    @if(
                                        $measurement &&
                                        $measurement->ping !== null
                                    )

                                        {{ number_format(
                                            $measurement->ping,
                                            2
                                        ) }}

                                        ms

                                    @else

                                        --

                                    @endif

                                </td>


                                <td>

                                    @php
                                        $classification = $item['classification'] ?? null;
                                        $condition = $classification['condition'] ?? null;
                                        $qosIndex = $classification['qos_index'] ?? null;
                                        $statusClass = match ($condition) {
                                            'BAIK' => 'badge-good',
                                            'SEDANG' => 'badge-medium',
                                            'BURUK' => 'badge-bad',
                                            default => 'badge-wait',
                                        };
                                    @endphp

                                    <span class="badge {{ $statusClass }}">
                                        {{ $condition ?? 'BELUM DIKLASIFIKASI' }}
                                    </span>

                                    @if($qosIndex !== null)
                                        <div class="qos-score">
                                            Indeks QoS: {{ number_format($qosIndex, 2) }}
                                        </div>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3">

                                    Belum ada data sekolah.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>



        <!-- =================================================
             RCA + DSS
        ================================================== -->

        <section class="bottom-grid">


            <!-- RCA -->

            <div class="panel">

                <div class="panel-header">
                    <div>
                        <div class="panel-title">
                            Root Cause Analysis (RCA)
                        </div>
                        <div class="panel-subtitle">
                            Analisis berbasis Rule-Based RCA dari hasil TIPHON
                        </div>
                    </div>
                </div>

                @forelse($schoolStatuses as $item)

                    @php
                        $classification = $item['classification'] ?? null;
                        $rca = $item['rca'] ?? null;
                        $condition = $classification['condition'] ?? null;
                        $qosIndex = $classification['qos_index'] ?? null;
                        $causes = $rca['causes'] ?? [];
                    @endphp

                    <div class="rca-item">

                        <div class="rca-school">
                            {{ $item['school']->nama_sekolah }}
                        </div>

                        <div class="rca-cause">

                            @if($condition === 'BAIK')

                                <strong>Kondisi BAIK</strong><br>
                                Tidak diperlukan analisis RCA karena kualitas jaringan berada pada kategori baik.

                            @elseif($condition === 'SEDANG' || $condition === 'BURUK')

                                <strong>
                                    Kondisi {{ $condition }}
                                    @if($qosIndex !== null)
                                        &mdash; Indeks QoS {{ number_format($qosIndex, 2) }}
                                    @endif
                                </strong>

                                @if(count($causes) > 0)
                                    <br><br>
                                    <strong>Indikasi penyebab:</strong>
                                    <ul style="margin: 6px 0 0 18px; padding: 0;">
                                        @foreach($causes as $cause)
                                            <li style="margin-bottom: 5px;">
                                                <strong>{{ $cause['parameter'] }}</strong>:
                                                {{ $cause['cause'] }}
                                                <span style="font-size: 10px;">
                                                    ({{ $cause['severity'] }})
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <br>
                                    {{ $rca['summary'] ?? 'Belum ditemukan rule RCA yang sesuai.' }}
                                @endif

                            @else

                                Data QoS belum lengkap sehingga RCA belum dapat dilakukan.

                            @endif

                        </div>

                        <div class="confidence">
                            @if($condition === 'BAIK')
                                Normal
                            @elseif(count($causes) > 0)
                                {{ count($causes) }} Rule aktif
                            @elseif($condition === 'SEDANG' || $condition === 'BURUK')
                                Perlu cek
                            @else
                                --
                            @endif
                        </div>

                    </div>

                @empty

                    <div class="rca-item">
                        <div class="rca-cause">
                            Belum ada data sekolah.
                        </div>
                    </div>

                @endforelse

            </div>


            <!-- DSS -->

            <div class="panel">

                <div class="panel-header">
                    <div>
                        <div class="panel-title">
                            Decision Support System (DSS)
                        </div>
                        <div class="panel-subtitle">
                            Rekomendasi tindakan berdasarkan hasil RCA
                        </div>
                    </div>
                </div>

                @php
                    $selectedCondition = $latestClassification['condition'] ?? null;
                    $selectedQosIndex = $latestClassification['qos_index'] ?? null;
                    $selectedDss = $latestDss ?? null;
                    $recommendations = $selectedDss['recommendations'] ?? [];
                @endphp

                <div class="dss-item">
                    <div class="dss-icon">&#10003;</div>
                    <div class="dss-content">
                        <strong>Hasil Monitoring</strong>
                        <p>
                            @if($latestMeasurement)
                                Data QoS terbaru berhasil diambil dari database.
                            @else
                                Belum tersedia data monitoring untuk sekolah yang dipilih.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="dss-item">
                    <div class="dss-icon">&#128161;</div>
                    <div class="dss-content">
                        <strong>Klasifikasi TIPHON</strong>
                        <p>
                            @if($selectedCondition && $selectedQosIndex !== null)
                                Indeks QoS {{ number_format($selectedQosIndex, 2) }}
                                dengan kategori {{ $selectedCondition }}.
                            @else
                                Klasifikasi belum dapat ditentukan karena data QoS belum lengkap.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="dss-item">
                    <div class="dss-icon">&#9881;</div>
                    <div class="dss-content" style="width: 100%;">
                        <strong>Rekomendasi Tindakan</strong>

                        @if($selectedDss && !empty($recommendations))

                            <p style="margin-bottom: 10px;">
                                {{ $selectedDss['summary'] ?? 'Rekomendasi dibuat berdasarkan hasil RCA.' }}
                            </p>

                            @foreach($recommendations as $recommendation)
                                <div style="margin-bottom: 10px; padding: 9px 10px; border: 1px solid #edf0f4; border-radius: 8px; background: #fafcff;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 4px;">
                                        <strong style="margin-bottom: 0;">
                                            {{ $recommendation['parameter'] ?? 'QoS' }}
                                        </strong>
                                        <span style="font-size: 8px; font-weight: bold; padding: 3px 7px; border-radius: 5px; background: #fff7ed; color: #c2410c;">
                                            {{ $recommendation['priority'] ?? 'SEDANG' }}
                                        </span>
                                    </div>
                                    <p style="margin: 0;">
                                        {{ $recommendation['action'] ?? '-' }}
                                    </p>
                                </div>
                            @endforeach

                        @elseif($selectedCondition === 'BAIK')

                            <p>
                                {{ $selectedDss['summary'] ?? 'Pertahankan konfigurasi jaringan dan lakukan monitoring berkala.' }}
                            </p>

                        @else

                            <p>
                                Rekomendasi akan tersedia setelah data QoS lengkap dan klasifikasi TIPHON berhasil.
                            </p>

                        @endif
                    </div>
                </div>

            </div>



        <!-- =================================================
             NOTIFICATION
        ================================================== -->

        <section class="notification">

            <div class="panel-header">

                <div>

                    <div class="panel-title">

                        &#128276; Notifikasi & Alarm

                    </div>

                    <div class="panel-subtitle">

                        Status sistem monitoring

                    </div>

                </div>

            </div>


            <div class="notification-list">


                <div class="notification-item">

                    <div class="notification-icon">

                        &#10003;

                    </div>


                    <div class="notification-text">

                        Sistem monitoring aktif.

                        <span class="notification-time">

                            Laravel Scheduler

                        </span>

                    </div>

                </div>



                <div class="notification-item">
                    <div class="notification-icon">!</div>
                    <div class="notification-text">
                        @if(($latestClassification['condition'] ?? null) === 'BURUK')
                            Kualitas jaringan terdeteksi BURUK.
                            <span class="notification-time">Perlu pemeriksaan dan RCA lebih lanjut</span>
                        @elseif(($latestClassification['condition'] ?? null) === 'SEDANG')
                            Kualitas jaringan berada pada kategori SEDANG.
                            <span class="notification-time">Perlu evaluasi parameter QoS</span>
                        @elseif(($latestClassification['condition'] ?? null) === 'BAIK')
                            Kualitas jaringan berada pada kategori BAIK.
                            <span class="notification-time">Tidak ada alarm kualitas saat ini</span>
                        @else
                            Data QoS belum cukup untuk membuat alarm kualitas.
                            <span class="notification-time">Menunggu hasil pengukuran lengkap</span>
                        @endif
                    </div>
                </div>

                </div>



                <div class="notification-item">

                    <div class="notification-icon">

                        &#9675;

                    </div>


                    <div class="notification-text">

                        Monitoring dilakukan setiap 5 menit.

                        <span class="notification-time">

                            Scheduler

                        </span>

                    </div>

                </div>


            </div>

        </section>



        <!-- =================================================
             FOOTER
        ================================================== -->

        <div class="footer">

            &copy; {{ date('Y') }}

            Sistem Monitoring QoS Jaringan Internet
            Wi-Fi SMA/SMK Kecamatan Bukit Batu

        </div>


    </main>
@endsection

@push('scripts')
<script>
    // Tooltip yang jelas saat titik grafik diarahkan kursor atau dipilih dengan keyboard.
    (function () {
        const chart = document.querySelector('.chart');
        const tooltip = document.getElementById('qosChartTooltip');
        if (!chart || !tooltip) return;

        function showTooltip(point) {
            tooltip.innerHTML = '';
            const heading = document.createElement('strong');
            heading.textContent = point.dataset.label || 'Parameter QoS';
            const value = document.createElement('div');
            value.textContent = (point.dataset.value || '--') + ' ' + (point.dataset.unit || '');
            const time = document.createElement('div');
            time.textContent = 'Waktu: ' + (point.dataset.time || '-');
            const marker = document.createElement('span');
            marker.style.cssText = 'display:inline-block;width:7px;height:7px;border-radius:50%;margin-right:6px;background:' + (point.dataset.color || '#fff');
            heading.prepend(marker);
            tooltip.append(heading, value, time);
            tooltip.style.display = 'block';

            const chartRect = chart.getBoundingClientRect();
            const pointRect = point.getBoundingClientRect();
            let left = pointRect.left - chartRect.left + pointRect.width / 2 + 12;
            let top = pointRect.top - chartRect.top - 12;
            const maxLeft = chart.clientWidth - tooltip.offsetWidth - 8;
            if (left > maxLeft) left = Math.max(8, pointRect.left - chartRect.left - tooltip.offsetWidth - 12);
            if (top < 8) top = 8;
            if (top + tooltip.offsetHeight > chart.clientHeight - 8) top = chart.clientHeight - tooltip.offsetHeight - 8;
            tooltip.style.left = Math.max(8, left) + 'px';
            tooltip.style.top = Math.max(8, top) + 'px';
        }
        function hideTooltip() { tooltip.style.display = 'none'; }
        chart.querySelectorAll('.qos-data-point').forEach(function (point) {
            point.addEventListener('mouseenter', function () { showTooltip(point); });
            point.addEventListener('mousemove', function () { showTooltip(point); });
            point.addEventListener('mouseleave', hideTooltip);
            point.addEventListener('focus', function () { showTooltip(point); });
            point.addEventListener('blur', hideTooltip);
        });
    })();

    // Putar ulang animasi SVG tanpa memuat ulang halaman atau mengambil ulang data.
    document.getElementById('qosReplayButton')?.addEventListener('click', function () {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        const chart = document.querySelector('.qos-latest-chart');
        if (!chart) return;
        chart.querySelectorAll('.qos-latest-point').forEach(function (point) {
            point.style.animation = 'none';
            void point.getBoundingClientRect();
            point.style.animation = '';
        });
    });

    // Refresh dashboard mengikuti pengaturan sistem (detik).
    const dashboardRefreshSeconds = @json($dashboardRefreshInterval ?? 30);
    setInterval(function () {
        window.location.reload();
    }, Math.max(15, dashboardRefreshSeconds) * 1000);
</script>
@endpush


