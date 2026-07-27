<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docs API | Stock ATK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900">
    <nav class="bg-white border-b border-slate-100">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-extrabold text-blue-600 text-xl">Stock ATK</a>
            <div class="flex gap-5 text-sm font-bold">
                <a href="{{ url('/') }}" class="text-slate-600 hover:text-blue-600">Home</a>
                <a href="{{ route('login') }}" class="text-slate-600 hover:text-blue-600">Masuk</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-10">
        <div class="mb-8">
            <h1 class="text-4xl font-extrabold mb-3">Dokumentasi API Stock ATK</h1>
            <p class="text-slate-600">Base URL lokal: <code class="bg-white px-2 py-1 rounded border">{{ url('/api') }}</code></p>
        </div>

        <section class="bg-white border rounded-2xl p-6 mb-6">
            <h2 class="text-2xl font-bold mb-3">Autentikasi</h2>
            <p class="text-slate-600 mb-4">Endpoint private memakai Bearer Token dari API login. Akun hasil register API berstatus <strong>inactive</strong> dan harus diaktifkan admin lewat menu Pengguna di web panel.</p>
            <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 overflow-auto mb-3"><code>POST /api/register
{
  "name": "Demo User",
  "email": "demo@example.com",
  "password": "password",
  "password_confirmation": "password"
}</code></pre>
            <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 overflow-auto mb-3"><code>POST /api/login
{
  "email": "admin@example.com",
  "password": "password",
  "device_name": "postman"
}</code></pre>
            <pre class="bg-slate-900 text-slate-100 rounded-xl p-4 overflow-auto"><code>Authorization: Bearer {access_token}</code></pre>
        </section>

        <section class="bg-white border rounded-2xl p-6 mb-6">
            <h2 class="text-2xl font-bold mb-3">Data Barang</h2>
            <p class="text-slate-600 mb-4">User aktif bisa melihat data barang. Tambah, edit, hapus mengikuti permission role seperti di web panel.</p>
            <ul class="space-y-2 text-sm">
                <li><code>GET /api/barang?search=pulpen&per_page=10</code></li>
                <li><code>GET /api/barang/{id}</code></li>
                <li><code>POST /api/barang</code> body: <code>{"kode":"ATK-009","nama":"Penghapus","stok":20}</code></li>
                <li><code>PUT /api/barang/{id}</code> body: <code>{"kode":"ATK-009","nama":"Penghapus Putih","stok":25}</code></li>
                <li><code>DELETE /api/barang/{id}</code> hanya bisa jika belum punya transaksi.</li>
            </ul>
        </section>

        <section class="bg-white border rounded-2xl p-6 mb-6">
            <h2 class="text-2xl font-bold mb-3">Barang Masuk</h2>
            <p class="text-slate-600 mb-4">Sesuai alur web, barang masuk untuk role yang punya permission. Saat dibuat, stok barang otomatis bertambah.</p>
            <ul class="space-y-2 text-sm">
                <li><code>GET /api/barang-masuk?per_page=10</code></li>
                <li><code>POST /api/barang-masuk</code> body: <code>{"barang_id":1,"jumlah":10,"tanggal":"2026-07-27"}</code></li>
            </ul>
        </section>

        <section class="bg-white border rounded-2xl p-6 mb-6">
            <h2 class="text-2xl font-bold mb-3">Barang Keluar</h2>
            <p class="text-slate-600 mb-4">User membuat permintaan barang keluar dengan status <strong>pending</strong>. Admin memproses approve/reject. Stok dipotong hanya saat approve.</p>
            <ul class="space-y-2 text-sm">
                <li><code>GET /api/barang-keluar?per_page=10</code></li>
                <li><code>POST /api/barang-keluar</code> body: <code>{"barang_id":1,"jumlah":2,"tanggal":"2026-07-27"}</code></li>
                <li><code>POST /api/barang-keluar/{id}/approve</code></li>
                <li><code>POST /api/barang-keluar/{id}/reject</code></li>
            </ul>
        </section>

        <section class="bg-white border rounded-2xl p-6">
            <h2 class="text-2xl font-bold mb-3">Postman</h2>
            <p class="text-slate-600">Import file <code class="bg-slate-100 px-2 py-1 rounded">postman_collection.json</code> dari root project. Request Login Admin akan menyimpan token otomatis ke variable collection.</p>
        </section>
    </main>
</body>
</html>
