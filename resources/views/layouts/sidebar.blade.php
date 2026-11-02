
<style>
    .sidebar .brand {
        padding: 4px 5px 26px !important;
        gap: 10px !important;
    }

    .sidebar .brand-icon {
        border-radius: 12px !important;
        background: linear-gradient(145deg, #ffffff, #dceaff) !important;
        box-shadow: 0 5px 18px rgba(0, 0, 0, .16);
    }

    .sidebar .brand-text h2 {
        font-size: 15px !important;
        letter-spacing: .35px !important;
    }

    .sidebar .brand-text p {
        font-size: 8px !important;
    }

    .sidebar .menu-title {
        font-size: 9px !important;
        letter-spacing: 1.35px !important;
        margin-bottom: 7px !important;
    }

    .sidebar .menu {
        gap: 5px !important;
    }

    .sidebar .menu a {
        min-height: 42px !important;
        height: 42px !important;
        flex-basis: 42px !important;
        border-radius: 9px !important;
        font-size: 12px !important;
        transition: background .22s ease, transform .22s ease,
                    color .22s ease, box-shadow .22s ease !important;
    }

    .sidebar .menu a:hover {
        transform: translateX(3px);
    }

    .sidebar .menu a.active {
        background: linear-gradient(115deg, #3288ff, #1266d8) !important;
        box-shadow: 0 5px 15px rgba(18, 102, 216, .28) !important;
    }

    .sidebar .menu-icon {
        font-size: 16px !important;
    }

    .sidebar-network-art {
        position: relative;
        flex: 0 0 auto;
        height: 168px;
        margin: 17px 0 15px;
        border-radius: 13px;
        overflow: hidden;
        background:
            radial-gradient(ellipse at 50% 45%, rgba(31, 126, 255, .18), transparent 62%),
            linear-gradient(180deg, rgba(13, 44, 91, .16), rgba(8, 21, 47, .06));
    }

    .sidebar-network-art::before,
    .sidebar-network-art::after {
        content: "";
        position: absolute;
        left: 50%;
        top: 42px;
        width: 95px;
        height: 95px;
        border: 1px solid rgba(76, 160, 255, .16);
        border-radius: 50%;
        transform: translateX(-50%);
    }

    .sidebar-network-art::after {
        width: 66px;
        height: 66px;
        top: 56px;
        border-color: rgba(76, 160, 255, .24);
    }

    .sidebar-network-wifi {
        position: absolute;
        left: 50%;
        top: 9px;
        transform: translateX(-50%);
        color: #55a7ff;
        font-size: 34px;
        line-height: 1;
        text-shadow: 0 0 18px rgba(50, 136, 255, .4);
        animation: sidebar-signal 2.4s ease-in-out infinite;
    }

    .sidebar-network-router {
        position: absolute;
        top: 55px;
        left: 50%;
        transform: translateX(-50%);
        width: 56px;
        height: 31px;
        border: 1px solid #5597e8;
        border-radius: 5px;
        background: linear-gradient(145deg, #2469b7, #12386d);
        box-shadow: 0 7px 16px rgba(0, 0, 0, .22);
    }

    .sidebar-network-router::before,
    .sidebar-network-router::after {
        content: "";
        position: absolute;
        top: -13px;
        width: 2px;
        height: 15px;
        background: #91c5ff;
        border-radius: 3px;
    }

    .sidebar-network-router::before {
        left: 9px;
        transform: rotate(-12deg);
    }

    .sidebar-network-router::after {
        right: 9px;
        transform: rotate(12deg);
    }

    .sidebar-router-light {
        position: absolute;
        left: 10px;
        bottom: 6px;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #48e2ad;
        box-shadow: 9px 0 #68b4ff, 18px 0 #68b4ff;
    }

    .sidebar-network-link {
        position: absolute;
        left: 50%;
        top: 87px;
        height: 23px;
        border-left: 1px dashed #4d9cf5;
        opacity: .8;
    }

    .sidebar-network-nodes {
        position: absolute;
        bottom: 10px;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-evenly;
        align-items: end;
    }

    .sidebar-network-node {
        position: relative;
        width: 36px;
        height: 30px;
        border: 1px solid rgba(83, 157, 247, .65);
        border-radius: 5px 5px 3px 3px;
        background: linear-gradient(145deg, #164a87, #0c2851);
        box-shadow: 0 5px 12px rgba(0, 0, 0, .16);
    }

    .sidebar-network-node::before {
        content: "";
        position: absolute;
        left: 5px;
        right: 5px;
        top: 6px;
        height: 9px;
        border-radius: 2px;
        background: repeating-linear-gradient(
            90deg, #3b91ed 0 4px, transparent 4px 7px
        );
        opacity: .85;
    }

    .sidebar-network-node::after {
        content: "";
        position: absolute;
        left: 9px;
        right: 9px;
        bottom: 5px;
        height: 4px;
        background: #4aa4ff;
        opacity: .65;
    }

    .sidebar-network-node:nth-child(2) {
        width: 43px;
        height: 43px;
        border-color: #68b1ff;
    }

    .sidebar-network-node:nth-child(2)::before {
        height: 14px;
    }

    .sidebar-network-caption {
        position: absolute;
        top: 115px;
        width: 100%;
        text-align: center;
        color: #8eb9ec;
        font-size: 8px;
        letter-spacing: 1.3px;
        text-transform: uppercase;
    }

    .sidebar .sidebar-status {
        padding: 12px !important;
        border-radius: 11px !important;
        background: linear-gradient(
            145deg, rgba(255,255,255,.085), rgba(255,255,255,.035)
        ) !important;
        border-color: rgba(163, 196, 239, .18) !important;
    }

    .sidebar .status-online {
        font-size: 10px !important;
    }

    .sidebar .sidebar-status p {
        font-size: 8px !important;
        line-height: 1.6 !important;
    }

    .sidebar .sidebar-user {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 12px;
        padding: 10px;
        border: 1px solid rgba(163, 196, 239, .18);
        border-radius: 11px;
        background: linear-gradient(
            145deg, rgba(255,255,255,.085), rgba(255,255,255,.035)
        );
    }

    .sidebar .sidebar-user-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        color: #0b2a56;
        background: linear-gradient(145deg, #ffffff, #cfe3ff);
        font-size: 13px;
        font-weight: 800;
    }

    .sidebar .sidebar-user-copy {
        flex: 1;
        min-width: 0;
    }

    .sidebar .sidebar-user-copy strong {
        display: block;
        overflow: hidden;
        color: #eef6ff;
        font-size: 10px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sidebar .sidebar-user-copy span {
        display: block;
        overflow: hidden;
        margin-top: 2px;
        color: #9dbbe4;
        font-size: 8px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sidebar .sidebar-logout {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 28px;
        width: 28px;
        height: 28px;
        border: 1px solid rgba(255, 255, 255, .16);
        border-radius: 8px;
        color: #cfe2ff;
        background: rgba(255, 255, 255, .06);
        font-size: 12px;
        cursor: pointer;
        transition: background .18s ease, color .18s ease;
    }

    .sidebar .sidebar-logout:hover {
        color: #fff;
        background: rgba(239, 68, 68, .75);
    }

    @keyframes sidebar-signal {
        0%, 100% { opacity: .75; transform: translateX(-50%) translateY(0); }
        50% { opacity: 1; transform: translateX(-50%) translateY(-3px); }
    }

    @media (max-width: 650px) {
        .sidebar-network-art {
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sidebar-network-wifi {
            animation: none;
        }

        .sidebar .menu a {
            transition: none !important;
        }
    }
</style>

<aside class="sidebar">
    {{-- BRAND --}}
    <div class="brand">
        <div class="brand-icon" aria-hidden="true">⌁</div>

        <div class="brand-text">
            <h2>NETMONITOR</h2>
            <p>Sistem Monitoring QoS Jaringan</p>
        </div>
    </div>

    {{-- JUDUL MENU --}}
    <div class="menu-title">Menu Utama</div>

    {{-- DAFTAR MENU --}}
    <nav class="menu" aria-label="Navigasi utama">
        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="menu-icon" aria-hidden="true">⌂</span>
            <span class="menu-label">Dashboard</span>
        </a>

        <a href="{{ route('monitoring.index') }}"
           class="{{ request()->routeIs('monitoring.index') ? 'active' : '' }}">
            <span class="menu-icon" aria-hidden="true">◉</span>
            <span class="menu-label">Monitoring QoS</span>
        </a>

        @if(auth()->user()?->isAdmin())
            <a href="{{ route('schools.index') }}"
               class="{{ request()->routeIs('schools.*') ? 'active' : '' }}">
                <span class="menu-icon" aria-hidden="true">▣</span>
                <span class="menu-label">Data Sekolah</span>
            </a>
        @endif

        <a href="{{ route('monitoring.history') }}"
           class="{{ request()->routeIs('monitoring.history') ? 'active' : '' }}">
            <span class="menu-icon" aria-hidden="true">◷</span>
            <span class="menu-label">Riwayat Monitoring</span>
        </a>

        <a href="{{ route('qos.analysis') }}"
           class="{{ request()->routeIs('qos.analysis') ? 'active' : '' }}">
            <span class="menu-icon" aria-hidden="true">▥</span>
            <span class="menu-label">Analisis QoS</span>
        </a>

        <a href="{{ route('recommendations.index') }}"
           class="{{ request()->routeIs('recommendations.*') ? 'active' : '' }}">
            <span class="menu-icon" aria-hidden="true">✓</span>
            <span class="menu-label">Rekomendasi</span>
        </a>

        <a href="{{ route('notifications.index') }}"
           class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <span class="menu-icon" aria-hidden="true">♢</span>
            <span class="menu-label">Notifikasi</span>
        </a>

        <a href="{{ route('settings.index') }}"
           class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <span class="menu-icon" aria-hidden="true">⚙</span>
            <span class="menu-label">Pengaturan</span>
        </a>
    </nav>

    {{-- ILUSTRASI JARINGAN --}}
    <div class="sidebar-network-art" aria-hidden="true">
        <div class="sidebar-network-wifi">⌁</div>

        <div class="sidebar-network-router">
            <span class="sidebar-router-light"></span>
        </div>

        <div class="sidebar-network-link"></div>

        <div class="sidebar-network-caption">
            Network infrastructure
        </div>

        <div class="sidebar-network-nodes">
            <div class="sidebar-network-node"></div>
            <div class="sidebar-network-node"></div>
            <div class="sidebar-network-node"></div>
        </div>
    </div>

    {{-- STATUS SISTEM --}}
    <div class="sidebar-status">
        <div class="status-online">
            <span class="online-dot"></span>
            <span>Sistem Berjalan</span>
        </div>

        <p>Monitoring otomatis menggunakan Laravel Scheduler.</p>

        @if(isset($latestMeasurement) && $latestMeasurement)
            <p class="sidebar-last-update">
                Terakhir:
                {{ $latestMeasurement->measured_at
                    ? $latestMeasurement->measured_at->format('d M Y H:i:s')
                    : 'Waktu tidak tersedia' }}
            </p>
        @else
            <p class="sidebar-last-update">
                Belum ada data monitoring.
            </p>
        @endif
    </div>

    {{-- AKUN PENGGUNA --}}
    <div class="sidebar-user">
        <div class="sidebar-user-avatar" aria-hidden="true">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>

        <div class="sidebar-user-copy">
            <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
            <span>
                @if(auth()->user()?->isAdmin())
                    Administrator
                @else
                    {{ auth()->user()?->school?->nama_sekolah ?? 'Sekolah' }}
                @endif
            </span>
        </div>

        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" class="sidebar-logout" title="Keluar" aria-label="Keluar">
                ⏻
            </button>
        </form>
    </div>
</aside>
