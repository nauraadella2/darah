@extends('layouts.app')

@section('content')
    <div class="prediction-container">

        <div class="prediction-header">
            <h2><i class="bx bx-cog"></i> Optimasi Parameter Peramalan</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Form Optimasi -->
        <div class="prediction-card">
            <div class="card-header">
                <h3><i class="bx bx-cog"></i> Hitung Parameter Optimasi</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.optimasi.hitung') }}">
                    @csrf
                    <div class="d-flex align-items-end gap-3 flex-wrap">
                        <div>
                            <label class="form-label">Tahun Mulai</label>
                            <select name="tahun_mulai" class="form-select" required>
                                <option value="">Pilih Tahun Mulai</option>
                                @foreach ($tahunTersedia as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Tahun Selesai</label>
                            <select name="tahun_selesai" class="form-select" required>
                                <option value="">Pilih Tahun Selesai</option>
                                @foreach ($tahunTersedia as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex align-items-end" style="padding-top: 24px;">
                            <button type="submit" class="btn btn-danger"
                                style="background-color: #f87171; border-color: #f87171;">
                                <i class="bx bx-play"></i> Hitung Parameter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Hasil Optimasi -->
        <div class="prediction-card mt-4">
            <div class="card-header">
                <h3><i class="bx bx-table"></i> Hasil Optimasi Parameter</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="result-table table table-bordered table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Golongan Darah</th>
                                <th>Alpha</th>
                                <th>Beta</th>
                                <th>Gamma</th>
                                <th>MAPE (%)</th>
                                <th>Periode Data</th>
                                <th>Status Akurasi</th>
                                <th>Terakhir Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (['A', 'B', 'AB', 'O'] as $golongan)
                                @php
                                    $data = $hasil[$golongan] ?? null;
                                    $mape = $data->mape ?? 0;

                                    if ($mape < 10) {
                                        $status = 'Sangat Baik';
                                        $badgeClass = 'bg-success';
                                    } elseif ($mape < 20) {
                                        $status = 'Baik';
                                        $badgeClass = 'bg-primary';
                                    } else {
                                        $status = 'Cukup';
                                        $badgeClass = 'bg-warning';
                                    }
                                @endphp
                                <tr>
                                    <td>Golongan {{ $golongan }}</td>
                                    <td>{{ $data ? number_format($data->alpha, 2) : '-' }}</td>
                                    <td>{{ $data ? number_format($data->beta, 2) : '-' }}</td>
                                    <td>{{ $data ? number_format($data->gamma, 2) : '-' }}</td>
                                    <td>{{ $data ? number_format($mape, 2) . '%' : '-' }}</td>
                                    <td>
                                        @if ($data)
                                            {{ $data->periode_mulai }} - {{ $data->periode_selesai }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data)
                                            <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data)
                                            {{ $data->updated_at->format('d/m/Y H:i') }}
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
<style>
    .prediction-card {
        border: 1px solid #fcdcdc;
        /* merah tipis */
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(248, 113, 113, 0.1);
        /* shadow merah halus */
        margin-bottom: 20px;
        background-color: #fff;
    }

    .prediction-card .card-header {
        background-color: #fff5f5;
        /* latar belakang merah sangat tipis */
        border-bottom: 1px solid #fcdcdc;
        padding: 12px 16px;
        font-weight: bold;
    }

    .result-table thead {
        background-color: #fff5f5;
        border-bottom: 2px solid #fcdcdc;
    }

    .result-table tbody tr:hover {
        background-color: #fff0f0;
        /* hover merah tipis */
    }
</style>

@push('scripts')
    <script>
        // Tahun Selesai dinamis mengikuti Tahun Mulai
        $('select[name="tahun_mulai"]').change(function() {
            var tahunMulai = parseInt($(this).val());
            $('select[name="tahun_selesai"] option').each(function() {
                var tahun = parseInt($(this).val());
                $(this).prop('disabled', tahun && tahun < tahunMulai);
            });
        });
    </script>
@endpush
