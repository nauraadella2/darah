@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Input Data Permintaan Darah Tahunan</h4>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Sukses!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonColor: '#d32f2f',
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    @if(session('confirm'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Data Sudah Ada',
                    text: 'Data untuk bulan {{ DateTime::createFromFormat('!m', session('confirm')['bulan'])->format('F') }} tahun {{ session('confirm')['tahun'] }} sudah ada. Apakah Anda ingin menimpanya?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d32f2f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Timpa Data',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form programmatically
                        document.getElementById('confirmOverwriteForm').submit();
                    } else {
                        window.location.href = "{{ route('admin.input') }}";
                    }
                });
            });
        </script>
        
        <form id="confirmOverwriteForm" action="{{ route('admin.permintaan.confirm-overwrite') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="tahun" value="{{ session('confirm')['tahun'] }}">
            <input type="hidden" name="bulan[]" value="{{ session('confirm')['bulan'] }}">
            <input type="hidden" name="gol_a[]" value="{{ session('confirm')['gol_a'] }}">
            <input type="hidden" name="gol_b[]" value="{{ session('confirm')['gol_b'] }}">
            <input type="hidden" name="gol_ab[]" value="{{ session('confirm')['gol_ab'] }}">
            <input type="hidden" name="gol_o[]" value="{{ session('confirm')['gol_o'] }}">
            <input type="hidden" name="confirm_overwrite" value="1">
        </form>
    @endif

    <form id="bulkDataForm" action="{{ route('admin.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="tahun" class="form-label">Tahun</label>
            <input type="number" name="tahun" class="form-control" required value="{{ date('Y') }}">
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Bulan</th>
                    <th>Golongan A</th>
                    <th>Golongan B</th>
                    <th>Golongan AB</th>
                    <th>Golongan O</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 12; $i++)
                <tr>
                    <td>
                        <input type="hidden" name="bulan[]" value="{{ $i }}">
                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                    </td>
                    <td><input type="number" name="gol_a[]" value="{{ old('gol_a.'.($i-1), random_int(40, 80)) }}" class="form-control" min="0" required></td>
                    <td><input type="number" name="gol_b[]" value="{{ old('gol_b.'.($i-1), random_int(35, 70)) }}" class="form-control" min="0" required></td>
                    <td><input type="number" name="gol_ab[]" value="{{ old('gol_ab.'.($i-1), random_int(10, 30)) }}" class="form-control" min="0" required></td>
                    <td><input type="number" name="gol_o[]" value="{{ old('gol_o.'.($i-1), random_int(90, 150)) }}" class="form-control" min="0" required></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary mt-3">Simpan Data</button>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle form submission
        const bulkDataForm = document.getElementById('bulkDataForm');
        
        if (bulkDataForm) {
            bulkDataForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Menyimpan Data',
                    html: 'Sedang memproses data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        
                        // Submit form after showing loading
                        setTimeout(() => {
                            bulkDataForm.submit();
                        }, 500);
                    }
                });
            });
        }
    });
</script>
@endpush