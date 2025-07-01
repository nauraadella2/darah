@extends('layouts.app')

@section('content')
    <div class="container-permintaan">
        <div class="header-permintaan" style="display: flex; align-items: center;">
            <div class="dashboard-header">
                <h2><i class="bx bx-history"></i> Halaman Data Historis</h2>
            </div>

            <div class="action-group">
                <button class="btn-pdf" onclick="openModal()">+ Input Satu Data</button>
                <a href="{{ route('admin.input') }}" class="btn-secondary">📑 Input Banyak Data</a>
                <a href="{{ route('admin.permintaan.export-pdf') }}" class="btn-pdf">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </a>
            </div>
        </div>

        <!-- Grafik Permintaan Darah -->
        <div class="chart-container" style="">
            <canvas id="permintaanChart" height="40" width="100"></canvas>
        </div>

        <div class="table-container">
            <table class="data-table" id="myTable">
                <thead>
                    <tr>
                        <th style="width: 200px;">Bulan</th>
                        <th>Golongan A (Kantong)</th>
                        <th>Golongan B (Kantong)</th>
                        <th>Golongan AB (Kantong)</th>
                        <th>Golongan O (Kantong)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataPermintaan as $item)
                        <tr>
                            <td>{{ $item['tanggal'] }}</td>
                            <td>{{ $item['gol_a'] }}</td>
                            <td>{{ $item['gol_b'] }}</td>
                            <td>{{ $item['gol_ab'] }}</td>
                            <td>{{ $item['gol_o'] }}</td>
                            <td style="display: flex; gap: 8px;">
                                <button class="btn-aksi edit-btn"
                                    onclick="editData('{{ $item['tahun'] }}-{{ $item['bulan'] }}')"><i
                                        class="bx bx-edit"></i></button>
                                <button class="btn-aksi delete-btn"
                                    onclick="deleteData('{{ $item['tahun'] }}-{{ $item['bulan'] }}')"><i
                                        class="bx bx-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal input satu data -->
        <div id="modalInput" class="modal hidden">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Input Permintaan Darah</h2>
                    <span class="close" onclick="closeModal()">&times;</span>
                </div>
                <form id="singleDataForm">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="tahun">Tahun</label>
                            <input type="number" id="tahun" name="tahun" class="form-control" required min="2000"
                                max="2100" value="{{ date('Y') }}">
                        </div>

                        <div class="form-group">
                            <label for="bulan">Bulan</label>
                            <select id="bulan" name="bulan" class="form-control" required>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}" {{ $month == date('n') ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="gol_a">Golongan A</label>
                            <input type="number" id="gol_a" name="gol_a" class="form-control" required
                                min="0">
                        </div>

                        <div class="form-group">
                            <label for="gol_b">Golongan B</label>
                            <input type="number" id="gol_b" name="gol_b" class="form-control" required
                                min="0">
                        </div>

                        <div class="form-group">
                            <label for="gol_ab">Golongan AB</label>
                            <input type="number" id="gol_ab" name="gol_ab" class="form-control" required
                                min="0">
                        </div>

                        <div class="form-group">
                            <label for="gol_o">Golongan O</label>
                            <input type="number" id="gol_o" name="gol_o" class="form-control" required
                                min="0">
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal edit data -->
        <div id="modalEdit" class="modal hidden">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Edit Permintaan Darah</h2>
                    <span class="close" onclick="closeEditModal()">&times;</span>
                </div>
                <form id="editDataForm">
                    @csrf
                    <input type="hidden" name="id" id="editId">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="editTahun">Tahun</label>
                            <input type="number" id="editTahun" name="tahun" class="form-control" required
                                min="2000" max="2100">
                        </div>

                        <div class="form-group">
                            <label for="editBulan">Bulan</label>
                            <select id="editBulan" name="bulan" class="form-control" required>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}">
                                        {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="editGolA">Golongan A</label>
                            <input type="number" id="editGolA" name="gol_a" class="form-control" required
                                min="0">
                        </div>

                        <div class="form-group">
                            <label for="editGolB">Golongan B</label>
                            <input type="number" id="editGolB" name="gol_b" class="form-control" required
                                min="0">
                        </div>

                        <div class="form-group">
                            <label for="editGolAB">Golongan AB</label>
                            <input type="number" id="editGolAB" name="gol_ab" class="form-control" required
                                min="0">
                        </div>

                        <div class="form-group">
                            <label for="editGolO">Golongan O</label>
                            <input type="number" id="editGolO" name="gol_o" class="form-control" required
                                min="0">
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" onclick="closeEditModal()" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Confirm Overwrite Modal -->
        <div id="confirmOverwriteModal" class="modal hidden">
            <div class="modal-content">
                <h2>Konfirmasi</h2>
                <p>Data untuk bulan dan tahun ini sudah ada. Apakah Anda ingin menimpanya?</p>
                <div class="modal-actions">
                    <button type="button" onclick="confirmOverwrite()" class="btn-primary">Ya, Timpa</button>
                    <button type="button" onclick="closeOverwriteModal()" class="btn-secondary">Batal</button>
                </div>
            </div>
        </div>

        <!-- Confirm Delete Modal -->
        <div id="confirmDeleteModal" class="modal hidden">
            <div class="modal-content">
                <h2>Konfirmasi Hapus</h2>
                <p>Apakah Anda yakin ingin menghapus data ini?</p>
                <input type="hidden" id="deleteId">
                <div class="modal-actions">
                    <button type="button" onclick="confirmDelete()" class="btn-danger">Ya, Hapus</button>
                    <button type="button" onclick="closeDeleteModal()" class="btn-secondary">Batal</button>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    .btn-aksi {
        border: none;
        background: none;
        padding: 6px 8px;
        margin-right: 4px;
        cursor: pointer;
        font-size: 1rem;
    }

    .edit-btn {
        color: #f59e0b;
    }

    .delete-btn {
        color: #ef4444;
    }

    .btn-aksi:hover {
        transform: scale(1.1);
    }

    /* Table Styles */
    .table-container {
        margin-top: 30px;
        background: #F4F5F7;
        border-radius: 8px;
        overflow: hidden;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background-color: #fee2e2;
        color: #291d1d;
    }

    .data-table th {
        padding: 12px 15px;
        text-align: left;
        font-weight: 500;
    }

    .data-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #978e8e;
        background-color: #F4F5F7;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .data-table tr:hover {
        background-color: #fee2e2;
    }
