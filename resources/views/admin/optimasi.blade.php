@extends('layouts.app')

@section('content')
<div class="container-permintaan">
    <div class="header-permintaan">
        <h1>Optimasi Parameter Peramalan</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Input Langsung di Atas Tabel -->
    <div class="optimasi-form">
        <form method="POST" action="{{ route('admin.optimasi.hitung') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Tahun Mulai</label>
                <select name="tahun_mulai" class="form-select" required>
                    <option value="">Pilih Tahun Mulai</option>
                    @foreach ($tahunTersedia as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tahun Selesai</label>
                <select name="tahun_selesai" class="form-select" required>
                    <option value="">Pilih Tahun Selesai</option>
                    @foreach ($tahunTersedia as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-grid">
                <button type="submit" class="btn btn-optimasi">
                    <i class="fas fa-calculator"></i> Hitung Parameter
                </button>
            </div>
        </form>
    </div>

    <div class="table-container mt-4">
        <table class="data-table" id="optimasiTable">
            <thead>
                <tr>
                    <th>Golongan Darah</th>
                    <th>Alpha</th>
                    <th>Beta</th>
                    <th>Gamma</th>
                    <th>MAPE (%)</th>
                    <th>RMSE</th>
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
                        
                        // Warna berdasarkan MAPE
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
                        <td>{{ $data ? number_format($mape, 2).'%' : '-' }}</td>
                        <td>{{ $data ? number_format($data->rmse, 2) : '-' }}</td>
                        <td>
                            @if($data)
                                {{ $data->periode_mulai }} - {{ $data->periode_selesai }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($data)
                                <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($data)
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
@endsection

@push('styles')
<style>
    /* Konsisten dengan halaman data historis */
    .container-permintaan {
        max-width: 100%;
        padding: 20px;
    }
    
    .header-permintaan {
        margin-bottom: 20px;
    }
    
    .optimasi-form {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .btn-optimasi {
        background-color: #d32f2f;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 4px;
        width: 100%;
    }
    
    .btn-optimasi:hover {
        background-color: #b71c1c;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th {
        background-color: #d32f2f;
        color: white;
        padding: 12px;
        text-align: left;
    }
    
    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }
    
    .data-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    
    .data-table tr:hover {
        background-color: #f0f0f0;
    }
    
    /* Badge untuk status akurasi */
    .badge {
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        color: white;
    }
    
    .bg-success {
        background-color: #388e3c;
    }
    
    .bg-primary {
        background-color: #1976d2;
    }
    
    .bg-warning {
        background-color: #ffa000;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#optimasiTable').DataTable({
            responsive: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            pageLength: 10,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
        
        // Validasi tahun selesai >= tahun mulai
        $('select[name="tahun_mulai"]').change(function() {
            var tahunMulai = parseInt($(this).val());
            $('select[name="tahun_selesai"] option').each(function() {
                var tahun = parseInt($(this).val());
                $(this).prop('disabled', tahun && tahun < tahunMulai);
            });
        });
    });
</script>
@endpush