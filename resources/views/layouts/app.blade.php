<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Stock ATK | Dashboard')</title>
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Bootstrap 5 (for content compatibility) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .card {
            border-radius: 1rem;
        }

        .table {
            margin-bottom: 0;
        }

        .form-control,
        .form-select {
            border-radius: 0.75rem;
        }
    </style>
</head>

<body class="bg-slate-50 flex min-h-screen">

    @auth
    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white border-r border-slate-200 hidden md:flex flex-col shrink-0">
        <div class="p-6 text-center md:text-left">
            <div class="flex items-center gap-2 font-bold text-xl text-blue-600">
                <span>📦</span> Stock ATK
            </div>
        </div>
        <nav class="px-4 flex-1 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-100' }} rounded-xl transition font-medium">📊 Dashboard</a>
            <a href="{{ route('barang.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('barang.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-100' }} rounded-xl transition font-medium">📦 Data Barang</a>
            @if(auth()->user()->role == 'admin')
            <a href="{{ route('barang_masuk.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('barang_masuk.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-100' }} rounded-xl transition font-medium">📥 Barang Masuk</a>
            @endif
            <a href="{{ route('barang_keluar.index') }}" class="flex items-center justify-between px-4 py-3 {{ request()->routeIs('barang_keluar.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-100' }} rounded-xl transition font-medium">
                <span class="flex items-center gap-3">📤 Barang Keluar</span>
            </a>
            @can('view', \App\Models\User::class)
            <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-100' }} rounded-xl transition font-medium">👥 Pengguna</a>
            <a href="{{ route('roles.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('roles.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-100' }} rounded-xl transition font-medium">🛡️ Role Akses</a>
            @endcan
        </nav>
        <div class="p-4 border-t border-slate-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-2 w-full text-red-500 hover:bg-red-50 rounded-lg transition text-sm font-semibold">🚪 Keluar Aplikasi</button>
            </form>
        </div>
    </aside>

    {{-- MOBILE HEADER --}}
    <div class="md:hidden fixed top-0 left-0 right-0 bg-white border-b border-slate-200 z-50 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2 font-bold text-blue-600"><span>📦</span> Stock ATK</div>
        <button id="mobileMenuBtn" class="text-slate-600 text-2xl p-1" onclick="toggleMobileSidebar()">☰</button>
    </div>

    {{-- MOBILE SIDEBAR --}}
    <div id="mobileSidebar" class="md:hidden fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/30" onclick="toggleMobileSidebar()"></div>
        <aside class="relative w-64 bg-white h-full p-4 overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <span class="font-bold text-lg text-blue-600">📦 Stock ATK</span>
                <button onclick="toggleMobileSidebar()" class="text-slate-400 text-xl">✕</button>
            </div>
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-100' }}" onclick="toggleMobileSidebar()">📊 Dashboard</a>
                <a href="{{ route('barang.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium {{ request()->routeIs('barang.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-100' }}" onclick="toggleMobileSidebar()">📦 Data Barang</a>
                @if(auth()->user()->role == 'admin')
                <a href="{{ route('barang_masuk.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium {{ request()->routeIs('barang_masuk.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-100' }}" onclick="toggleMobileSidebar()">📥 Barang Masuk</a>
                @endif
                <a href="{{ route('barang_keluar.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium {{ request()->routeIs('barang_keluar.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-100' }}" onclick="toggleMobileSidebar()">📤 Barang Keluar</a>
                @can('view', \App\Models\User::class)
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-100' }}" onclick="toggleMobileSidebar()">👥 Pengguna</a>
                @endcan
                @can('view', \App\Models\Role::class)
                <a href="{{ route('roles.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition font-medium {{ request()->routeIs('roles.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-600 hover:bg-slate-100' }}" onclick="toggleMobileSidebar()">🛡️ Role Akses</a>
                @endcan
            </nav>
            <div class="mt-6 pt-4 border-t border-slate-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-2 w-full text-red-500 hover:bg-red-50 rounded-lg transition text-sm font-semibold">🚪 Keluar</button>
                </form>
            </div>
        </aside>
    </div>
    @endauth

    {{-- MAIN CONTENT --}}
    <main class="flex-1 {{ Auth::check() ? 'pt-14 md:pt-0 p-4 md:p-8' : 'min-h-screen flex items-center justify-center' }}">
        @auth
        <div class="max-w-7xl mx-auto">
            @endauth
            @yield('content')
            @auth
        </div>
        @endauth
    </main>

    {{-- SweetAlert Notifications --}}
    @if(session('alert'))
    <script>
        Swal.fire({
            icon: "{{ session('alert')['type'] }}",
            title: "{{ session('alert')['title'] }}",
            text: "{{ session('alert')['text'] }}",
            timer: {
                {
                    session('alert')['type'] === 'error' ? 3000 : 2500
                }
            },
            showConfirmButton: false
        });
    </script>
    @endif

    {{-- Bootstrap JS (for modals, etc.) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleMobileSidebar() {
            document.getElementById('mobileSidebar').classList.toggle('hidden');
        }
    </script>

    @stack('scripts')
</body>

</html>