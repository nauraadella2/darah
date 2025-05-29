@extends('layouts.app')

@section('content')
  <h1 class="text-2xl font-bold mb-6">Selamat datang Admin!</h1>

  <!-- Ringkasan Kartu -->
  <div class="summary-cards">
    <div class="card">
      <h3>Permintaan Darah</h3>
      <p class="jumlah" id="totalPermintaan">120</p>
    </div>
    {{-- <div class="card">
      <h3>User Terdaftar</h3>
      <p class="jumlah" id="totalUser">15</p>
    </div> --}}
    <div class="card">
      <h3>Prediksi Aktif</h3>
      <p class="jumlah" id="totalPrediksi">6</p>
    </div>
  </div>

  <!-- Grafik -->
  <div class="chart-section">
    <h2 class="text-xl font-semibold mb-2">Tren Permintaan Darah</h2>
    <canvas id="grafikPermintaan"></canvas>
  </div>

  <!-- Shortcut -->
  <div class="shortcut-section">
    <a href="{{ route('admin.permintaan') }}" class="shortcut">
      <i class='bx bx-plus-medical'></i> Input Data
    </a>
    <a href="{{ route('admin.optimasi') }}" class="shortcut">
      <i class='bx bx-cog'></i> Optimasi Alpha
    </a>
    {{-- <a href="{{ route('admin.pengujian') }}" class="shortcut"> --}}
      <i class='bx bx-flask'></i> Pengujian
    </a>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('grafikPermintaan').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [{
          label: 'Permintaan Darah',
          data: [12, 19, 15, 22, 17, 25],
          borderColor: 'red',
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          tension: 0.3,
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
@endpush