</style>
@push('styles')
    <style>
        /* DataTables Custom */
        #myTable_wrapper {
            margin-top: 20px;
        }

        /* Modern Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 25px;
            width: 600px;
            /* Fixed width for better control */
            max-width: 90%;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            animation: modalFadeIn 0.3s ease-out;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h2 {
            margin: 0;
            color: #d32f2f;
            font-size: 1.5rem;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s;
        }

        .close:hover {
            color: #333;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #d32f2f;
            outline: none;
            box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.1);
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .btn-aksi {
            border: none;
            background: none;
            padding: 6px 8px;
            margin-right: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .edit-btn {
            color: #f59e0b;
        }

        .delete-btn {
            color: #ef4444;
        }

        .btn-aksi:hover {
            transform: scale(1.1);
        }

        .btn-primary {
            background-color: #d32f2f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #b71c1c;
        }

        .btn-secondary {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
        }

        .btn-danger {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn-danger:hover {
            background-color: #d32f2f;
        }

        /* Notification Toast */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            padding: 15px 20px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            display: flex;
            align-items: center;
            animation: toastSlideIn 0.3s ease-out;
            transform: translateX(0);
        }

        .toast-success {
            background-color: #4CAF50;
        }

        .toast-error {
            background-color: #f44336;
        }

        .toast-warning {
            background-color: #FF9800;
        }

        .toast-icon {
            margin-right: 10px;
            font-size: 20px;
        }

        @keyframes toastSlideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes toastSlideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .modal-content {
                width: 90%;
                margin: 10% auto;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                responsive: true,
                dom: '<"top"lf>rt<"bottom"ip>',
                ordering: false,
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_length select').addClass('form-control');
                }
            });

            // Show success message from session if exists
            @if (session('success'))
                showSuccessAlert('{{ session('success') }}');
            @endif
        });

        // Show success alert function
        function showSuccessAlert(message) {
            Swal.fire({
                title: 'Sukses!',
                text: message,
                icon: 'success',
                confirmButtonColor: '#d32f2f',
                timer: 3000,
                timerProgressBar: true
            });
        }

        // Show error alert function
        function showErrorAlert(message) {
            Swal.fire({
                title: 'Error!',
                text: message,
                icon: 'error',
                confirmButtonColor: '#d32f2f'
            });
        }

        // Data untuk chart
        const chartData = {
            labels: @json($chartLabels),
            datasets: [{
                    label: 'Golongan A',
                    data: @json($chartData['A']),
                    borderColor: '#d32f2f',
                    backgroundColor: 'rgba(211, 47, 47, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Golongan B',
                    data: @json($chartData['B']),
                    borderColor: '#1976d2',
                    backgroundColor: 'rgba(25, 118, 210, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Golongan AB',
                    data: @json($chartData['AB']),
                    borderColor: '#388e3c',
                    backgroundColor: 'rgba(56, 142, 60, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Golongan O',
                    data: @json($chartData['O']),
                    borderColor: '#ffa000',
                    backgroundColor: 'rgba(255, 160, 0, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        };

        // Inisialisasi chart
        const ctx = document.getElementById('permintaanChart').getContext('2d');
        const permintaanChart = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Permintaan Darah per Bulan',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Kantong Darah'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan-Tahun'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'nearest'
                }
            }
        });

        // Modal functions
        function openModal() {
            document.getElementById('modalInput').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('modalInput').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function openEditModal() {
            document.getElementById('modalEdit').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('modalEdit').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        }

        // Form submission with SweetAlert confirmation
        $('#singleDataForm').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: "{{ route('admin.permintaan.single') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.confirm) {
                        Swal.fire({
                            title: 'Data Sudah Ada',
                            text: 'Data untuk bulan dan tahun ini sudah ada. Apakah Anda ingin menimpanya?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d32f2f',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Timpa Data',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const confirmFormData = formData + '&confirm_overwrite=1';
                                submitSingleData(confirmFormData);
                            }
                        });
                    } else {
                        submitSingleData(formData);
                    }
                },
                error: function(xhr) {
                    showErrorAlert('Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        });

        function submitSingleData(formData) {
            $.ajax({
                url: "{{ route('admin.permintaan.single') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    showSuccessAlert(response.success || 'Data berhasil disimpan!');
                    closeModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    showErrorAlert('Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        }

        // Edit data
        $('#editDataForm').submit(function(e) {
            e.preventDefault();
            const id = $('#editId').val();
            const formData = $(this).serialize();

            // Tampilkan loading
            Swal.fire({
                title: 'Menyimpan Perubahan',
                html: 'Sedang menyimpan data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `/admin/permintaan/${id}`,
                type: "PUT",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: 'Data berhasil diperbarui',
                        icon: 'success',
                        confirmButtonColor: '#d32f2f',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire(
                        'Error!',
                        'Gagal memperbarui data.',
                        'error'
                    );
                }
            });
        });

        // Tambahkan fungsi ini di bagian script
        function editData(id) {
            // Ambil tahun dan bulan dari ID
            const [tahun, bulan] = id.split('-');

            // Tampilkan loading
            Swal.fire({
                title: 'Memuat Data',
                html: 'Sedang mengambil data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX request untuk mendapatkan data
            $.ajax({
                url: `/admin/permintaan/${id}/edit`,
                type: "GET",
                success: function(response) {
                    Swal.close();

                    // Isi form edit dengan data yang diterima
                    $('#editId').val(id);
                    $('#editTahun').val(response.tahun);
                    $('#editBulan').val(response.bulan);
                    $('#editGolA').val(response.gol_a);
                    $('#editGolB').val(response.gol_b);
                    $('#editGolAB').val(response.gol_ab);
                    $('#editGolO').val(response.gol_o);

                    // Buka modal edit
                    openEditModal();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Error!',
                        'Gagal memuat data untuk diedit.',
                        'error'
                    );
                }
            });
        }
        // Delete data with confirmation
        function deleteData(id) {
            Swal.fire({
                title: 'Hapus Data?',
                text: "Anda yakin ingin menghapus data ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d32f2f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/permintaan') }}/" + id,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            showSuccessAlert(response.success || 'Data berhasil dihapus!');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        },
                        error: function(xhr) {
                            showErrorAlert('Terjadi kesalahan. Silakan coba lagi.');
                        }
                    });
                }
            });
        }
    </script>
@endpush
