<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Stock ATK | Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
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

        .app-sidebar a,
        .app-mobile-sidebar a {
            text-decoration: none !important;
        }

        .app-sidebar-link {
            align-items: center;
            border-radius: 0.75rem;
            display: flex;
            font-weight: 600;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            transition: background-color .15s ease, color .15s ease;
        }

        .app-sidebar-link i {
            text-align: center;
            width: 1.25rem;
        }

        .dt-container .dt-search input,
        .dt-container .dt-length select {
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            padding: 0.45rem 0.75rem;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-slate-50 flex min-h-screen">
    @auth
    <aside class="app-sidebar w-64 bg-white border-r border-slate-200 hidden md:flex flex-col shrink-0">
        <div class="p-6 text-center md:text-left">
            <div class="flex items-center gap-2 font-bold text-xl text-blue-600">
                <i class="fa-solid fa-boxes-stacked"></i> Stock ATK
            </div>
        </div>
        <nav class="px-4 flex-1 space-y-1">
            <a href="{{ route('dashboard') }}" class="app-sidebar-link {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}"><i class="fa-solid fa-chart-simple"></i> Dashboard</a>
            <a href="{{ route('barang.index') }}" class="app-sidebar-link {{ request()->routeIs('barang.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}"><i class="fa-solid fa-box"></i> Data Barang</a>
            @if(auth()->user()->role == 'admin')
            <a href="{{ route('barang_masuk.index') }}" class="app-sidebar-link {{ request()->routeIs('barang_masuk.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}"><i class="fa-solid fa-right-to-bracket"></i> Barang Masuk</a>
            @endif
            <a href="{{ route('barang_keluar.index') }}" class="app-sidebar-link {{ request()->routeIs('barang_keluar.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}"><i class="fa-solid fa-right-from-bracket"></i> Barang Keluar</a>
            @can('view', \App\Models\User::class)
            <a href="{{ route('users.index') }}" class="app-sidebar-link {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}"><i class="fa-solid fa-users"></i> Pengguna</a>
            @endcan
            @can('view', \App\Models\Role::class)
            <a href="{{ route('roles.index') }}" class="app-sidebar-link {{ request()->routeIs('roles.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}"><i class="fa-solid fa-shield-halved"></i> Role Akses</a>
            @endcan
        </nav>
        <div class="p-4 border-t border-slate-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="app-sidebar-link w-full border-0 bg-transparent text-red-500 hover:bg-red-50"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar Aplikasi</button>
            </form>
        </div>
    </aside>

    <div class="md:hidden fixed top-0 left-0 right-0 bg-white border-b border-slate-200 z-50 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2 font-bold text-blue-600"><i class="fa-solid fa-boxes-stacked"></i> Stock ATK</div>
        <button id="mobileMenuBtn" class="text-slate-600 text-2xl p-1" onclick="toggleMobileSidebar()"><i class="fa-solid fa-bars"></i></button>
    </div>

    <div id="mobileSidebar" class="md:hidden fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/30" onclick="toggleMobileSidebar()"></div>
        <aside class="app-mobile-sidebar relative w-64 bg-white h-full p-4 overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <span class="font-bold text-lg text-blue-600"><i class="fa-solid fa-boxes-stacked"></i> Stock ATK</span>
                <button onclick="toggleMobileSidebar()" class="text-slate-400 text-xl"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" class="app-sidebar-link {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}" onclick="toggleMobileSidebar()"><i class="fa-solid fa-chart-simple"></i> Dashboard</a>
                <a href="{{ route('barang.index') }}" class="app-sidebar-link {{ request()->routeIs('barang.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}" onclick="toggleMobileSidebar()"><i class="fa-solid fa-box"></i> Data Barang</a>
                @if(auth()->user()->role == 'admin')
                <a href="{{ route('barang_masuk.index') }}" class="app-sidebar-link {{ request()->routeIs('barang_masuk.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}" onclick="toggleMobileSidebar()"><i class="fa-solid fa-right-to-bracket"></i> Barang Masuk</a>
                @endif
                <a href="{{ route('barang_keluar.index') }}" class="app-sidebar-link {{ request()->routeIs('barang_keluar.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}" onclick="toggleMobileSidebar()"><i class="fa-solid fa-right-from-bracket"></i> Barang Keluar</a>
                @can('view', \App\Models\User::class)
                <a href="{{ route('users.index') }}" class="app-sidebar-link {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}" onclick="toggleMobileSidebar()"><i class="fa-solid fa-users"></i> Pengguna</a>
                @endcan
                @can('view', \App\Models\Role::class)
                <a href="{{ route('roles.index') }}" class="app-sidebar-link {{ request()->routeIs('roles.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}" onclick="toggleMobileSidebar()"><i class="fa-solid fa-shield-halved"></i> Role Akses</a>
                @endcan
            </nav>
            <div class="mt-6 pt-4 border-t border-slate-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="app-sidebar-link w-full border-0 bg-transparent text-red-500 hover:bg-red-50"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</button>
                </form>
            </div>
        </aside>
    </div>
    @endauth

    <main class="flex-1 {{ Auth::check() ? 'pt-14 md:pt-0 p-4 md:p-8' : 'min-h-screen flex items-center justify-center' }}">
        @auth
        <div class="max-w-7xl mx-auto">
            @endauth
            @yield('content')
            @auth
        </div>
        @endauth
    </main>

    @if(session('alert'))
    <script>
        Swal.fire({
            icon: "{{ session('alert')['type'] }}",
            title: "{{ session('alert')['title'] }}",
            text: "{{ session('alert')['text'] }}",
            timer: "{{ session('alert')['type'] === 'error' ? 3000 : 2500 }}",
            showConfirmButton: false
        });
    </script>
    @endif

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <script>
        function toggleMobileSidebar() {
            document.getElementById('mobileSidebar').classList.toggle('hidden');
        }

        document.addEventListener('input', function (event) {
            if (!event.target.classList.contains('only-whole-number')) {
                return;
            }

            const input = event.target;
            const min = Number(input.getAttribute('min') || 0);
            const value = input.value;
            const isValid = /^\d+$/.test(value) && Number(value) >= min;

            if (value !== '' && !isValid) {
                input.value = value.replace(/[^\d]/g, '');
                Swal.fire({
                    icon: 'warning',
                    title: 'Input tidak valid',
                    text: 'Kolom ini hanya menerima angka bulat positif. Minus dan desimal tidak diperbolehkan.',
                    timer: 2200,
                    showConfirmButton: false
                });
            }
        });

        document.addEventListener('keydown', function (event) {
            if (!event.target.classList.contains('only-whole-number')) {
                return;
            }

            if (['-', '+', '.', ',', 'e', 'E'].includes(event.key)) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Hanya angka saja',
                    text: 'Minus dan desimal tidak diperbolehkan.',
                    timer: 1800,
                    showConfirmButton: false
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
