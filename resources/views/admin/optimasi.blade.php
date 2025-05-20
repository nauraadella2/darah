@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow">
            <div class="card-header bg-pmi text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>Optimasi Nilai Alpha
                    </h3>
                </div>
            </div>

            <div class="card-body">
                <!-- Optimization Form -->
                <form method="POST" action="{{ route('admin.optimasi.hitung') }}" class="mb-4">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Tahun Mulai</label>
                            <select name="tahun_mulai" class="form-select" required>
                                <option value="">Pilih Tahun Mulai</option>
                                @foreach ($tahunTersedia as $tahun)
                                    <option value="{{ $tahun }}"
                                        {{ old('tahun_mulai') == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Tahun Selesai</label>
                            <select name="tahun_selesai" class="form-select" required>
                                <option value="">Pilih Tahun Selesai</option>
                                @foreach ($tahunTersedia as $tahun)
                                    <option value="{{ $tahun }}"
                                        {{ old('tahun_selesai') == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-pmi w-100">
                                <i class="fas fa-play me-2"></i>Proses
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Results Section -->
                @if (session('results'))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Hasil optimasi terbaru untuk periode {{ session('results_period') }}
                    </div>
                @endif

                <!-- Optimization Results Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Golongan Darah</th>
                                <th>Nilai Alpha</th>
                                <th>MAPE (%)</th>
                                <th>RMSE</th>
                                <th>Periode Data</th>
                                <th>Terakhir Update</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (['A', 'B', 'AB', 'O'] as $golongan)
                                @php
                                    $dataOptimasi = $hasil[$golongan]->first() ?? null;
                                    $rowColor =
                                        $dataOptimasi && $dataOptimasi->mape < 10
                                            ? 'table-success'
                                            : ($dataOptimasi && $dataOptimasi->mape < 20
                                                ? 'table-warning'
                                                : '');
                                @endphp
                                <tr class="{{ $rowColor }}">
                                    <td>Golongan {{ $golongan }}</td>
                                    <td>{{ $dataOptimasi ? number_format($dataOptimasi->alpha, 2) : '-' }}</td>
                                    <td>{{ $dataOptimasi ? number_format($dataOptimasi->mape, 2) . '%' : '-' }}</td>
                                    <td>{{ $dataOptimasi ? number_format($dataOptimasi->rmse, 2) : '-' }}</td>
                                    <td>
                                        @if ($dataOptimasi && $dataOptimasi->periode_mulai)
                                            {{ $dataOptimasi->periode_mulai }} - {{ $dataOptimasi->periode_selesai }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $dataOptimasi ? $dataOptimasi->updated_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        @if ($dataOptimasi)
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#detailModal" data-golongan="{{ $golongan }}"
                                                data-alpha="{{ number_format($dataOptimasi->alpha, 2) }}"
                                                data-mape="{{ number_format($dataOptimasi->mape, 2) }}"
                                                data-rmse="{{ number_format($dataOptimasi->rmse, 2) }}"
                                                data-period="{{ $dataOptimasi->periode_mulai }} - {{ $dataOptimasi->periode_selesai }}"
                                                data-updated="{{ $dataOptimasi->updated_at->format('d/m/Y H:i') }}">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
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
            transition: all 0.3s ease;
        }

        .btn-pmi:hover {
            background-color: #c62828;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
        }

        .table-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Year select validation
        document.querySelector('select[name="tahun_mulai"]').addEventListener('change', function() {
        var tahunMulai = parseInt(this.value);
        var tahunSelesaiSelect = document.querySelector('select[name="tahun_selesai"]');

        if (tahunMulai) {
            Array.from(tahunSelesaiSelect.options).forEach(option => {
                if (option.value && parseInt(option.value) < tahunMulai) {
                    option.disabled = true;
                } else {
                    option.disabled = false;
                }
            });
        }
        });
        });
    </script>
@endpush
