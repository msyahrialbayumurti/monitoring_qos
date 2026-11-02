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
            --border: #e1e9f5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            color: var(--text-main);
            background:
                radial-gradient(circle at 8% 8%, rgba(50, 136, 255, .10), transparent 28%),
                radial-gradient(circle at 92% 90%, rgba(23, 101, 220, .08), transparent 30%),
                var(--page-bg);
        }

        .auth-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr);
            width: 100%;
            min-height: 100vh;
        }

        /* ========================================
           PANEL KIRI
        ======================================== */

        .auth-showcase {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 34px;
            min-height: 100vh;
            padding: 48px clamp(30px, 5vw, 74px);
            color: #fff;
            background:
                radial-gradient(circle at 82% 20%, rgba(50, 136, 255, .30), transparent 35%),
                linear-gradient(155deg, var(--navy-950), var(--navy-900) 54%, var(--navy-800));
        }

        .auth-showcase::before,
        .auth-showcase::after {
            content: "";
            position: absolute;
            border: 1px solid rgba(104, 173, 255, .16);
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-showcase::before {
            top: -170px;
            right: -150px;
            width: 470px;
            height: 470px;
            box-shadow:
                0 0 0 50px rgba(65, 145, 255, .035),
                0 0 0 110px rgba(65, 145, 255, .022);
        }

        .auth-showcase::after {
            bottom: -230px;
            left: -190px;
            width: 520px;
            height: 520px;
            box-shadow:
                0 0 0 55px rgba(65, 145, 255, .03),
                0 0 0 120px rgba(65, 145, 255, .02);
        }

        .auth-brand,
        .auth-hero,
        .auth-foot {
            position: relative;
            z-index: 2;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .auth-brand-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            color: var(--blue-600);
            background: linear-gradient(145deg, #fff, #dceaff);
            font-size: 26px;
            font-weight: 800;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .22);
        }

        .auth-brand-text h2 {
            font-size: 17px;
            letter-spacing: .6px;
        }

        .auth-brand-text p {
            margin-top: 3px;
            color: #a9c5ec;
            font-size: 10px;
            letter-spacing: .35px;
        }

        .auth-hero {
            max-width: 560px;
            margin: auto 0;
        }

        .auth-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 18px;
            color: #8ec0ff;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .22em;
            text-transform: uppercase;
        }

        .auth-eyebrow::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4f9dff;
            box-shadow: 0 0 0 5px rgba(79, 157, 255, .14);
        }

        .auth-hero h1 {
            max-width: 540px;
            font-size: clamp(34px, 4.2vw, 54px);
            line-height: 1.08;
            letter-spacing: -.045em;
            font-weight: 800;
        }

        .auth-hero h1 span {
            color: #6fb4ff;
        }

        .auth-hero > p {
            max-width: 470px;
            margin-top: 20px;
            color: #b9d0ee;
            font-size: 14px;
            line-height: 1.8;
        }

        .auth-features {
            display: grid;
            gap: 13px;
            margin-top: 30px;
            list-style: none;
        }

        .auth-features li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: #d8e8ff;
            font-size: 12px;
            line-height: 1.6;
        }

        .auth-features b {
            display: flex;
            flex: 0 0 26px;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border: 1px solid rgba(108, 177, 255, .35);
            border-radius: 8px;
            color: #8fc5ff;
            background: rgba(58, 132, 238, .16);
            font-size: 11px;
        }

        .auth-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-top: 22px;
            border-top: 1px solid rgba(255, 255, 255, .12);
            color: #8faed6;
            font-size: 10px;
        }

        .auth-foot strong {
            color: #d7e8ff;
            font-weight: 700;
        }

        /* ========================================
           PANEL KANAN
        ======================================== */

        .auth-main {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
            padding: 46px clamp(22px, 5vw, 68px);
        }

        .auth-mobile-brand {
            display: none;
            align-items: center;
            gap: 12px;
            width: 100%;
            max-width: 620px;
            margin-bottom: 4px;
        }

        .auth-mobile-brand .auth-brand-icon {
            width: 42px;
            height: 42px;
            font-size: 22px;
            color: #fff;
            background: linear-gradient(145deg, #3288ff, #125bd1);
            box-shadow: 0 7px 18px rgba(23, 101, 220, .24);
        }

        .auth-mobile-brand h2 {
            color: #12386d;
            font-size: 15px;
            letter-spacing: .4px;
        }

        .auth-mobile-brand p {
            color: #7b90b0;
            font-size: 9px;
        }

        .auth-card {
            width: 100%;
            max-width: 470px;
            padding: clamp(26px, 3.2vw, 42px);
            border: 1px solid rgba(216, 229, 248, .95);
            border-radius: 24px;
            background: rgba(255, 255, 255, .94);
            box-shadow:
                0 28px 65px rgba(29, 69, 132, .11),
                0 4px 16px rgba(29, 69, 132, .05);
            backdrop-filter: blur(8px);
        }

        .auth-card-wide {
            max-width: 660px;
        }

        .auth-card-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 11px;
            margin-bottom: 16px;
            border: 1px solid #d7e7ff;
            border-radius: 999px;
            color: #3f74b9;
            background: #f3f8ff;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .auth-card-badge::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #2b82f6;
            box-shadow: 0 0 0 3px rgba(43, 130, 246, .12);
        }

        .auth-card h2 {
            color: #102f60;
            font-size: clamp(24px, 2.5vw, 31px);
            line-height: 1.15;
            letter-spacing: -.035em;
            font-weight: 800;
        }

        .auth-sub {
            margin-top: 9px;
            margin-bottom: 24px;
            color: #7488a7;
            font-size: 12px;
            line-height: 1.7;
        }

        .auth-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 18px;
            padding: 12px 14px;
            border: 1px solid #fecdd3;
            border-radius: 12px;
            color: #9f2635;
            background: #fff1f2;
            font-size: 11px;
            line-height: 1.6;
        }

        .auth-alert strong {
            display: block;
            margin-bottom: 2px;
        }

        .auth-alert ul {
            margin: 3px 0 0;
            padding-left: 17px;
        }

        .auth-section-label {
            margin: 22px 0 12px;
            padding-top: 18px;
            border-top: 1px solid #edf2fa;
            color: #4e72a5;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .auth-section-label:first-of-type {
            margin-top: 0;
            padding-top: 0;
            border-top: 0;
        }

        .auth-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .auth-span-2 {
            grid-column: 1 / -1;
        }

        .auth-field {
            margin-bottom: 15px;
        }

        .auth-grid .auth-field {
            margin-bottom: 0;
        }

        .auth-field label {
            display: block;
            margin-bottom: 7px;
            color: #294568;
            font-size: 11px;
            font-weight: 750;
        }

        .auth-required {
            color: #e24a5a;
        }

        .auth-input-wrap {
            position: relative;
        }

        .auth-input-icon {
            position: absolute;
            top: 50%;
            left: 13px;
            transform: translateY(-50%);
            color: #7d98c0;
            font-size: 13px;
            font-weight: 700;
            pointer-events: none;
        }

        .auth-field input {
            display: block;
            width: 100%;
            min-height: 45px;
            padding: 11px 13px 11px 38px;
            border: 1px solid #d8e3f2;
            border-radius: 11px;
            outline: none;
            color: #1d355b;
            background: #fbfdff;
            font: inherit;
            font-size: 12px;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .auth-field input::placeholder {
            color: #9aabc2;
        }

        .auth-field input:hover {
            border-color: #b8cdeb;
        }

        .auth-field input:focus {
            border-color: #4389f4;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(40, 119, 235, .11);
        }

        .auth-field input.is-invalid {
            border-color: #e76a76;
            background: #fffafa;
        }

        .auth-error {
            margin-top: 6px;
            color: #c53030;
            font-size: 10px;
            line-height: 1.5;
        }

        .auth-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 4px 0 20px;
        }

        .auth-check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #5f7698;
            font-size: 11px;
            cursor: pointer;
            user-select: none;
        }

        .auth-check input {
            width: 15px;
            height: 15px;
            accent-color: #1765dc;
            cursor: pointer;
        }

        .auth-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            min-height: 47px;
            border: 1px solid #1768e5;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(135deg, #3284ff, #125bd1);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .2px;
            cursor: pointer;
            box-shadow: 0 10px 22px rgba(23, 101, 220, .24);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .auth-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 27px rgba(23, 101, 220, .3);
        }

        .auth-submit:active {
            transform: translateY(0);
        }

        .auth-switch {
            margin-top: 22px;
            color: #7188a8;
            font-size: 11px;
            text-align: center;
        }

        .auth-switch a {
            color: #1765dc;
            font-weight: 800;
            text-decoration: none;
        }

        .auth-switch a:hover {
            text-decoration: underline;
        }

        .auth-copyright {
            color: #8ba0be;
            font-size: 10px;
            text-align: center;
        }

        @media (max-width: 980px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }

            .auth-showcase {
                display: none;
            }

            .auth-main {
                min-height: 100vh;
                padding: 32px 20px;
            }

            .auth-mobile-brand {
                display: flex;
            }
        }

        @media (max-width: 640px) {
            .auth-card {
                padding: 24px 20px;
                border-radius: 18px;
            }

            .auth-grid {
                grid-template-columns: 1fr;
            }

            .auth-span-2 {
                grid-column: auto;
            }

            .auth-field {
                margin-bottom: 14px;
            }

            .auth-grid .auth-field {
                margin-bottom: 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .auth-submit {
                transition: none;
            }
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <aside class="auth-showcase">
            <div class="auth-brand">
                <div class="auth-brand-icon" aria-hidden="true">⌁</div>
                <div class="auth-brand-text">
                    <h2>NETMONITOR</h2>
                    <p>Sistem Monitoring QoS Jaringan</p>
                </div>
            </div>

            <div class="auth-hero">
                <div class="auth-eyebrow">Quality of Service Monitoring</div>
                <h1>Pantau kualitas jaringan sekolah dalam <span>satu dashboard</span>.</h1>
                <p>
                    Ukur throughput, delay, jitter, dan packet loss secara otomatis.
                    Ketahui kondisi jaringan, penyebab gangguan, dan rekomendasi
                    penanganannya dalam satu tempat.
                </p>

                <ul class="auth-features">
                    <li>
                        <b>01</b>
                        <span>Pengukuran QoS otomatis menggunakan ping dan Speedtest.</span>
                    </li>
                    <li>
                        <b>02</b>
                        <span>Klasifikasi kondisi jaringan dengan standar TIPHON.</span>
                    </li>
                    <li>
                        <b>03</b>
                        <span>Analisis penyebab dan rekomendasi tindakan saat jaringan bermasalah.</span>
                    </li>
                </ul>
            </div>

            <div class="auth-foot">
                <span>Monitoring otomatis · Laravel Scheduler</span>
                <strong>NETMONITOR</strong>
            </div>
        </aside>

        <main class="auth-main">
            <div class="auth-mobile-brand">
                <div class="auth-brand-icon" aria-hidden="true">⌁</div>
                <div>
                    <h2>NETMONITOR</h2>
                    <p>Sistem Monitoring QoS Jaringan</p>
                </div>
            </div>

            @yield('content')

            <p class="auth-copyright">
                &copy; {{ now()->year }} NETMONITOR · Sistem Monitoring QoS Jaringan
            </p>
        </main>
    </div>
</body>
</html>
