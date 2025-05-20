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


</head>


<body>
    <!-- Navbar -->
    <header class="navbar" style="border-bottom: 4px solid red;">
        <div class="logo-name">
            <i class="bx bxs-droplet" style="color: red; margin-left: 10px;"></i>
            <span class="logo-text">Pantau Darah .</span>
        </div>
    </header>

    <div class="main-layout">
        <!-- Sidebar -->
        <aside class="sidebar always-show">
            @if (Auth::user()->role == 'admin')
                <ul class="lists">
                    <li class="list">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="link">Dashboard</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="{{ route('admin.permintaan') }}" class="nav-link">
                            <i class="bx bx-home-alt icon"></i>
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
                        <a href="{{ route('admin.prediksi') }}" class="nav-link">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="link">Prediksi</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="{{ route('admin.optimasi') }}" class="nav-link">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="link">Optimasi</span>
                        </a>
                    </li>
                    <li class="list">
                        <a href="{{ route('admin.pengujian') }}" class="nav-link">
                            <i class="bx bx-home-alt icon"></i>
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
