@extends('layouts.app')
{{-- @dd($prediksi) --}}
@section('content')
    <div class="prediction-container">
        <div class="prediction-header">
            <h2><i class="bx bx-trending-up"></i> Prediksi Kebutuhan Darah</h2>
            <div class="header-info">
                <span class="training-data">Data Training: {{ $lastTrainingYear - 1 }} - {{ $lastTrainingYear }}</span>
            </div>
        </div>

        {{-- @include('partials.alert') --}}

        <!-- Form Prediksi -->
        <div class="prediction-card">
            <div class="card-header">
                <h3><i class="bx bx-calculator"></i> Buat Prediksi Baru</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.prediksi.hitung') }}" class="prediction-form">
                    @csrf
                    <div class="form-group">
                        <label>Tahun Prediksi</label>
                        <select name="tahun" required disabled>
                            <option value="{{ $tahunBerikutnya }}" selected>{{ $tahunBerikutnya }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Bulan Prediksi</label>
                        <input type="number" name="periods" min="1" max="12" value="6" required>
                    </div>

                    <div class="form-group">
                        <label>Alpha (α)</label>
                        <input type="number" name="alpha" step="0.1" min="0.1" max="0.9"
                            placeholder="0.1 - 0.9">
                    </div>

                    <div class="form-group">
                        <label>Beta (β)</label>
                        <input type="number" name="beta" step="0.1" min="0.1" max="0.9"
                            placeholder="0.1 - 0.9">
                    </div>
                    <div class="form-group">
                        <label>Gamma</label>
                        <input type="number" name="gamma" step="0.1" min="0.1" max="0.9"
                            placeholder="0.1 - 0.9">
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="bx bx-play"></i> Proses
                    </button>
                </form>
            </div>
        </div>

        @if (session('results'))
            <div class="results-card">
                <div class="card-header success">
                    <h3><i class="bx bx-check-circle"></i> Hasil Prediksi Tahun {{ session('results')['year'] }}</h3>
                    @if (session('results')['custom_params'])
                        <span class="custom-params">(Menggunakan parameter custom)</span>
                    @endif
                    <a href="{{ route('admin.prediksi.index') }}" class="btn-close">
                        <i class="bx bx-x"></i> Tutup
                    </a>
                </div>
                <div class="card-body">
                    @foreach (session('results')['forecasts'] as $golongan => $result)
                        <div class="result-group">
                            <h4>Golongan {{ $golongan }}</h4>
                            <div class="table-responsive">
                                <table class="result-table">
                                    <thead>
                                        <tr>
                                            <th>Bulan-Tahun</th>
                                            <th>Hasil Prediksi</th>
                                            <th>Alpha (α)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($result['forecasts'] as $forecast)
                                            <tr>
                                                <td>{{ $forecast['period'] }}</td>
                                                <td>{{ number_format($forecast['forecast'], 0) }} kantong</td>
                                                <td>{{ number_format($result['alpha'], 4) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Filter Controls -->
        <div class="filter-controls">
            <div class="filter-group">
                <label for="monthFilter">Filter Bulan:</label>
                <select id="monthFilter" class="month-filter">
                    <option value="all">Semua Bulan</option>
                    @foreach ($availableMonths as $month)
                        <option value="{{ $month }}">
                            {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="bloodTypeFilter">Filter Golongan Darah:</label>
                <select id="bloodTypeFilter" class="blood-type-select">
                    <option value="all">Semua Golongan</option>
                    <option value="A">Golongan A</option>
                    <option value="B">Golongan B</option>
                    <option value="AB">Golongan AB</option>
                    <option value="O">Golongan O</option>
                </select>
            </div>
        </div>

        <!-- Grafik Prediksi -->
        <div class="chart-card">
            <div class="card-header">
                <h3><i class="bx bx-line-chart"></i> Grafik Prediksi</h3>
            </div>
            <div class="card-body">
                <canvas id="resultsChart" height="40" width="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Prediksi -->
    <div class="history-card">
        <div class="card-header">
            <h3><i class="bx bx-history"></i> Riwayat Prediksi</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="history-table" id="historyTable">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th>Golongan A</th>
                            <th>Golongan B</th>
                            <th>Golongan AB</th>
                            <th>Golongan O</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($predictions as $prediction)
                            <tr data-month="{{ $prediction->bulan }}">
                                <td>{{ DateTime::createFromFormat('!m', $prediction->bulan)->format('F') }}
                                    {{ $prediction->tahun }}</td>
                                <td>{{ number_format($prediction->gol_a, 0) }}</td>
                                <td>{{ number_format($prediction->gol_b, 0) }}</td>
                                <td>{{ number_format($prediction->gol_ab, 0) }}</td>
                                <td>{{ number_format($prediction->gol_o, 0) }}</td>
                                <td><strong>{{ number_format($prediction->gol_a + $prediction->gol_b + $prediction->gol_ab + $prediction->gol_o, 0) }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="no-data">Tidak ada data prediksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($predictions->count() > 0)
                <div class="summary-info" id="summaryInfo">
                    <p><strong>Total Periode:</strong> <span id="totalPeriods">{{ $predictions->count() }}</span> bulan</p>
                    <p><strong>Data Terakhir Diprediksi:</strong>
                        {{ $predictions->first()->created_at->format('d M Y H:i') }}</p>
                </div>
            @endif
        </div>
    </div>
    </div>

    <style>
        .filter-controls {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
            font-size: 14px;
            min-width: 150px;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        @media (max-width: 768px) {
            .filter-controls {
                flex-direction: column;
            }

            .filter-group select {
                min-width: 100%;
            }
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('resultsChart').getContext('2d');

            // Prepare data from PHP collection
            const prediksiData = @json($prediksi);
            const predictionsData = @json($predictions->toArray());

            // Store original data for filtering
            const originalPrediksiData = [...prediksiData];

            // Month names
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            const fullMonthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                'September', 'Oktober', 'November', 'Desember'
            ];

            // Blood types and their colors
            const bloodTypes = [{
                    type: 'A',
                    color: '#d32f2f',
                    bgColor: 'rgba(211, 47, 47, 0.1)'
                },
                {
                    type: 'B',
                    color: '#1976d2',
                    bgColor: 'rgba(25, 118, 210, 0.1)'
                },
                {
                    type: 'AB',
                    color: '#388e3c',
                    bgColor: 'rgba(56, 142, 60, 0.1)'
                },
                {
                    type: 'O',
                    color: '#ffa000',
                    bgColor: 'rgba(255, 160, 0, 0.1)'
                }
            ];

            // Get available months from PHP data
            const availableMonths = @json($availableMonths->toArray());

            // Function to get data by blood type with month filter
            function getDataByBloodType(bloodType, selectedMonth = 'all') {
                let filtered = originalPrediksiData.filter(item => item.golongan_darah === bloodType);

                if (selectedMonth !== 'all') {
                    filtered = filtered.filter(item => item.bulan == selectedMonth);
                }

                filtered.sort((a, b) => a.bulan - b.bulan);

                if (selectedMonth !== 'all') {
                    // For single month, return only that month's data
                    const monthData = filtered.find(item => item.bulan == selectedMonth);
                    return monthData ? [parseFloat(monthData.jumlah)] : [0];
                } else {
                    // For all months, use only available months from data
                    const monthlyData = [];
                    availableMonths.forEach(month => {
                        const monthData = filtered.find(item => item.bulan == month);
                        monthlyData.push(monthData ? parseFloat(monthData.jumlah) : 0);
                    });
                    return monthlyData;
                }
            }

            // Function to get chart labels based on selected month
            function getChartLabels(selectedMonth = 'all') {
                if (selectedMonth !== 'all') {
                    return [fullMonthNames[parseInt(selectedMonth) - 1]];
                }
                // Return labels for available months only
                return availableMonths.map(month => monthNames[month - 1]);
            }

            // Function to prepare datasets
            function prepareDatasets(selectedMonth = 'all', selectedBloodType = 'all') {
                let datasets = [];

                if (selectedBloodType === 'all') {
                    datasets = bloodTypes.map(bloodType => ({
                        label: `Golongan ${bloodType.type}`,
                        data: getDataByBloodType(bloodType.type, selectedMonth),
                        borderColor: bloodType.color,
                        backgroundColor: bloodType.bgColor,
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        hidden: false
                    }));
                } else {
                    const bloodType = bloodTypes.find(bt => bt.type === selectedBloodType);
                    if (bloodType) {
                        datasets = [{
                            label: `Golongan ${bloodType.type}`,
                            data: getDataByBloodType(bloodType.type, selectedMonth),
                            borderColor: bloodType.color,
                            backgroundColor: bloodType.bgColor,
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            hidden: false
                        }];
                    }
                }

                return datasets;
            }

            // Initialize chart
            let chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: getChartLabels(),
                    datasets: prepareDatasets()
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Perbandingan Prediksi Kebutuhan Darah Semua Golongan',
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
                            beginAtZero: true,
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

            // Function to update chart title
            function updateChartTitle(selectedMonth, selectedBloodType) {
                let title = 'Prediksi Kebutuhan Darah';

                if (selectedBloodType !== 'all') {
                    title += ` Golongan ${selectedBloodType}`;
                } else {
                    title += ' Semua Golongan';
                }

                if (selectedMonth !== 'all') {
                    title += ` - ${fullMonthNames[parseInt(selectedMonth) - 1]}`;
                }

                chart.options.plugins.title.text = title;
            }

            // Function to filter history table
            function filterHistoryTable(selectedMonth) {
                const tbody = document.querySelector('#historyTable tbody');
                const rows = tbody.querySelectorAll('tr[data-month]');
                let visibleCount = 0;

                rows.forEach(row => {
                    const rowMonth = row.getAttribute('data-month');
                    if (selectedMonth === 'all' || rowMonth === selectedMonth) {
                        row.style.display = 'table-row';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update summary info
                const totalPeriodsSpan = document.getElementById('totalPeriods');
                if (totalPeriodsSpan) {
                    totalPeriodsSpan.textContent = visibleCount;
                }
            }

            // Month filter event listener
            const monthFilter = document.getElementById('monthFilter');
            monthFilter.addEventListener('change', function() {
                const selectedMonth = this.value;
                const selectedBloodType = document.getElementById('bloodTypeFilter').value;

                // Update chart
                chart.data.labels = getChartLabels(selectedMonth);
                chart.data.datasets = prepareDatasets(selectedMonth, selectedBloodType);
                updateChartTitle(selectedMonth, selectedBloodType);
                chart.update();

                // Update table
                filterHistoryTable(selectedMonth);
            });

            // Blood type filter event listener
            const bloodTypeFilter = document.getElementById('bloodTypeFilter');
            bloodTypeFilter.addEventListener('change', function() {
                const selectedBloodType = this.value;
                const selectedMonth = document.getElementById('monthFilter').value;

                // Update chart
                chart.data.datasets = prepareDatasets(selectedMonth, selectedBloodType);
                updateChartTitle(selectedMonth, selectedBloodType);
                chart.update();
            });

            // Chart type switching
            const chartTypeSelect = document.querySelector('.chart-type-select');
            if (chartTypeSelect) {
                chartTypeSelect.addEventListener('change', (e) => {
                    chart.config.type = e.target.value;
                    chart.update();
                });
            }

            // Tab Switching
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove(
                        'active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList
                        .remove('active'));

                    button.classList.add('active');
                    document.getElementById(`${button.dataset.tab}-tab`).classList.add('active');

                    // Update chart when switching to chart tab
                    if (button.dataset.tab === 'chart' && chart) {
                        chart.update();
                    }
                });
            });
        });
    </script>
@endpush
