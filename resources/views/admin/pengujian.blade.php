@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
  <h2 class="text-2xl font-bold mb-4">🔬 Pengujian Model Prediksi</h2>

  <div class="bg-white rounded-lg shadow p-6">
    <form class="mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block mb-1 font-medium">Tanggal Mulai (Training)</label>
          <input type="month" class="w-full border rounded px-3 py-2">
        </div>
        <div>
          <label class="block mb-1 font-medium">Tanggal Akhir (Testing)</label>
          <input type="month" class="w-full border rounded px-3 py-2">
        </div>
        <div>
          <label class="block mb-1 font-medium">Alpha</label>
          <input type="number" step="0.1" min="0.1" max="0.9" value="0.5" class="w-full border rounded px-3 py-2">
        </div>
      </div>
      <button type="button" class="mt-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
        Mulai Pengujian
      </button>
    </form>

    <div class="mt-6">
      <h3 class="text-lg font-semibold mb-2">Hasil Perbandingan Prediksi vs Aktual</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm border">
          <thead class="bg-red-100">
            <tr>
              <th class="px-4 py-2 border">Tanggal</th>
              <th class="px-4 py-2 border">Golongan Darah</th>
              <th class="px-4 py-2 border">Aktual</th>
              <th class="px-4 py-2 border">Prediksi</th>
              <th class="px-4 py-2 border">Error (%)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="px-4 py-2 border text-center">2025-01</td>
              <td class="px-4 py-2 border text-center">A</td>
              <td class="px-4 py-2 border text-center">120</td>
              <td class="px-4 py-2 border text-center">118.5</td>
              <td class="px-4 py-2 border text-center">1.25%</td>
            </tr>
            <tr>
              <td class="px-4 py-2 border text-center">2025-01</td>
              <td class="px-4 py-2 border text-center">B</td>
              <td class="px-4 py-2 border text-center">90</td>
              <td class="px-4 py-2 border text-center">94.2</td>
              <td class="px-4 py-2 border text-center">4.67%</td>
            </tr>
            <tr>
              <td class="px-4 py-2 border text-center">2025-01</td>
              <td class="px-4 py-2 border text-center">O</td>
              <td class="px-4 py-2 border text-center">150</td>
              <td class="px-4 py-2 border text-center">145.0</td>
              <td class="px-4 py-2 border text-center">3.33%</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-4 text-green-700 font-bold">
        Total MAPE: <span class="text-red-600">3.08%</span>
      </div>
    </div>

    <div class="mt-6">
      <h3 class="text-lg font-semibold mb-2">Grafik Perbandingan</h3>
      <canvas id="grafikPengujian" height="100"></canvas>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('grafikPengujian').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['2025-01', '2025-02', '2025-03'],
      datasets: [
        {
          label: 'Aktual',
          data: [120, 90, 150],
          borderColor: 'red',
          fill: false,
        },
        {
          label: 'Prediksi',
          data: [118.5, 94.2, 145],
          borderColor: 'blue',
          fill: false,
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'top' }
      }
    }
  });
</script>
@endpush
