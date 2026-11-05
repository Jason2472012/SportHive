@extends('layouts.template-home')

@section('title', 'Booking Lapangan dan Cari Teman Main')
@section('description', 'Temukan lapangan olahraga dan teman bermain di dekatmu.')

@section('content')
    <section class="hero">
        <div class="container">
            <p class="eyebrow">Cari lapangan kamu</p>
            <h1>Booking lapangan dalam kurang dari 5 menit</h1>
            <p class="hero-copy">Pilih lapangan, cek jadwal, lalu ajak pemain lain lewat Open Room.</p>

            <form class="search-panel" action="{{ route('home') }}" method="GET">
                <div class="field">
                    <label for="sport">Olahraga</label>
                    <select id="sport" name="sport_id">
                        <option value="">Semua olahraga</option>
                        <option value="1">Futsal</option>
                        <option value="2">Badminton</option>
                        <option value="3">Basket</option>
                        <option value="4">Tenis</option>
                        <option value="5">Mini soccer</option>
                    </select>
                </div>
                <div class="field">
                    <label for="city">Lokasi</label>
                    <select id="city" name="city">
                        <option value="">Semua lokasi</option>
                        <option>Bandung</option>
                        <option>Jakarta</option>
                        <option>Surabaya</option>
                    </select>
                </div>
                <div class="field">
                    <label for="date">Tanggal</label>
                    <input id="date" type="date" name="date">
                </div>
                <div class="field">
                    <label for="time">Jam</label>
                    <input id="time" type="time" name="time">
                </div>
                <a class="button" href="#venue">Cari Lapangan</a>
            </form>
        </div>
    </section>

    <div class="container">
        <nav class="categories" aria-label="Kategori olahraga">
            <a class="category" href="#venue"><span class="category-mark">F</span><strong>Futsal</strong></a>
            <a class="category" href="#venue"><span class="category-mark">B</span><strong>Badminton</strong></a>
            <a class="category" href="#venue"><span class="category-mark">B</span><strong>Basket</strong></a>
            <a class="category" href="#venue"><span class="category-mark">T</span><strong>Tenis</strong></a>
            <a class="category" href="#venue"><span class="category-mark">M</span><strong>Mini soccer</strong></a>
        </nav>
    </div>

    <section class="section">
        <div class="container about-grid">
            <div>
                <p class="eyebrow">Tentang kami</p>
                <h2>Platform untuk pecinta olahraga</h2>
                <p class="muted">Temukan dan pesan venue olahraga langsung dari website. Sudah punya lapangan tapi kurang pemain? Buat Open Room dan cari teman main dengan level yang cocok.</p>
                <div class="stats">
                    <div class="stat"><strong>50+</strong><span class="muted">Venue</span></div>
                    <div class="stat"><strong>5</strong><span class="muted">Cabang olahraga</span></div>
                    <div class="stat"><strong>1.000+</strong><span class="muted">Pemain</span></div>
                </div>
            </div>
            <div class="placeholder" role="img" aria-label="Area foto venue olahraga">Foto atau ilustrasi venue</div>
        </div>
    </section>

    <section class="section section-soft" id="venue">
        <div class="container">
            <div class="section-heading">
                <div><p class="eyebrow">Venue populer</p><h2>Lapangan pilihan untuk kamu</h2></div>
                <a href="{{ route('register') }}" class="button button-outline">Lihat Semua</a>
            </div>
            <div class="card-grid">
                <article class="card">
                    <div class="card-image">Foto venue</div>
                    <div class="card-body"><span class="tag">Futsal</span><h3>Lapangan Futsal A</h3><p class="meta">Bandung · Buka 08.00–23.00</p><div class="card-row"><span class="price">Rp120.000 <small>/ jam</small></span><a class="button" href="{{ route('login') }}">Pesan</a></div></div>
                </article>
                <article class="card">
                    <div class="card-image">Foto venue</div>
                    <div class="card-body"><span class="tag">Badminton</span><h3>Gelanggang B</h3><p class="meta">Bandung · Buka 07.00–22.00</p><div class="card-row"><span class="price">Rp60.000 <small>/ jam</small></span><a class="button" href="{{ route('login') }}">Pesan</a></div></div>
                </article>
                <article class="card">
                    <div class="card-image">Foto venue</div>
                    <div class="card-body"><span class="tag">Basket</span><h3>Lapangan Basket C</h3><p class="meta">Jakarta · Buka 09.00–22.00</p><div class="card-row"><span class="price">Rp150.000 <small>/ jam</small></span><a class="button" href="{{ route('login') }}">Pesan</a></div></div>
                </article>
            </div>
        </div>
    </section>

    <section class="section" id="open-room">
        <div class="container">
            <div class="section-heading">
                <div><p class="eyebrow">Open Room</p><h2>Cari teman main sekarang</h2><p class="muted">Contoh sesi yang masih mencari pemain.</p></div>
                <a href="{{ route('register') }}" class="button button-outline">Jelajahi Room</a>
            </div>
            <div class="card-grid">
                <article class="card"><div class="card-body"><div><span class="tag">Fun match</span> <span class="tag tag-warn">Semua level</span></div><h3>Futsal santai malam</h3><p class="meta">Sabtu, 20.00 · Venue A</p><p class="meta">3 dari 5 slot terisi</p><div class="card-row"><span class="price">Rp25.000 <small>/ orang</small></span><a class="button" href="{{ route('login') }}">Gabung</a></div></div></article>
                <article class="card"><div class="card-body"><div><span class="tag">Turnamen</span> <span class="tag tag-warn">Menengah</span></div><h3>Mini turnamen badminton</h3><p class="meta">Minggu, 09.00 · Venue B</p><p class="meta">2 dari 8 slot terisi</p><div class="card-row"><span class="price">Rp40.000 <small>/ orang</small></span><a class="button" href="{{ route('login') }}">Gabung</a></div></div></article>
                <article class="card"><div class="card-body"><div><span class="tag">Fun match</span> <span class="tag tag-warn">Pemula</span></div><h3>Basket sore santai</h3><p class="meta">Jumat, 16.00 · Venue C</p><p class="meta">4 dari 5 slot terisi</p><div class="card-row"><span class="price">Rp30.000 <small>/ orang</small></span><a class="button" href="{{ route('login') }}">Gabung</a></div></div></article>
            </div>
        </div>
    </section>

    <section class="section section-soft" id="cara-kerja">
        <div class="container">
            <p class="eyebrow">Cara kerja</p><h2>Main bareng dalam empat langkah</h2>
            <div class="steps">
                <article class="step"><span class="step-number">1</span><h3>Cari lapangan</h3><p class="meta">Pilih olahraga, lokasi, tanggal, dan jam.</p></article>
                <article class="step"><span class="step-number">2</span><h3>Pesan dan bayar</h3><p class="meta">Pilih slot yang tersedia untuk bermain.</p></article>
                <article class="step"><span class="step-number">3</span><h3>Buka Open Room</h3><p class="meta">Ajak pemain lain bergabung ke sesi kamu.</p></article>
                <article class="step"><span class="step-number">4</span><h3>Koordinasi dan main</h3><p class="meta">Atur jadwal, bertemu, lalu mulai bermain.</p></article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container"><div class="cta"><h2>Siap main hari ini?</h2><p>Daftar gratis dan temukan lapangan serta teman main di dekatmu.</p><a class="button" href="{{ route('register') }}">Daftar Sekarang</a></div></div>
    </section>
@endsection
