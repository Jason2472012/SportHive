<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Akun' }} · {{ config('app.name', 'Laravel') }}</title>
    <style>
        :root { color-scheme: light; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; color: #17231f; background: #f2f5f1; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: radial-gradient(circle at 12% 8%, #dcefe2 0, transparent 34%), #f2f5f1; }
        .page { min-height: 100vh; display: grid; place-items: center; padding: 32px 18px; }
        .card { width: min(100%, 440px); padding: 38px; border: 1px solid #e1e8e1; border-radius: 24px; background: #fff; box-shadow: 0 22px 70px rgb(24 48 35 / 9%); }
        .brand { display: flex; align-items: center; gap: 11px; margin-bottom: 30px; color: #1d6b4b; font-size: 14px; font-weight: 750; letter-spacing: .04em; }
        .brand-mark { display: grid; width: 38px; height: 38px; place-items: center; border-radius: 12px; background: #e2f3e8; font-size: 19px; }
        h1 { margin: 0; font-size: 29px; letter-spacing: -.04em; }
        .intro { margin: 9px 0 27px; color: #68766e; font-size: 14px; line-height: 1.6; }
        .fields { display: grid; gap: 17px; }
        label { display: block; margin-bottom: 7px; color: #35443b; font-size: 13px; font-weight: 650; }
        input { width: 100%; min-height: 47px; padding: 0 13px; border: 1px solid #d9e1da; border-radius: 11px; outline: none; background: #fff; color: #17231f; font: inherit; font-size: 14px; }
        input:focus { border-color: #33966a; box-shadow: 0 0 0 3px rgb(51 150 106 / 13%); }
        .button { width: 100%; min-height: 48px; margin-top: 7px; border: 0; border-radius: 11px; background: #18744d; color: #fff; cursor: pointer; font: inherit; font-size: 14px; font-weight: 700; }
        .button:hover { background: #115c3c; }
        .foot { margin: 22px 0 0; color: #68766e; text-align: center; font-size: 13px; }
        a { color: #18744d; font-weight: 700; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .alert { margin-bottom: 19px; padding: 12px 14px; border: 1px solid #f1c7c3; border-radius: 10px; background: #fff4f2; color: #9a3027; font-size: 13px; line-height: 1.5; }
        .field-error { margin: 6px 0 0; color: #b42318; font-size: 12px; }
        .dashboard { width: min(100%, 720px); }
        .dashboard-head { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .dashboard p { color: #68766e; line-height: 1.6; }
        .role { display: inline-block; margin-top: 10px; padding: 7px 11px; border-radius: 999px; background: #e2f3e8; color: #1d6b4b; font-size: 12px; font-weight: 750; }
        .logout { padding: 10px 14px; border: 1px solid #d9e1da; border-radius: 10px; background: #fff; color: #35443b; cursor: pointer; font: inherit; font-size: 13px; font-weight: 650; }
        @media (max-width: 480px) { .card { padding: 29px 23px; border-radius: 19px; } h1 { font-size: 26px; } .dashboard-head { align-items: flex-start; flex-direction: column; } }
    </style>
</head>
<body>
    <main class="page">
        @yield('content')
    </main>
</body>
</html>
