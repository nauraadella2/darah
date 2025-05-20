@extends('layouts.app')
{{-- @dd($holla) --}}
@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-pmi text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Prediksi Kebutuhan Darah
                </h3>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-times-circle me-2"></i>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.prediksi.hitung') }}" class="mb-4">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Golongan Darah</label>
                        <select name="golongan_darah" class="form-select" required>
                            <option value="">Pilih Golongan</option>
                            <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>Golongan A</option>
                            <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>Golongan B</option>
                            <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>Golongan AB</option>
                            <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>Golongan O</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tahun Prediksi</label>
                        <select name="tahun" class="form-select" required>
                            <option value="">Pilih Tahun</option>
                            @foreach($tahunPrediksiTersedia as $tahun)
                                <option value="{{ $tahun }}" {{ old('tahun') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Data training sampai {{ $lastTrainingYear }}, prediksi dimulai {{ $lastTrainingYear + 1 }}</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Mode Prediksi</label>
                        <select name="mode_prediksi" id="modePrediksi" class="form-select" required>
                            <option value="bulanan" {{ old('mode_prediksi') == 'bulanan' ? 'selected' : '' }}>Per Bulan</option>
                            <option value="tahunan" {{ old('mode_prediksi') == 'tahunan' ? 'selected' : '' }}>Setahun Sekaligus</option>
                        </select>
                    </div>

                    <div class="col-md-4" id="bulanField">
                        <label class="form-label">Bulan Prediksi</label>
                        <select name="bulan" class="form-select">
                            <option value="">Pilih Bulan</option>
                            @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}" {{ old('bulan') == $month ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-pmi px-4">
                            <i class="fas fa-calculator me-2"></i>Hitung Prediksi
                        </button>
                    </div>
                </div>
            </form>

            @isset($prediksi)
            <div class="prediction-results mt-5">
                @if(session('prediction_mode') == 'tahunan')
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt text-pmi me-2"></i>
                            Prediksi Tahunan {{ $prediksi['tahun'] }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:300px;">
                            <canvas id="annualChart"></canvas>
                        </div>
                        <div class="table-responsive mt-4">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bulan</th>
                                        <th>Prediksi (kantong)</th>
                                        <th>Alpha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prediksi['annual_predictions'] as $pred)
                                    <tr>
                                        <td>{{ DateTime::createFromFormat('!m', $pred->bulan)->format('F') }}</td>
                                        <td>{{ number_format($pred->jumlah, 0) }}</td>
                                        <td>{{ number_format($pred->alpha, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle text-pmi me-2"></i>
                                    Detail Prediksi
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="prediction-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Golongan Darah:</span>
                                        <span class="detail-value">Golongan {{ $prediksi['golongan'] }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Periode Prediksi:</span>
                                        <span class="detail-value">
                                            {{ DateTime::createFromFormat('!m', $prediksi['bulan'])->format('F Y') }}
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Nilai Alpha:</span>
                                        <span class="detail-value">{{ number_format($prediksi['alpha'], 2) }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Hasil Prediksi:</span>
                                        <span class="detail-value prediction-value">
                                            {{ number_format($prediksi['hasil'], 0) }} kantong
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-bar text-pmi me-2"></i>
                                    Grafik Historis
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="position: relative; height:250px;">
                                    <canvas id="historyChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('admin.pengujian') }}" class="btn btn-outline-pmi me-2">
                        <i class="fas fa-flask me-2"></i>Uji Akurasi
                    </a>
                    <button class="btn btn-pmi" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Cetak Laporan
                    </button>
                </div>
            </div>
            @endisset
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-pmi {
        background-color: #e53935;
        background: linear-gradient(135deg, #e53935, #c62828);
    }
    .btn-pmi {
        background-color: #e53935;
        color: white;
        border: none;
        transition: all 0.3s;
    }
    .btn-pmi:hover {
        background-color: #c62828;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .btn-outline-pmi {
        color: #e53935;
        border-color: #e53935;
    }
    .btn-outline-pmi:hover {
        background-color: #e53935;
        color: white;
    }
    .prediction-value {
        color: #e53935;
        font-weight: bold;
        font-size: 1.2em;
    }
    .detail-item {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
    }
    .detail-label {
        font-weight: 600;
        color: #555;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .form-select:focus {
        border-color: #e53935;
        box-shadow: 0 0 0 0.25rem rgba(229, 57, 53, 0.25);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle bulan field based on prediction mode
        const modeSelect = document.getElementById('modePrediksi');
        const bulanField = document.getElementById('bulanField');
        
        function toggleBulanField() {
            bulanField.style.display = modeSelect.value === 'bulanan' ? 'block' : 'none';
            if (modeSelect.value === 'tahunan') {
                document.querySelector('[name="bulan"]').value = '';
            }
        }
        
        modeSelect.addEventListener('change', toggleBulanField);
        toggleBulanField(); // Initialize
        
        // Initialize charts if prediction results exist
        @isset($prediksi)
            @if(session('prediction_mode') == 'tahunan')
                // Annual chart
                const annualCtx = document.getElementById('annualChart').getContext('2d');
                new Chart(annualCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($prediksi['annual_predictions']->map(function($item) {
                            return DateTime::createFromFormat('!m', $item->bulan)->format('M');
                        })),
                        datasets: [{
                            label: 'Prediksi {{ $prediksi["tahun"] }}',
                            data: @json($prediksi['annual_predictions']->pluck('jumlah')),
                            backgroundColor: 'rgba(229, 57, 53, 0.7)',
                            borderColor: '#e53935',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.raw + ' kantong';
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
                                }
                            }
                        }
                    }
                });
            @else
                // Monthly chart
                const historyCtx = document.getElementById('historyChart').getContext('2d');
                new Chart(historyCtx, {
                    type: 'line',
                    data: {
                        labels: @json($prediksi['history_labels']),
                        datasets: [{
                            label: 'Kebutuhan Aktual',
                            data: @json($prediksi['history_data']),
                            borderColor: '#e53935',
                            backgroundColor: 'rgba(229, 57, 53, 0.1)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.raw + ' kantong';
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
                                }
                            }
                        }
                    }
                });
            @endif
        @endisset
    });
</script>
@endpush