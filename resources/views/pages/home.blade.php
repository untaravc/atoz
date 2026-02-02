<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HandlingJogja</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="relative overflow-hidden bg-[radial-gradient(circle_at_top,rgba(30,64,175,0.12),transparent_55%),radial-gradient(circle_at_bottom,rgba(37,99,235,0.14),transparent_55%)]">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-24 right-0 h-72 w-72 rounded-full bg-emerald-500/20 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl"></div>
        </div>

        <header class="relative z-10">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-8">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-800 to-blue-600 text-white font-bold">HJ</div>
                    <div>
                        <p class="text-lg font-semibold">HandlingJogja</p>
                        <p class="text-xs text-slate-600">Pengiriman cepat area Jawa Tengah & DIY</p>
                    </div>
                </div>
                <nav class="hidden items-center gap-8 text-sm text-slate-600 md:flex">
                    <a class="hover:text-blue-900" href="#services">Layanan</a>
                    <a class="hover:text-blue-900" href="#tracking">Tracking</a>
                    <a class="hover:text-blue-900" href="#coverage">Jangkauan</a>
                    <a class="hover:text-blue-900" href="#contact">Kontak</a>
                </nav>
                <button class="rounded-full border border-blue-300 px-5 py-2 text-sm font-semibold text-blue-800 hover:border-blue-400 hover:text-blue-900">Minta Estimasi</button>
            </div>
        </header>

        <main class="relative z-10">
            <section class="mx-auto grid max-w-6xl gap-12 px-6 pb-20 pt-12 lg:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-xs uppercase tracking-[0.2em] text-blue-800">Pengiriman andal harian</p>
                    <h1 class="text-4xl font-semibold leading-tight md:text-5xl">Kirim paket cepat dan aman untuk area Jogja dan sekitarnya.</h1>
                    <p class="mt-5 text-lg text-slate-600">HandlingJogja melayani pengiriman paket, dokumen, dan barang usaha dengan pantauan status yang jelas dan armada lokal berpengalaman.</p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <button class="rounded-full bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-3 text-sm font-semibold text-white hover:from-blue-700 hover:to-blue-500">Jadwalkan Pickup</button>
                        <button class="rounded-full border border-blue-200 px-6 py-3 text-sm font-semibold text-blue-800 hover:border-blue-300">Lihat Rute</button>
                    </div>
                    <div class="mt-10 grid gap-4 md:grid-cols-3">
                        <div class="rounded-2xl border border-blue-200 bg-white p-4 shadow-sm">
                            <p class="text-2xl font-semibold">98%</p>
                            <p class="text-sm text-slate-600">Tepat waktu pengiriman</p>
                        </div>
                        <div class="rounded-2xl border border-blue-200 bg-white p-4 shadow-sm">
                            <p class="text-2xl font-semibold">4</p>
                            <p class="text-sm text-slate-600">Kota jangkauan utama</p>
                        </div>
                        <div class="rounded-2xl border border-blue-200 bg-white p-4 shadow-sm">
                            <p class="text-2xl font-semibold">24/7</p>
                            <p class="text-sm text-slate-600">Layanan status paket</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl border border-blue-200 bg-white p-8 shadow-xl">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-800">Status Pengiriman</p>
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs text-blue-800">Aktif</span>
                    </div>
                    <div class="mt-6 space-y-5">
                        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                            <p class="text-xs uppercase text-blue-700">Paket berjalan</p>
                            <p class="mt-2 text-xl font-semibold">Dokumen tender proyek</p>
                            <div class="mt-3 flex items-center justify-between text-xs text-slate-600">
                                <span>Asal: Jogja</span>
                                <span>ETA: 2 jam</span>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                            <p class="text-xs uppercase text-blue-700">Pengantaran prioritas</p>
                            <p class="mt-2 text-xl font-semibold">Sparepart mesin produksi</p>
                            <div class="mt-3 flex items-center justify-between text-xs text-slate-600">
                                <span>Rute: Jogja → Solo</span>
                                <span>Status: Berangkat</span>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                            <p class="text-xs uppercase text-blue-700">Pickup berikutnya</p>
                            <p class="mt-2 text-xl font-semibold">Paket UMKM kuliner</p>
                            <div class="mt-3 flex items-center justify-between text-xs text-slate-600">
                                <span>Jadwal: 15:00 WIB</span>
                                <span>Kurir: HJ-08</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="services" class="mx-auto max-w-6xl px-6 pb-20">
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="rounded-3xl border border-blue-200 bg-white p-6 shadow-sm">
                        <p class="text-sm text-blue-700">Same Day</p>
                        <h3 class="mt-3 text-xl font-semibold">Pengiriman cepat hari yang sama</h3>
                        <p class="mt-3 text-sm text-slate-600">Cocok untuk dokumen penting dan paket urgent di dalam kota.</p>
                    </div>
                    <div class="rounded-3xl border border-blue-200 bg-white p-6 shadow-sm">
                        <p class="text-sm text-blue-700">Paket Usaha</p>
                        <h3 class="mt-3 text-xl font-semibold">Armada aman untuk barang usaha</h3>
                        <p class="mt-3 text-sm text-slate-600">Handling rapi, rute terjadwal, dan bukti serah terima.</p>
                    </div>
                    <div class="rounded-3xl border border-blue-200 bg-white p-6 shadow-sm">
                        <p class="text-sm text-blue-700">Antar Kota</p>
                        <h3 class="mt-3 text-xl font-semibold">Rute Jogja - Solo - Semarang</h3>
                        <p class="mt-3 text-sm text-slate-600">Pengiriman lintas kota dengan tracking status yang jelas.</p>
                    </div>
                </div>
            </section>

            <section id="tracking" class="mx-auto max-w-6xl px-6 pb-20">
                <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr]">
                    <div class="rounded-3xl border border-blue-200 bg-blue-50 p-8">
                        <h2 class="text-2xl font-semibold">Lacak paket Anda</h2>
                        <p class="mt-3 text-sm text-slate-600">Masukkan ID tracking untuk melihat status terakhir pengiriman.</p>
                        <form class="mt-6 space-y-4">
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-blue-700">Tracking ID</label>
                                <input type="text" name="tracking" placeholder="HJ-2402-001" class="mt-2 w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-[0.2em] text-blue-700">Email untuk update</label>
                                <input type="email" name="email" placeholder="nama@perusahaan.com" class="mt-2 w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                            </div>
                            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-blue-800 to-blue-600 px-5 py-3 text-sm font-semibold text-white hover:from-blue-700 hover:to-blue-500">Cek Tracking</button>
                        </form>
                        <p class="mt-4 text-xs text-slate-500">Butuh bantuan? Hubungi admin HandlingJogja di 08xx-xxxx-xxxx.</p>
                    </div>
                    <div class="rounded-3xl border border-blue-200 bg-white p-8 shadow-sm">
                        <h3 class="text-xl font-semibold">Alur tracking</h3>
                        <div class="mt-6 space-y-4">
                            <div class="flex items-start gap-4">
                                <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-800">01</div>
                                <div>
                                    <p class="text-base font-semibold">Input paket</p>
                                    <p class="text-sm text-slate-600">Paket diterima dan dicatat ke sistem.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-800">02</div>
                                <div>
                                    <p class="text-base font-semibold">Proses antar</p>
                                    <p class="text-sm text-slate-600">Kurir membawa paket sesuai rute yang dipilih.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-800">03</div>
                                <div>
                                    <p class="text-base font-semibold">Update status</p>
                                    <p class="text-sm text-slate-600">Status dikirim berkala sampai paket diterima.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-800">04</div>
                                <div>
                                    <p class="text-base font-semibold">Selesai</p>
                                    <p class="text-sm text-slate-600">Paket diterima dan bukti serah terima tercatat.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="coverage" class="mx-auto max-w-6xl px-6 pb-20">
                <div class="rounded-3xl border border-blue-200 bg-white p-8 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-6">
                        <div>
                            <h2 class="text-2xl font-semibold">Jangkauan area layanan</h2>
                            <p class="mt-3 text-sm text-slate-600">Pengiriman fokus area Jogja dan kota sekitar dengan jadwal rutin.</p>
                        </div>
                        <button class="rounded-full border border-blue-200 px-6 py-3 text-sm font-semibold text-blue-800 hover:border-blue-300">Minta Rute</button>
                    </div>
                    <div class="mt-8 grid gap-4 md:grid-cols-4">
                        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                            <p class="text-xs uppercase text-blue-700">Kota</p>
                            <p class="mt-2 text-lg font-semibold">Jogja</p>
                        </div>
                        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                            <p class="text-xs uppercase text-blue-700">Kota</p>
                            <p class="mt-2 text-lg font-semibold">Solo</p>
                        </div>
                        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                            <p class="text-xs uppercase text-blue-700">Kota</p>
                            <p class="mt-2 text-lg font-semibold">Semarang</p>
                        </div>
                        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                            <p class="text-xs uppercase text-blue-700">Kota</p>
                            <p class="mt-2 text-lg font-semibold">Purworejo</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer id="contact" class="relative z-10 border-t border-blue-200">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-6 px-6 py-10">
                <div>
                    <p class="text-lg font-semibold">HandlingJogja</p>
                    <p class="text-sm text-slate-600">handlingjogja@email.com · 08xx-xxxx-xxxx</p>
                </div>
                <div class="text-sm text-slate-500">© 2026 HandlingJogja. Semua pengiriman aman.</div>
            </div>
        </footer>
    </div>
</body>
</html>
