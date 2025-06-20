@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="prediction-header">
            <h2><i class="bx bx-test-tube"></i> Pengujian</h2>
            <div class="header-info">
                <span class="training-data"
                    style="color: #e63946; font-weight: 500; background-color: #ffebee; padding: 4px 8px; border-radius: 4px; border-left: 3px solid #c62828;">
                    Membandingkan tahun {{ $tahun['tahun'] }} (aktual) dengan {{ $tahun['tahun'] }} (prediksi)
                </span>
                <a href="{{ route('admin.pengujian.export-pdf') }}" class="btn-pdf">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
            <div class="card-body">
                <div class="filter-controls">
                    <div class="filter-group">
                        <select id="bloodTypeFilter">
                            <option value="all">Semua Golongan</option>
                            <option value="A">Golongan A</option>
                            <option value="B">Golongan B</option>
                            <option value="AB">Golongan AB</option>
                            <option value="O">Golongan O</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button id="resetFilter" class="btn-secondary">
                            <i class="bx bx-refresh"></i> Reset Filter
                        </button>
                        <button id="exportData" class="btn-primary">
                            Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Perbandingan -->
        <div class="chart-card"
            style="border: 1px solid #f0f0f0; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div class="card-header" style="padding: 3px; border-bottom: 1px solid #f5f5f5; background-color: #fff;">
                <h3 style="margin: 0; font-size: 0.9 rem; color: #333; display: flex; align-items: center; gap: 8px;">
                    <i style="color: #e63946; font-size: 1.0rem;"></i>
                    Grafik Perbandingan
                </h3>
                <div class="chart-controls" style="margin-top: 8px;">
                    <button id="toggleChartType" class="btn-outline"
                        style="background: none; border: 1px solid #e0e0e0; border-radius: 4px; padding: 4px 8px; font-size: 0.85rem; color: #555; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 4px;">
                        <i class="bx bx-bar-chart" style="color: #e63946;"></i>
                        Bar Chart
                    </button>
                </div>
            </div>
            <div class="card-body" style="padding: 16px; background-color: #fafafa;">
                <canvas id="comparisonChart" height="40" width="100"></canvas>
            </div>
        </div>

        <!-- Tabel Perbandingan -->
        <div class="table-card">
            <div class="card-header">
                <h3>Tabel Perbandingan</h3>
                <div class="table-controls">
                    <span class="table-info" id="tableInfo">Total: {{ count($hasilPengujian) }} data</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table id="comparisonTable">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Golongan</th>
                                <th>Permintaan Aktual</th>
                                <th>Hasil Prediksi</th>
                                <th>Selisih</th>
                                <th>Error (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hasilPengujian as $data)
                                <tr data-blood-type="{{ $data['golongan'] }}">
                                    <td>{{ $data['bulan'] }}</td>
                                    <td>
                                        <span class="blood-type-badge blood-type-{{ strtolower($data['golongan']) }}">
                                            {{ $data['golongan'] }}
                                        </span>
                                    </td>
                                    <td>{{ $data['permintaan_aktual'] }}</td>
                                    <td>{{ $data['hasil_prediksi'] }}</td>
                                    <td class="{{ $data['selisih'] >= 0 ? 'positive' : 'negative' }}">
                                        {{ $data['selisih'] }}
                                    </td>
                                    <td class="{{ abs($data['error']) > 20 ? 'error-high' : 'error-low' }}">
                                        {{ $data['error'] }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="dashboard-cards">
            <!-- Card Total Permintaan Darah -->
            <div class="dashboard-card total-card">
                <div class="card-header">
                    <h3><i class="bx bxs-droplet"></i> Total Permintaan Darah</h3>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table id="comparisonTable">
                            <thead>
                                <tr>
                                    <th class="ps-4">Golongan Darah</th>
                                    <th class="text-end">Rata-rata MAPE</th>
                                    <th class="text-end pe-4">Jumlah Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($summaryResults as $summary)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                                {{ $summary['golongan'] }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span
                                                class="fw-semibold {{ $summary['mape'] > 20 ? 'text-danger' : ($summary['mape'] > 10 ? 'text-warning' : 'text-success') }}">
                                                {{ number_format($summary['mape'], 2) }}%
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">{{ $summary['jumlah_data'] }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-light fw-bold">
                                    <td class="ps-4">Total Rata-rata</td>
                                    <td class="text-end">
                                        <span
                                            class="{{ $mape > 20 ? 'text-danger' : ($mape > 10 ? 'text-warning' : 'text-success') }}">
                                            {{ number_format($mape, 2) }}%
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        {{ array_sum(array_column($summaryResults, 'jumlah_data')) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card Action Buttons -->
            <div class="dashboard-card action-card">
                <div class="card-header">
                    <h3><i class="bx bx-plus-circle"></i>Prediksi Terbaik</h3>
                </div>
                <div class="card-body action-buttons">
                    <button style="border: none"class="btn-action btn-one-data">
                        Golongan {{ $bestPrediction['golongan'] }} ({{ $bestPrediction['bulan'] }})<br>
                        <span class="fw-medium">{{ number_format($bestPrediction['error'], 2) }}% MAPE</span>
                    </button>
                    <button style="border: none"class="btn-action btn-two-data">
                        Golongan {{ $worstPrediction['golongan'] }} ({{ $worstPrediction['bulan'] }})<br>
                                    <span class="fw-medium">{{ number_format($worstPrediction['error'], 2) }}% MAPE</span>
                    </button>
                </div>
            </div>
        </div>


        <style>
            /* Tema Merah Utama */
            :root {
                --primary-color: #dc2626;
                --primary-light: #fee2e2;
                --primary-dark: #7f1d1d;
                --secondary-color: #b91c1c;
                --accent-color: #ef4444;
                --text-color: #333;
                --light-bg: #eeecec;
                --success-color: #166534;
                --error-color: #b91c1c;
                --warning-color: #d97706;
                --info-color: #2563eb;
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: var(--text-color);
                background-color: var(--light-bg);
            }

            .btn-action {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px 16px;
                font-size: 0.95rem;
                text-decoration: none;
                color: #fff;
                border-radius: 8px;
                transition: all 0.2s ease;
                justify-content: center;
                font-weight: 500;
            }

            .btn-one-data {
                background-color: #a0c7a4;
            }

            .btn-two-data {
                background-color: #f97316;
            }   

            .btn-action:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            }

            .btn-one-data:hover {
                background-color: #dc2626;
            }

            .btn-two-data:hover {
                background-color: #ea580c;
            }

            .dashboard-container {
                padding: 20px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            .dashboard-header {
                margin-bottom: 20px;
                color: #333;
            }

            .dashboard-header h2 {
                font-weight: 600;
                font-size: 1.8rem;
            }

            .dashboard-header i {
                color: #ef4444;
            }

            .card-body {
                padding: 20px;
            }

            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .dashboard-cards {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
            }

            .dashboard-card {
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                flex: 1;
                min-width: 300px;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .dashboard-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .total-card {
                flex: 2;
            }

            .action-card {
                flex: 1;
            }

            .card-header {
                padding: 16px 20px;
                border-bottom: 1px solid #f3f4f6;
                font-size: 16px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px;
                color: #333;
                background-color: #fff;
                border-radius: 10px 10px 0 0;
            }

            .card-header h3 {
                margin: 0;
                font-size: 1.1rem;
            }

            .card-header i {
                color: #ef4444;
            }

            .card-body {
                padding: 20px;
            }

            .total-count {
                font-size: 2.5rem;
                font-weight: 700;
                color: #ef4444;
                margin: 10px 0;
            }

            .total-count span {
                font-size: 1rem;
                color: #6b7280;
                font-weight: 500;
            }

            .update-info {
                margin-top: 10px;
                font-size: 0.875rem;
                color: #6b7280;
            }

            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .btn-action {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px 16px;
                font-size: 0.95rem;
                text-decoration: none;
                color: #fff;
                border-radius: 8px;
                transition: all 0.2s ease;
                justify-content: center;
                font-weight: 500;
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Header */
            .prediction-header {
                margin-bottom: 30px;
                padding-bottom: 15px;
                border-bottom: 2px solid var(--primary-color);
            }

            .prediction-header h2 {
                color: var(--primary-dark);
                margin-bottom: 5px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .header-info {
                color: #666;
                font-size: 0.9rem;
            }

            /* Cards */
            .chart-card,
            .table-card,
            .summary-card,
            .filter-card {
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                margin-bottom: 25px;
                overflow: hidden;
            }

            .card-header {
                background-color: var(--primary-light);
                color: white;
                padding: 5px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
            }

            .card-header h3 {
                margin: 0;
                font-size: 1.1rem;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .card-body {
                padding: 20px;
            }

            /* Filter Styles */
            .filter-controls {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 20px;
                margin-bottom: 15px;
            }

            .filter-group {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .filter-group label {
                font-weight: 600;
                color: var(--text-color);
                white-space: nowrap;
            }

            .filter-group select {
                padding: 8px 12px;
                border: 2px solid var(--primary-light);
                border-radius: 6px;
                font-size: 0.9rem;
                background: white;
                color: var(--text-color);
                min-width: 150px;
                transition: all 0.2s ease;
            }

            .filter-group select:focus {
                outline: none;
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
            }

            .filter-actions {
                display: flex;
                gap: 10px;
                margin-left: auto;
            }

            .btn-primary,
            .btn-secondary,
            .btn-outline {
                padding: 8px 16px;
                border: none;
                border-radius: 6px;
                font-size: 0.9rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                gap: 6px;
                text-decoration: none;
            }

            .btn-primary {
                background-color: var(--primary-color);
                color: white;
            }

            .btn-primary:hover {
                background-color: var(--primary-dark);
                transform: translateY(-1px);
            }

            .btn-secondary {
                background-color: #6b7280;
                color: white;
            }

            .btn-secondary:hover {
                background-color: #4b5563;
                transform: translateY(-1px);
            }

            .btn-outline {
                background-color: transparent;
                color: white;
                border: 2px solid white;
            }

            .btn-outline:hover {
                background-color: white;
                color: var(--primary-color);
            }

            .filter-summary {
                padding: 10px 15px;
                background-color: var(--primary-light);
                border-radius: 6px;
                font-size: 0.9rem;
            }

            /* Chart Controls */
            .chart-controls {
                display: flex;
                gap: 10px;
            }

            /* Table Controls */
            .table-controls {
                display: flex;
                align-items: center;
            }

            .table-info {
                color: rgb(49, 44, 44);
                font-size: 0.9rem;
            }

            /* Blood Type Badges */
            .blood-type-badge {
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 0.8rem;
                font-weight: bold;
                color: white;
            }

            .blood-type-a {
                background-color: var(--primary-color);
            }

            .blood-type-b {
                background-color: var(--info-color);
            }

            .blood-type-ab {
                background-color: var(--success-color);
            }

            .blood-type-o {
                background-color: var(--warning-color);
            }

            /* Enhanced Table Styles */
            .table-container {
                overflow-x: auto;
            }

            #comparisonTable {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            #comparisonTable thead {
                background-color: var(--primary-light);
                color: white;
            }

            #comparisonTable th {
                padding: 5px;
                text-align: center;
                font-weight: 600;
                letter-spacing: 0.5px;
                border: none;
                color: rgb(56, 40, 40);
                background-color: var(--primary-light);
            }

            #comparisonTable td {
                padding: 12px 15px;
                text-align: center;
                border-bottom: 1px solid #f0f0f0;
                transition: all 0.2s ease;
            }

            #comparisonTable tr:not(:last-child) td {
                border-bottom: 1px solid #f0f0f0;
            }

            #comparisonTable tr:nth-child(even) {
                background-color: #fafafa;
            }

            #comparisonTable tr:hover td {
                background-color: var(--primary-light);
                transform: translateX(2px);
            }

            #comparisonTable tr.hidden {
                display: none;
            }

            /* Enhanced Error Styles */
            .error-high {
                background-color: rgba(220, 38, 38, 0.1);
                font-weight: bold;
                position: relative;
            }

            .error-high::after {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                background-color: var(--error-color);
                border-radius: 2px;
            }

            .error-low {
                background-color: rgba(22, 101, 52, 0.1);
                position: relative;
            }

            .error-low::after {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                background-color: var(--success-color);
                border-radius: 2px;
            }

            .positive {
                color: var(--success-color);
                font-weight: 600;
            }

            .negative {
                color: var(--error-color);
                font-weight: 600;
            }

            /* Summary Stats */
            .summary-stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
            }

            .stat-item {
                background: white;
                border-radius: 8px;
                padding: 15px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                border-left: 4px solid var(--primary-color);
                transition: all 0.2s ease;
            }

            .stat-item:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .stat-label {
                display: block;
                font-size: 0.9rem;
                color: #666;
                margin-bottom: 5px;
            }

            .stat-value {
                font-size: 1.2rem;
                font-weight: bold;
                color: var(--primary-dark);
            }

            /* Responsif */
            @media (max-width: 768px) {
                .filter-controls {
                    flex-direction: column;
                    align-items: stretch;
                }

                .filter-actions {
                    margin-left: 0;
                    justify-content: center;
                }

                .summary-stats {
                    grid-template-columns: 1fr;
                }

                .chart-controls {
                    flex-wrap: wrap;
                }

                #comparisonTable {
                    display: block;
                    overflow-x: auto;
                    white-space: nowrap;
                }

                #comparisonTable th,
                #comparisonTable td {
                    min-width: 120px;
                }
            }


            /* Style unik untuk komponen Riwayat Prediksi */
            .pantau-table th,
            .pantau-table td {
                vertical-align: middle;
                padding: 12px 16px;
                font-size: 14px;
            }

            .pantau-th {
                font-weight: 600;
                background-color: #f9f9fb;
                color: #5a5a5a;
                border-bottom: 1px solid #dee2e6;
            }

            .pantau-td {
                color: #333;
                border-top: 1px solid #f0f0f0;
            }

            .pantau-table tbody tr:hover {
                background-color: #fff3f3;
                /* merah lembut saat hover */
            }

            .pantau-card .card-header h5 i {
                color: #dc3545;
            }
        </style>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data dari PHP
            const hasilPengujian = @json($hasilPengujian);
            let currentChart = null;
            let currentChartType = 'line';

            // Elements
            const bloodTypeFilter = document.getElementById('bloodTypeFilter');
            const resetFilterBtn = document.getElementById('resetFilter');
            const exportDataBtn = document.getElementById('exportData');
            const toggleChartTypeBtn = document.getElementById('toggleChartType');
            const filterSummary = document.getElementById('filterSummary');
            const tableInfo = document.getElementById('tableInfo');

            // Initialize
            initializeChart();
            setupEventListeners();

            function setupEventListeners() {
                bloodTypeFilter.addEventListener('change', handleFilterChange);
                resetFilterBtn.addEventListener('click', resetFilter);
                exportDataBtn.addEventListener('click', exportData);
                toggleChartTypeBtn.addEventListener('click', toggleChartType);
            }

            function handleFilterChange() {
                const selectedType = bloodTypeFilter.value;
                filterTableData(selectedType);
                updateChart(selectedType);
                updateSummary(selectedType);
                updateFilterSummary(selectedType);
            }

            function filterTableData(bloodType) {
                const rows = document.querySelectorAll('#comparisonTable tbody tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    const rowBloodType = row.dataset.bloodType;
                    if (bloodType === 'all' || rowBloodType === bloodType) {
                        row.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                tableInfo.textContent = `Total: ${visibleCount} data`;
            }

            function updateChart(bloodType) {
                const ctx = document.getElementById('comparisonChart').getContext('2d');

                // Filter data berdasarkan golongan darah
                let filteredData = hasilPengujian;
                if (bloodType !== 'all') {
                    filteredData = hasilPengujian.filter(item => item.golongan === bloodType);
                }

                // Kelompokkan data per golongan darah
                const groupedData = {
                    'A': {
                        actual: [],
                        predicted: [],
                        months: []
                    },
                    'B': {
                        actual: [],
                        predicted: [],
                        months: []
                    },
                    'AB': {
                        actual: [],
                        predicted: [],
                        months: []
                    },
                    'O': {
                        actual: [],
                        predicted: [],
                        months: []
                    }
                };

                // Isi data
                filteredData.forEach(item => {
                    if (groupedData[item.golongan]) {
                        groupedData[item.golongan].actual.push(item.permintaan_aktual);
                        groupedData[item.golongan].predicted.push(item.hasil_prediksi);
                        groupedData[item.golongan].months.push(item.bulan);
                    }
                });

                // Warna untuk setiap golongan
                const bloodTypeColors = {
                    'A': {
                        color: '#dc2626',
                        bgColor: 'rgba(220, 38, 38, 0.1)'
                    },
                    'B': {
                        color: '#2563eb',
                        bgColor: 'rgba(37, 99, 235, 0.1)'
                    },
                    'AB': {
                        color: '#16a34a',
                        bgColor: 'rgba(22, 163, 74, 0.1)'
                    },
                    'O': {
                        color: '#d97706',
                        bgColor: 'rgba(217, 119, 6, 0.1)'
                    }
                };

                // Siapkan dataset untuk chart
                const datasets = [];

                Object.keys(groupedData).forEach(type => {
                    if (groupedData[type].months.length > 0) {
                        // Data aktual
                        datasets.push({
                            label: `Aktual ${type}`,
                            data: groupedData[type].actual,
                            borderColor: bloodTypeColors[type].color,
                            backgroundColor: bloodTypeColors[type].bgColor,
                            borderWidth: currentChartType === 'line' ? 2 : 0,
                            borderDash: currentChartType === 'line' ? [5, 5] : [],
                            tension: 0.1,
                            fill: false
                        });

                        // Data prediksi
                        datasets.push({
                            label: `Prediksi ${type}`,
                            data: groupedData[type].predicted,
                            borderColor: bloodTypeColors[type].color,
                            backgroundColor: currentChartType === 'bar' ? bloodTypeColors[type]
                                .color : bloodTypeColors[type].bgColor,
                            borderWidth: currentChartType === 'line' ? 3 : 0,
                            tension: 0.3,
                            fill: false
                        });
                    }
                });

                // Hapus chart lama
                if (currentChart) {
                    currentChart.destroy();
                }

                // Buat chart baru
                currentChart = new Chart(ctx, {
                    type: currentChartType,
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt',
                            'Nov', 'Des'
                        ],
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: `Perbandingan Aktual vs Prediksi ${bloodType === 'all' ? 'Semua Golongan' : 'Golongan ' + bloodType}`,
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                },
                                color: '#7f1d1d'
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.raw} kantong`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Jumlah Kantong Darah',
                                    color: '#7f1d1d'
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Bulan',
                                    color: '#7f1d1d'
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
            }

            function updateSummary(bloodType) {
                let filteredData = hasilPengujian;
                if (bloodType !== 'all') {
                    filteredData = hasilPengujian.filter(item => item.golongan === bloodType);
                }

                if (filteredData.length === 0) {
                    document.getElementById('avgError').textContent = '0%';
                    document.getElementById('bestPred').textContent = 'Tidak ada data';
                    document.getElementById('worstPred').textContent = 'Tidak ada data';
                    document.getElementById('dataCount').textContent = '0';
                    return;
                }

                // Hitung rata-rata error
                const totalError = filteredData.reduce((sum, item) => sum + Math.abs(parseFloat(item.error)), 0);
                const avgError = (totalError / filteredData.length).toFixed(2);

                // Cari prediksi terbaik dan terburuk
                const sortedByError = [...filteredData].sort((a, b) => Math.abs(a.error) - Math.abs(b.error));
                const bestPred = sortedByError[0];
                const worstPred = sortedByError[sortedByError.length - 1];

                // Update UI
                document.getElementById('avgError').textContent = `${avgError}%`;
                document.getElementById('bestPred').textContent = `${bestPred.golongan} (${bestPred.error}%)`;
                document.getElementById('worstPred').textContent = `${worstPred.golongan} (${worstPred.error}%)`;
                document.getElementById('dataCount').textContent = filteredData.length;
            }

            function updateFilterSummary(bloodType) {
                const summaryText = bloodType === 'all' ? 'Semua Golongan Darah' : `Golongan ${bloodType}`;
                filterSummary.innerHTML = `<span>Menampilkan: <strong>${summaryText}</strong></span>`;
            }

            function resetFilter() {
                bloodTypeFilter.value = 'all';
                handleFilterChange();
            }

            function toggleChartType() {
                currentChartType = currentChartType === 'line' ? 'bar' : 'line';
                toggleChartTypeBtn.innerHTML = currentChartType === 'line' ?
                    '<i class="bx bx-bar-chart"></i> Bar Chart' :
                    '<i class="bx bx-line-chart"></i> Line Chart';

                const selectedType = bloodTypeFilter.value;
                updateChart(selectedType);
            }

            // Replace the exportData function with this:
            function saveData() {
                const selectedType = bloodTypeFilter.value;
                let filteredData = hasilPengujian;

                if (selectedType !== 'all') {
                    filteredData = hasilPengujian.filter(item => item.golongan === selectedType);
                }

                // Show loading state
                exportDataBtn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Menyimpan...';
                exportDataBtn.disabled = true;

                // Send data to server
                fetch('{{ route('admin.pengujian.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            data: filteredData
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Data berhasil disimpan!');
                        } else {
                            console.log(data.message)
                            alert('Gagal menyimpan data: ' + (data.message || 'Terjadi kesalahan'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyimpan data');
                    })
                    .finally(() => {
                        exportDataBtn.innerHTML = '<i class="bx bx-save"></i> Simpan Data';
                        exportDataBtn.disabled = false;
                    });
            }

            // Update the event listener setup:
            function setupEventListeners() {
                bloodTypeFilter.addEventListener('change', handleFilterChange);
                resetFilterBtn.addEventListener('click', resetFilter);
                exportDataBtn.addEventListener('click', saveData); // Changed from exportData to saveData
                toggleChartTypeBtn.addEventListener('click', toggleChartType);
            }

            function initializeChart() {
                updateChart('all');
            }
        });
    </script>
@endpush
