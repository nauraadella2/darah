@extends('layouts.app')

@section('content')
    <div class="testing-container">
        <div class="testing-header">
            <h1><i class="fas fa-flask"></i> Pengujian Akurasi Prediksi</h1>
            <p class="subtitle">Evaluasi performa model prediksi permintaan darah</p>
        </div>
        <!-- Filter Section -->
        <form method="GET" action="{{ route('admin.pengujian.filter') }}">
            <div class="testing-filter" style="display: none">
                <div class="filter-card">
                    <div class="filter-group">
                        <label>Golongan Darah</label>
                        <select name="golongan_darah" class="blood-type-select">
                            <option value="all">Semua Golongan</option>
                            <option value="A">Golongan A</option>
                            <option value="B">Golongan B</option>
                            <option value="AB">Golongan AB</option>
                            <option value="O">Golongan O</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Tahun</label>
                        {{-- <select name="tahun" class="year-select">
                            @foreach ($selects->keys() as $tahun)
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforeach
                        </select> --}}
                    </div>

                    <button type="submit" class="test-button">
                        <i class="fas fa-play"></i> Jalankan Pengujian
                    </button>
                </div>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="summary-cards" style="display: none">
            <div class="summary-card">
                <div class="card-icon bg-red-100">
                    <i class="fas fa-percentage text-red-600"></i>
                </div>
                <div class="card-content">
                    <span class="card-label">Rata-rata MAPE</span>
                    <span class="card-value">5.2%</span>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-icon bg-green-100">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="card-content">
                    <span class="card-label">Tingkat Akurasi</span>
                    <span class="card-value">94.8%</span>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-icon bg-blue-100">
                    <i class="fas fa-vial text-blue-600"></i>
                </div>
                <div class="card-content">
                    <span class="card-label">Total Pengujian</span>
                    <span class="card-value">24</span>
                </div>
            </div>
        </div>


        <!-- Results Section -->
        <div class="results-section">
            <div class="results-tabs">
                <button class="tab-button active" data-tab="table"><i class="fas fa-table"></i> Tabel Data</button>
                <button class="tab-button" data-tab="chart"><i class="fas fa-chart-line"></i> Visualisasi</button>
                <button class="tab-button" data-tab="analysis"><i class="fas fa-chart-pie"></i> Analisis</button>
            </div>

            <div class="tab-content active" id="table-tab">
                <div class="table-container">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Golongan</th>
                                <th>Permintaan Aktual</th>
                                <th>Hasil Prediksi</th>
                                <th>Selisih</th>
                                <th>Error (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($aktual as $dataAktual)
                                @php

                                    // Cari data prediksi yang sesuai: bulan dan golongan_darah harus sama
                                    $prediksiItem = $prediksi->first(function ($item) use ($dataAktual) {
                                        return $item->bulan == $dataAktual->bulan &&
                                            $item->golongan_darah == $dataAktual->golongan_darah;
                                    });

                                    // Hitung selisih dan error jika ada data prediksi
                                    $selisih = $prediksiItem ? $dataAktual->jumlah - $prediksiItem->jumlah : 0;
                                    $error =
                                        $prediksiItem && $dataAktual->jumlah > 0
                                            ? round((abs($selisih) / $dataAktual->jumlah) * 100, 2)
                                            : 0;
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::create()->month($dataAktual->bulan)->locale('id')->isoFormat('MMMM') }}
                                        {{ $dataAktual->tahun }}</td>
                                    <td><span
                                            class="blood-badge bg-red-100 text-red-800">{{ $dataAktual->golongan_darah }}</span>
                                    </td>
                                    <td>{{ $dataAktual->jumlah }}</td>
                                    <td>{{ $prediksiItem ? $prediksiItem->jumlah : '-' }}</td>
                                    <td>{{ $prediksiItem ? $selisih : '-' }}</td>
                                    <td>
                                        @if ($prediksiItem)
                                            <span class="error-badge">{{ $error }}%</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

            <div class="tab-content" id="chart-tab">
                <div class="chart-container">
                    <div class="chart-controls">
                        <select class="chart-type-select">
                            <option value="line">Line Chart</option>
                            <option value="bar">Bar Chart</option>
                            <option value="combo">Kombinasi</option>
                        </select>

                        <div class="chart-legend">
                            <span class="legend-item"><i class="fas fa-square" style="color: #e53e3e"></i> Aktual</span>
                            <span class="legend-item"><i class="fas fa-square" style="color: #3182ce"></i> Prediksi</span>
                        </div>
                    </div>
                    <canvas id="resultsChart" height="40" width="100"></canvas>
                </div>
            </div>
            <div class="tab-content" id="analysis-tab">
                <div class="analysis-grid">
                    @php
                        $totalA = $aktual->where('golongan_darah', 'A')->sum('jumlah');
                        $totalB = $aktual->where('golongan_darah', 'B')->sum('jumlah');
                        $totalAB = $aktual->where('golongan_darah', 'AB')->sum('jumlah');
                        $totalO = $aktual->where('golongan_darah', 'O')->sum('jumlah');
                        $grandTotal = $totalA + $totalB + $totalAB + $totalO;
                        $mapeA = round(($totalA / $grandTotal) * 100);
                        $mapeB = round(($totalB / $grandTotal) * 100);
                        $mapeAB = round(($totalAB / $grandTotal) * 100);
                        $mapeO = round(($totalO / $grandTotal) * 100);
                    @endphp
                    <div class="analysis-card">
                        <h3><i class="fas fa-tint" style="color: #ef4444;"></i> Golongan A</h3>
                        <div class="mb-2">
                            <p>Permintaan: {{ round(($totalA / $grandTotal) * 100) }}%</p>
                            <div class="progress-bar">
                                <div class="progress-fill bg-red-500"
                                    style="width: {{ round(($totalA / $grandTotal) * 100) }}%"></div>
                            </div>
                            <span class="blood-value">{{ $totalA }} kantong</span>
                        </div>

                        <div class="mt-3">
                            <p>Akurasi: {{ $mapeA }}%</p>
                            <div class="progress-bar">
                                <div class="progress-fill 
                    {{ $mapeA < 10
                        ? 'bg-green-500'
                        : ($mapeA < 20
                            ? 'bg-yellow-500'
                            : ($mapeA < 50
                                ? 'bg-orange-500'
                                : 'bg-red-500')) }}"
                                    style="width: {{ min($mapeA, 100) }}%">
                                </div>
                            </div>
                            <span
                                class="text-sm {{ $mapeA < 10
                                    ? 'text-green-500'
                                    : ($mapeA < 20
                                        ? 'text-yellow-500'
                                        : ($mapeA < 50
                                            ? 'text-orange-500'
                                            : 'text-red-500')) }}">
                                {{ $mapeA < 10 ? 'Sangat Akurat' : ($mapeA < 20 ? 'Akurat' : ($mapeA < 50 ? 'Cukup' : 'Tidak Akurat')) }}
                            </span>
                        </div>
                    </div>

                    <!-- Golongan B -->
                    <div class="analysis-card">
                        <h3><i class="fas fa-tint" style="color: #3b82f6;"></i> Golongan B</h3>
                        <div class="mb-2">
                            <p>Permintaan: {{ round(($totalB / $grandTotal) * 100) }}%</p>
                            <div class="progress-bar">
                                <div class="progress-fill bg-blue-500"
                                    style="width: {{ round(($totalB / $grandTotal) * 100) }}%"></div>
                            </div>
                            <span class="blood-value">{{ $totalB }} kantong</span>
                        </div>

                        <div class="mt-3">
                            <p>Akurasi: {{ $mapeB }}%</p>
                            <div class="progress-bar">
                                <div class="progress-fill 
                    {{ $mapeB < 10
                        ? 'bg-green-500'
                        : ($mapeB < 20
                            ? 'bg-yellow-500'
                            : ($mapeB < 50
                                ? 'bg-orange-500'
                                : 'bg-red-500')) }}"
                                    style="width: {{ min($mapeB, 100) }}%">
                                </div>
                            </div>
                            <span
                                class="text-sm {{ $mapeB < 10
                                    ? 'text-green-500'
                                    : ($mapeB < 20
                                        ? 'text-yellow-500'
                                        : ($mapeB < 50
                                            ? 'text-orange-500'
                                            : 'text-red-500')) }}">
                                {{ $mapeB < 10 ? 'Sangat Akurat' : ($mapeB < 20 ? 'Akurat' : ($mapeB < 50 ? 'Cukup' : 'Tidak Akurat')) }}
                            </span>
                        </div>
                    </div>

                    <!-- Golongan AB -->
                    <div class="analysis-card">
                        <h3><i class="fas fa-tint" style="color: #a855f7;"></i> Golongan AB</h3>
                        <div class="mb-2">
                            <p>Permintaan: {{ round(($totalAB / $grandTotal) * 100) }}%</p>
                            <div class="progress-bar">
                                <div class="progress-fill bg-purple-500"
                                    style="width: {{ round(($totalAB / $grandTotal) * 100) }}%"></div>
                            </div>
                            <span class="blood-value">{{ $totalAB }} kantong</span>
                        </div>

                        <div class="mt-3">
                            <p>Akurasi: {{ $mapeAB }}%</p>
                            <div class="progress-bar">
                                <div class="progress-fill 
                    {{ $mapeAB < 10
                        ? 'bg-green-500'
                        : ($mapeAB < 20
                            ? 'bg-yellow-500'
                            : ($mapeAB < 50
                                ? 'bg-orange-500'
                                : 'bg-red-500')) }}"
                                    style="width: {{ min($mapeAB, 100) }}%">
                                </div>
                            </div>
                            <span
                                class="text-sm {{ $mapeAB < 10
                                    ? 'text-green-500'
                                    : ($mapeAB < 20
                                        ? 'text-yellow-500'
                                        : ($mapeAB < 50
                                            ? 'text-orange-500'
                                            : 'text-red-500')) }}">
                                {{ $mapeAB < 10 ? 'Sangat Akurat' : ($mapeAB < 20 ? 'Akurat' : ($mapeAB < 50 ? 'Cukup' : 'Tidak Akurat')) }}
                            </span>
                        </div>
                    </div>

                    <!-- Golongan O -->
                    <div class="analysis-card">
                        <h3><i class="fas fa-tint" style="color: #f97316;"></i> Golongan O</h3>
                        <div class="mb-2">
                            <p>Permintaan: {{ round(($totalO / $grandTotal) * 100) }}%</p>
                            <div class="progress-bar">
                                <div class="progress-fill bg-orange-500"
                                    style="width: {{ round(($totalO / $grandTotal) * 100) }}%"></div>
                            </div>
                            <span class="blood-value">{{ $totalO }} kantong</span>
                        </div>

                        <div class="mt-3">
                            <p>Akurasi: {{ $mapeO }}%</p>
                            <div class="progress-bar">
                                <div class="progress-fill 
                    {{ $mapeO < 10
                        ? 'bg-green-500'
                        : ($mapeO < 20
                            ? 'bg-yellow-500'
                            : ($mapeO < 50
                                ? 'bg-orange-500'
                                : 'bg-red-500')) }}"
                                    style="width: {{ min($mapeO, 100) }}%">
                                </div>
                            </div>
                            <span
                                class="text-sm {{ $mapeO < 10
                                    ? 'text-green-500'
                                    : ($mapeO < 20
                                        ? 'text-yellow-500'
                                        : ($mapeO < 50
                                            ? 'text-orange-500'
                                            : 'text-red-500')) }}">
                                {{ $mapeO < 10 ? 'Sangat Akurat' : ($mapeO < 20 ? 'Akurat' : ($mapeO < 50 ? 'Cukup' : 'Tidak Akurat')) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.0.2"></script>
        <script>
            // Data untuk chart
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

            // Data aktual dan prediksi dari backend
            const aktualData = @json($aktual);
            const prediksiData = @json($prediksi);

            // Function untuk mendapatkan data berdasarkan golongan darah
            function getDataByBloodType(data, bloodType) {
                return months.map((_, index) => {
                    const monthData = data.find(item =>
                        item.bulan === index + 1 &&
                        item.golongan_darah === bloodType
                    );
                    return monthData ? (typeof monthData.jumlah === 'string' ? parseFloat(monthData.jumlah) : monthData
                        .jumlah) : 0;
                });
            }

            // Data untuk chart berdasarkan golongan darah
            const chartData = {
                labels: months,
                datasets: [{
                        label: 'Aktual',
                        data: getDataByBloodType(aktualData, 'A'), // Default golongan A
                        borderColor: '#e53e3e',
                        backgroundColor: 'rgba(229, 62, 62, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Prediksi',
                        data: getDataByBloodType(prediksiData, 'A'), // Default golongan A
                        borderColor: '#3182ce',
                        backgroundColor: 'rgba(49, 130, 206, 0.1)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.3,
                        fill: true
                    }
                ]
            };

            // Inisialisasi chart
            const ctx = document.getElementById('resultsChart').getContext('2d');
            const resultsChart = new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Perbandingan Aktual vs Prediksi Golongan Darah A',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw.toFixed(2)} kantong`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Jumlah Kantong Darah'
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Bulan'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'nearest'
                    }
                }
            });

            // Function untuk update chart berdasarkan golongan darah
            function updateChartByBloodType(bloodType) {
                if (bloodType === 'all') {
                    // Handle "all" case jika diperlukan
                    return;
                }

                // Update data untuk kedua dataset
                resultsChart.data.datasets[0].data = getDataByBloodType(aktualData, bloodType);
                resultsChart.data.datasets[1].data = getDataByBloodType(prediksiData, bloodType);

                // Update title
                resultsChart.options.plugins.title.text = `Perbandingan Aktual vs Prediksi Golongan Darah ${bloodType}`;

                // Update chart
                resultsChart.update();
            }

            // Function untuk update chart type
            function updateChartType(chartType) {
                resultsChart.config.type = chartType;
                resultsChart.update();
            }

            // Event listener untuk filter golongan darah
            document.querySelector('.blood-type-select').addEventListener('change', (e) => {
                updateChartByBloodType(e.target.value);
            });

            // Event listener untuk chart type
            document.querySelector('.chart-type-select').addEventListener('change', (e) => {
                updateChartType(e.target.value);
            });

            // Tab switching functionality
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', () => {
                    // Remove active class dari semua tab
                    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove(
                        'active'));

                    // Add active class ke tab yang diklik
                    button.classList.add('active');
                    document.getElementById(`${button.dataset.tab}-tab`).classList.add('active');

                    // Update chart ketika switch ke chart tab
                    if (button.dataset.tab === 'chart') {
                        resultsChart.update();
                    }
                });
            });
        </script>
    @endpush
@endsection
