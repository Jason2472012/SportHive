<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'Temukan lapangan dan teman bermain olahraga di dekatmu.')">
    <title>@yield('title', config('app.name', 'Sports Booking'))</title>
    <style>
        :root {
            --primary: #16834a;
            --primary-dark: #0f5f37;
            --ink: #14251c;
            --muted: #69786f;
            --soft: #f3f7f3;
            --border: #e1e9e2;
            --radius: 18px;
            --shadow: 0 12px 32px rgb(15 45 28 / 9%);
            --font: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: white;
            color: var(--ink);
            font-family: var(--font);
            line-height: 1.6;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button {
            font: inherit;
        }

        .container {
            width: min(1140px, 92%);
            margin-inline: auto;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 20;
            border-bottom: 1px solid var(--border);
            background: rgb(255 255 255 / 94%);
            backdrop-filter: blur(10px);
        }

        .navbar {
            display: flex;
            min-height: 72px;
            align-items: center;
            justify-content: space-between;
            gap: 22px;
        }

        .logo {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 850;
            letter-spacing: -.04em;
        }

        .nav-toggle {
            display: none;
            border: 0;
            background: transparent;
            cursor: pointer;
            font-size: 1.6rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .nav-links a:not(.button):hover {
            color: var(--primary);
        }

        .nav-links form {
            margin: 0;
        }

        .button {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border: 1px solid transparent;
            border-radius: 999px;
            background: var(--primary);
            color: white;
            cursor: pointer;
            font-weight: 700;
            transition: background .2s, transform .2s;
        }

        .button:hover {
            transform: translateY(-1px);
            background: var(--primary-dark);
        }

        .button-outline {
            border-color: var(--primary);
            background: transparent;
            color: var(--primary);
        }

        .button-outline:hover {
            color: white;
        }

        .button-quiet {
            min-height: auto;
            padding: 0;
            border: 0;
            background: transparent;
            color: var(--ink);
        }

        .button-quiet:hover {
            transform: none;
            background: transparent;
            color: var(--primary);
        }

        .hero {
            padding: 88px 0 110px;
            background: linear-gradient(130deg, #0b3823, var(--primary-dark) 54%, var(--primary));
            color: white;
        }

        .eyebrow {
            margin: 0;
            color: var(--primary);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .hero .eyebrow {
            color: #b9f1ca;
        }

        h1, h2, h3, p {
            margin-top: 0;
        }

        h1 {
            max-width: 760px;
            margin-bottom: 14px;
            font-size: clamp(2.3rem, 6vw, 4.1rem);
            line-height: 1.08;
            letter-spacing: -.055em;
        }

        h2 {
            margin-bottom: 12px;
            font-size: clamp(1.65rem, 3.4vw, 2.45rem);
            line-height: 1.2;
            letter-spacing: -.04em;
        }

        h3 {
            margin-bottom: 4px;
            font-size: 1.1rem;
        }

        .hero-copy {
            max-width: 650px;
            margin-bottom: 0;
            color: #d8f5e1;
            font-size: 1.05rem;
        }

        .search-panel {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
            align-items: end;
            gap: 12px;
            margin-top: 34px;
            padding: 18px;
            border-radius: var(--radius);
            background: white;
            box-shadow: var(--shadow);
            color: var(--ink);
        }

        .field label {
            display: block;
            margin-bottom: 5px;
            color: var(--muted);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .field input,
        .field select {
            width: 100%;
            min-height: 46px;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: white;
            color: var(--ink);
            font: inherit;
        }

        .categories {
            position: relative;
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
            margin-top: -55px;
        }

        .category {
            display: grid;
            min-height: 108px;
            place-content: center;
            gap: 4px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: white;
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform .2s, border-color .2s;
        }

        .category:hover {
            transform: translateY(-4px);
            border-color: var(--primary);
        }

        .category-mark {
            color: var(--primary);
            font-size: 1.65rem;
            font-weight: 850;
        }

        .section {
            padding: 76px 0;
        }

        .section-soft {
            background: var(--soft);
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 50px;
        }

        .muted,
        .meta {
            color: var(--muted);
        }

        .stats {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
            margin-top: 24px;
        }

        .stat strong {
            display: block;
            color: var(--primary);
            font-size: 1.7rem;
        }

        .placeholder {
            display: grid;
            min-height: 300px;
            place-items: center;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: linear-gradient(135deg, #e7f1e9, #cbdccf);
            color: #506c59;
            text-align: center;
        }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 28px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 22px;
        }

        .card {
            display: flex;
            overflow: hidden;
            flex-direction: column;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: white;
            box-shadow: var(--shadow);
        }

        .card-image {
            display: grid;
            min-height: 155px;
            place-items: center;
            background: linear-gradient(135deg, #e4efe5, #c6d8ca);
            color: #55705d;
        }

        .card-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 9px;
            padding: 19px;
        }

        .tag {
            display: inline-flex;
            align-self: start;
            padding: 3px 10px;
            border-radius: 999px;
            background: #e1f4e7;
            color: var(--primary-dark);
            font-size: .72rem;
            font-weight: 800;
        }

        .tag-warn {
            background: #fff3d5;
            color: #865a00;
        }

        .meta {
            margin: 0;
            font-size: .88rem;
        }

        .card-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: auto;
            padding-top: 10px;
        }

        .price {
            color: var(--primary-dark);
            font-weight: 850;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-top: 26px;
        }

        .step {
            padding: 23px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: white;
        }

        .step-number {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            margin-bottom: 14px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            font-weight: 800;
        }

        .cta {
            padding: 54px 24px;
            border-radius: 25px;
            background: var(--ink);
            color: white;
            text-align: center;
        }

        .cta p {
            margin: 9px 0 23px;
            color: #d0ddd4;
        }

        .site-footer {
            margin-top: 72px;
            padding: 52px 0 22px;
            background: #0d1912;
            color: #d4dfd7;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.3fr;
            gap: 30px;
        }

        .site-footer h2 {
            margin-bottom: 12px;
            color: white;
            font-size: .9rem;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .site-footer ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .site-footer li {
            margin: 6px 0;
        }

        .site-footer a:hover {
            color: white;
        }

        .copyright {
            margin: 30px 0 0;
            padding-top: 17px;
            border-top: 1px solid #26372c;
            color: #acbdb1;
            font-size: .84rem;
            text-align: center;
        }

        @media (max-width: 900px) {
            .search-panel {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .about-grid,
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 720px) {
            .nav-toggle {
                display: block;
            }
            .nav-links {
                position: absolute;
                top: 71px;
                right: 0;
                left: 0;
                display: none;
                align-items: stretch;
                flex-direction: column;
                gap: 16px;
                padding: 20px 4%;
                border-bottom: 1px solid var(--border);
                background: white;
            }
            .nav-links.is-open {
                display: flex;
            }
            .nav-links li,
            .nav-links form,
            .nav-links .button {
                width: 100%;
            }
            .hero {
                padding: 72px 0 95px;
            }
            .search-panel,
            .about-grid,
            .footer-grid {
                grid-template-columns: 1fr;
            }
            .categories {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .section {
                padding: 58px 0;
            }
            .section-heading {
                align-items: start;
                flex-direction: column;
            }
            .placeholder {
                min-height: 220px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="site-header">
        <div class="container navbar">
            <a class="logo" href="{{ route('home') }}">{{ config('app.name', 'Sports Booking') }}</a>
            <button class="nav-toggle" type="button" aria-label="Buka menu" aria-controls="site-menu" aria-expanded="false">&#9776;</button>
            <ul class="nav-links" id="site-menu">
                <li><a href="{{ route('home') }}#venue">Lapangan</a></li>
                <li><a href="{{ route('home') }}#open-room">Open Room</a></li>
                <li><a href="{{ route('home') }}#cara-kerja">Cara Kerja</a></li>
                <li><a href="{{ route('home') }}#kontak">Kontak</a></li>
                @auth
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="button button-quiet" type="submit">Keluar</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">Masuk</a></li>
                    <li><a class="button" href="{{ route('register') }}">Mulai Sekarang</a></li>
                @endauth
            </ul>
        </div>
    </header>

    <main>@yield('content')</main>

    <footer class="site-footer" id="kontak">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <a class="logo" href="{{ route('home') }}" style="color: white">{{ config('app.name', 'Sports Booking') }}</a>
                    <p class="meta" style="margin-top: 9px; color: #acbdb1">Platform reservasi lapangan dan komunitas pemain olahraga.</p>
                </div>
                <div>
                    <h2>Jelajahi</h2>
                    <ul>
                        <li><a href="{{ route('home') }}#venue">Lapangan</a></li>
                        <li><a href="{{ route('home') }}#open-room">Open Room</a></li>
                    </ul>
                </div>
                <div>
                    <h2>Akun</h2>
                    <ul>
                        <li><a href="{{ route('login') }}">Masuk</a></li>
                        <li><a href="{{ route('register') }}">Daftar</a></li>
                    </ul>
                </div>
                <div>
                    <h2>Kontak</h2>
                    <p class="meta" style="color: #acbdb1">Temukan informasi kontak dan bantuan dari pengelola platform.</p>
                </div>
            </div>
            <p class="copyright">&copy; {{ now()->year }} {{ config('app.name', 'Sports Booking') }}. Hak cipta dilindungi.</p>
        </div>
    </footer>

    <script>
        const menuToggle = document.querySelector('.nav-toggle');
        const siteMenu = document.querySelector('#site-menu');

        menuToggle?.addEventListener('click', () => {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';

            menuToggle.setAttribute('aria-expanded', String(! isExpanded));
            siteMenu?.classList.toggle('is-open', ! isExpanded);
        });
    </script>
    @stack('scripts')
</body>
</html>
