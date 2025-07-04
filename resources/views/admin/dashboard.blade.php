@extends('layouts.app')

@section('content')
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2><i class="bx bx-home"></i>Halaman Beranda</h2>
        </div>

        <div class="dashboard-cards">
            <!-- Card Total Permintaan Darah -->
            <div class="dashboard-card total-card">
                <div class="card-header">
                    <h3><i class="bx bxs-droplet"></i> Total Permintaan Darah</h3>
                </div>
                <div class="card-body">
                    <div class="year-selector mb-3">
                        <select id="yearFilter" class="form-select">
                            @foreach ($years as $year)
                                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                    {{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <h1 class="total-count" id="yearlyTotal">0 <span>kantong</span></h1>
                    <p class="update-info">Update terakhir: {{ now()->format('d F Y H:i') }}</p>
                </div>
            </div>

            <!-- Card Action Buttons -->
            <div class="dashboard-card action-card">
                <div class="card-header">
                    <h3><i class="bx bx-plus-circle"></i> Aksi Cepat</h3>
                </div>
                <div class="card-body action-buttons">
                    <a href="{{ route('admin.input') }}" class="btn-pdf">
                        <i class="bx bx-plus"></i> Input data
                    </a>
                </div>
            </div>
        </div>

        <!-- Grafik Permintaan Darah -->
        <div class="chart-card mt-4">
            <div class="card-header">
                <h3><i class="bx bx-line-chart"></i> Grafik Permintaan Darah Tahun <span
                        id="currentYear">{{ date('Y') }}</span></h3>
            </div>
            <div class="card-body">
                <canvas id="bloodDemandChart" height="300"></canvas>
            </div>
        </div>

        <!-- Tabel Ringkasan Tahunan -->
        <div class="chart-card mt-4">
            <div class="card-header">
                <h3><i class="bx bx-table"></i> Ringkasan Tahunan</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tahun</th>
                                <th>Total Permintaan</th>
                                <th>Golongan A</th>
                                <th>Golongan B</th>
                                <th>Golongan AB</th>
                                <th>Golongan O</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($yearlySummary as $summary)
                                <tr>
                                    <td>{{ $summary->year }}</td>
                                    <td>{{ number_format($summary->total) }}</td>
                                    <td>{{ number_format($summary->a) }}</td>
                                    <td>{{ number_format($summary->b) }}</td>
                                    <td>{{ number_format($summary->ab) }}</td>
                                    <td>{{ number_format($summary->o) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Chart setup
                const ctx = document.getElementById('bloodDemandChart').getContext('2d');
                let bloodDemandChart;
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

                // Initialize chart
                function initializeChart() {
                    bloodDemandChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: monthNames,
                            datasets: [{
                                label: 'Permintaan Darah',
                                data: Array(12).fill(0),
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.05)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#ef4444',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: {
                                            weight: 500
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1f2937',
                                    titleFont: {
                                        weight: 'bold'
                                    },
                                    callbacks: {
                                        label: function(context) {
                                            return ` ${context.dataset.label}: ${context.raw.toLocaleString()} kantong`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grace: '5%',
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    },
                                    title: {
                                        display: true,
                                        text: 'Jumlah Kantong Darah',
                                        font: {
                                            weight: 500
                                        }
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString();
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    title: {
                                        display: true,
                                        text: 'Bulan',
                                        font: {
                                            weight: 500
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Fetch data for selected year
                function fetchYearData(year) {
                    fetch(`{{ route('admin.dashboard.data') }}?year=${year}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            // Validate data format
                            if (!data || !data.monthly_data || !Array.isArray(data.monthly_data)) {
                                throw new Error('Invalid data format');
                            }

                            // Ensure we have 12 months of data
                            const monthlyData = data.monthly_data.length === 12 ?
                                data.monthly_data : [...data.monthly_data, ...Array(12 - data.monthly_data.length)
                                    .fill(0)
                                ];

                            // Update UI
                            document.getElementById('yearlyTotal').innerHTML =
                                `${data.total.toLocaleString()} <span>kantong</span>`;
                            document.getElementById('currentYear').textContent = year;

                            // Update chart
                            if (bloodDemandChart) {
                                bloodDemandChart.data.datasets[0].data = monthlyData;
                                bloodDemandChart.update();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Gagal memuat data. Silakan coba lagi.');
                        });
                }

                // Event listeners
                document.getElementById('yearFilter').addEventListener('change', function() {
                    fetchYearData(this.value);
                });

                // Initial load
                initializeChart();
                fetchYearData(document.getElementById('yearFilter').value);
            });
        </script>
    @endpush
@endsection
