@extends('layouts.app')

@section('content')
    <div class="container-permintaan">
        <div class="header-permintaan">
            <h1>Data Permintaan Darah</h1>

            <div class="filter-group">
                <select name="tahun" id="tahunFilter" class="filter-select">
                    <option value="">Semua Tahun</option>
                    @foreach ($tahunTersedia as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
                <select name="golongan" id="golonganFilter" class="filter-select">
                    <option value="">Semua Golongan</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                    <option value="O">O</option>
                </select>
                <button class="filter-btn" onclick="filterData()">Filter</button>
            </div>

            <div class="action-group">
                <button class="btn-primary" onclick="openModal()">+ Input Satu Data</button>
                <a href="{{ route('admin.input') }}" class="btn-secondary">📑 Input Banyak Data</a>
            </div>
        </div>

        <!-- Grafik Permintaan Darah -->
        <div class="chart-container" style="">
            <h2 style="color: #d32f2f; margin-bottom: 20px;">Grafik Permintaan Darah</h2>
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
                                <button class="btn-edit">Edit</button>
                                <button class="btn-delete">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal input satu data -->
        <div id="modalInput" class="modal hidden">
            <div class="modal-content">
                <h2>Input Permintaan Darah</h2>
                <form>
                    <label>Tanggal:</label>
                    <input type="date" name="tanggal" required>

                    <label>Golongan Darah:</label>
                    <select name="golongan_darah" required>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>

                    <label>Jumlah (kantong):</label>
                    <input type="number" name="jumlah" min="1" required>

                    <div class="modal-actions">
                        <button type="submit" class="btn-primary">Simpan</button>
                        <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* DataTables Custom */
        #myTable_wrapper {
            margin-top: 20px;
        }

        #myTable thead th {
            background-color: #d32f2f !important;
            color: white !important;
        }

        #myTable tbody tr:nth-child(even) {
            background-color: #ffebee !important;
        }

        #myTable tbody tr:hover {
            background-color: #ffcdd2 !important;
        }

        .dataTables_filter input {
            border: 1px solid #ddd !important;
            padding: 5px !important;
        }

        .dataTables_length select {
            border: 1px solid #ddd !important;
            padding: 5px !important;
        }
    </style>
    <style>
        .chart-container {
            border: 1px solid #ffcdd2;
            background-color: #ffebee;
        }

        .filter-btn {
            background-color: #d32f2f;
            color: white;
        }

        .filter-btn:hover {
            background-color: #b71c1c;
        }

        .btn-primary {
            background-color: #d32f2f;
            border-color: #d32f2f;
        }

        .btn-primary:hover {
            background-color: #b71c1c;
            border-color: #b71c1c;
        }

        .data-table th {
            background-color: #d32f2f;
            color: white;
        }

        .data-table tr:nth-child(even) {
            background-color: #ffebee;
        }

        .data-table tr:hover {
            background-color: #ffcdd2;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                responsive: true,
                dom: '<"top"lf>rt<"bottom"ip>',
                ordering: false, // Ini yang menonaktifkan sorting
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_length select').addClass('form-control');
                }
            });
        });
    </script>
    <script>
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

        function openModal() {
            document.getElementById('modalInput').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modalInput').classList.add('hidden');
        }

        function filterData() {
            const tahun = document.getElementById('tahunFilter').value;
            const golongan = document.getElementById('golonganFilter').value;

            // Implementasi filter sesuai kebutuhan
            console.log('Filter:', tahun, golongan);
            // Di sini bisa tambahkan AJAX request untuk filter data
        }
    </script>
@endpush
