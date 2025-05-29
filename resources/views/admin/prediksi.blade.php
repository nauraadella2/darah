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
                        <select name="tahun" required>
                            <option value="">Pilih Tahun</option>
                            @foreach ($tahunPrediksiTersedia as $tahun)
                                <option value="{{ $tahun }}" {{ old('tahun') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Golongan Darah</label>
                        <select name="golongan">
                            <option value="">Semua Golongan</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
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
            <form method="GET" action="{{ route('admin.prediksi.index') }}" class="filter-form">
                <div class="filter-group">
                    <select name="tahun">
                        <option value="">Semua Tahun</option>
                        @foreach (range($lastTrainingYear + 1, date('Y')) as $year)
                            <option value="{{ $year }}" {{ $request->tahun == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <select name="bulan">
                        <option value="">Semua Bulan</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $request->bulan == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <button type="submit" class="btn-filter">
                    <i class="bx bx-filter-alt"></i> Filter
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th>Golongan A</th>
                            <th>Golongan B</th>
                            <th>Golongan AB</th>
                            <th>Golongan O</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($predictions as $prediction)
                            <tr>
                                <td>{{ DateTime::createFromFormat('!m', $prediction->bulan)->format('F') }}
                                    {{ $prediction->tahun }}</td>
                                <td>{{ number_format($prediction->gol_a, 0) }}</td>
                                <td>{{ number_format($prediction->gol_b, 0) }}</td>
                                <td>{{ number_format($prediction->gol_ab, 0) }}</td>
                                <td>{{ number_format($prediction->gol_o, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="no-data">Tidak ada data prediksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('resultsChart').getContext('2d');

            // Prepare data from PHP collection
            const prediksiData = @json($prediksi);

            // Month names
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

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

            // Function to get data by blood type
            function getDataByBloodType(bloodType) {
                const filtered = prediksiData.filter(item => item.golongan_darah === bloodType);
                filtered.sort((a, b) => a.bulan - b.bulan);

                // Create array for 12 months, fill with 0 if no data
                const monthlyData = new Array(12).fill(0);
                filtered.forEach(item => {
                    if (item.bulan >= 1 && item.bulan <= 12) {
                        monthlyData[item.bulan - 1] = parseFloat(item.jumlah);
                    }
                });

                return monthlyData;
            }

            // Prepare datasets for all blood types
            const datasets = bloodTypes.map(bloodType => ({
                label: `Golongan ${bloodType.type}`,
                data: getDataByBloodType(bloodType.type),
                borderColor: bloodType.color,
                backgroundColor: bloodType.bgColor,
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }));

            // Initialize chart
            let chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthNames,
                    datasets: datasets
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

            // Blood type filter change handler (now for showing/hiding specific blood types)
            const bloodTypeSelect = document.querySelector('.blood-type-select');
            if (bloodTypeSelect) {
                bloodTypeSelect.addEventListener('change', (e) => {
                    const selectedType = e.target.value;

                    if (selectedType === 'all') {
                        // Show all blood types
                        chart.data.datasets.forEach(dataset => {
                            dataset.hidden = false;
                        });
                        chart.options.plugins.title.text =
                            'Perbandingan Prediksi Kebutuhan Darah Semua Golongan';
                    } else {
                        // Show only selected blood type
                        chart.data.datasets.forEach((dataset, index) => {
                            dataset.hidden = bloodTypes[index].type !== selectedType;
                        });
                        chart.options.plugins.title.text =
                            `Prediksi Kebutuhan Darah Golongan ${selectedType}`;
                    }

                    chart.update();
                });
            }

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
