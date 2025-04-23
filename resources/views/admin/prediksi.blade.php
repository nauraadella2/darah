@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
  <h2 class="text-2xl font-bold mb-6 text-red-600">📊 Hasil Prediksi Permintaan Darah</h2>

  <div class="bg-white shadow rounded-lg p-6 mb-6">
    <form>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="bulan" class="block mb-2">Pilih Bulan Prediksi:</label>
          <input type="month" id="bulan" name="bulan" class="w-full">
        </div>
        <div>
          <label for="alpha" class="block mb-2">Nilai Alpha:</label>
          <input type="number" step="0.1" min="0.1" max="0.9" value="0.5" id="alpha" name="alpha" class="w-full">
        </div>
      </div>
      <div class="mt-4">
        <button type="submit">Tampilkan Prediksi</button>
      </div>
    </form>
  </div>

  <div class="bg-white shadow rounded-lg p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Tabel Hasil Prediksi</h3>
    <div class="overflow-x-auto">
      <table>
        <thead>
          <tr>
            <th>Bulan</th>
            <th>Golongan Darah A</th>
            <th>Golongan Darah B</th>
            <th>Golongan Darah AB</th>
            <th>Golongan Darah O</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>2025-05</td>
            <td>120</td>
            <td>95</td>
            <td>60</td>
            <td>110</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-4">Grafik Prediksi</h3>
    <canvas id="prediksiChart" height="100"></canvas>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('prediksiChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['2025-01', '2025-02', '2025-03', '2025-04', '2025-05'],
      datasets: [
        {
          label: 'Darah A',
          data: [100, 110, 115, 118, 120],
          borderColor: 'rgba(239, 68, 68, 1)', // merah
          backgroundColor: 'rgba(239, 68, 68, 0.2)',
          fill: true,
        },
        {
          label: 'Darah B',
          data: [80, 85, 90, 93, 95],
          borderColor: 'rgba(34, 197, 94, 1)', // hijau
          backgroundColor: 'rgba(34, 197, 94, 0.2)',
          fill: true,
        },
        {
          label: 'Darah AB',
          data: [45, 50, 55, 58, 60],
          borderColor: 'rgba(59, 130, 246, 1)', // biru
          backgroundColor: 'rgba(59, 130, 246, 0.2)',
          fill: true,
        },
        {
          label: 'Darah O',
          data: [90, 95, 100, 105, 110],
          borderColor: 'rgba(251, 191, 36, 1)', // kuning
          backgroundColor: 'rgba(251, 191, 36, 0.2)',
          fill: true,
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Jumlah Permintaan'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Bulan'
          }
        }
      }
    }
  });
</script>
@endpush
