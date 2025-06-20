<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Darah') }}</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Favicon dari Boxicons -->
      <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='red'><path d='M12 18c4 0 7-3 7-7 0-2-1-4-2-6-1-2-5-5-5-5S8 4 7 6c-1 2-2 4-2 6 0 4 3 7 7 7z'/></svg>">

     <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>


<body>
   
<!-- Navbar -->
<header class="navbar" style="border-bottom: 4px solid red; display: flex; align-items: center; padding: 10px 0; background-color: white;">
    <div class="logo-name" style="display: flex; align-items: center;">
        <i class="bx bxs-droplet" style="color: red; margin-left: 10px; font-size: 1.5rem;"></i>
        <span class="logo-text" style="margin-left: 10px; font-weight: bold; color: #333; font-size: 1.2rem;">Pantau Darah .</span>
    </div>
    
    @auth
    <div class="bx bx-user" style="margin-left: auto; margin-right: 20px; display: flex; align-items: center; gap: 8px;">
        {{-- <i class='bx bx-user-circle' style="color: #555; font-size: 1.4rem;"></i> --}}
        <span style="color: #6e6464; font-weight: 500; font-size: 0.95rem;">{{ Auth::user()->name }}</span>
    </div>
    @endauth
</header>

    <div class="main-layout">
        <!-- Sidebar -->
        <aside class="sidebar always-show">
            @if (Auth::user()->role == 'admin')
                <ul class="lists">
                    <li class="list">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="link">Beranda</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="{{ route('admin.permintaan') }}" class="nav-link">
                            <i class="bx bx-history icon"></i>
                            <span class="link">Data Historis</span>
                        </a>
                    </li>
                    {{-- <li class="list">
            <a href="{{ route('admin.permintaan') }}" class="nav-link">
              <i class="bx bx-home-alt icon"></i>
              <span class="link">Tambah Data</span>
            </a>
          </li> --}}
                    <li class="list">
                        <a href="{{ route('admin.prediksi.index') }}" class="nav-link">
                            <i class="bx bx-bar-chart-alt icon"></i>
                            <span class="link">Prediksi</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="{{ route('admin.optimasi') }}" class="nav-link">
                            <i class="bx bx-rocket icon"></i>
                            <span class="link">Optimasi</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="{{ route('admin.pengujian.index') }}" class="nav-link">
                            <i class="bx bx-check-shield icon"></i>
                            <span class="link">Pengujian</span>
                        </a>
                    </li>
                    {{-- <li class="list">
            <a href="{{ route('admin.users') }}" class="nav-link">
              <i class="bx bx-home-alt icon"></i>
              <span class="link">Kelola Pengguna</span>
            </a>
          </li> --}}
                </ul>
            @elseif(Auth::user()->role == 'petugas')
                <ul class="lists">
                    <li class="list">
                        <a href="{{ route('petugas.dashboard') }}" class="nav-link">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="link">Dashboard</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="{{ route('petugas.permintaan') }}" class="nav-link">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="link">Data Historis</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="{{ route('petugas.prediksi') }}" class="nav-link">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="link">Prediksi</span>
                        </a>
                    </li>
                </ul>
            @endif

            <ul class="lists bottom">
                <li class="list">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="nav-link"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="bx bx-log-out icon"></i>
                            <span class="link">Logout</span>
                        </a>
                    </form>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    @stack('scripts')
</body>

</html>
