<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengujian Prediksi</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 5px;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 5px;
            padding-bottom: 5px;
            border-bottom: 1px solid #d32f2f;
        }
        .header h1 {
            color: #d32f2f;
            margin: 0;
            font-size: 14px;
            line-height: 1.2;
        }
        .header p {
            margin: 2px 0;
            font-size: 9px;
        }
        .logo {
            height: 40px;
            margin-bottom: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 9px;
            page-break-inside: avoid;
        }
        th {
            background-color: #d32f2f;
            color: white;
            padding: 4px;
            text-align: center;
            font-weight: bold;
        }
        td {
            padding: 3px;
            border: 1px solid #ddd;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 5px;
            text-align: right;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 3px;
        }
        .blood-type {
            font-weight: bold;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
            min-width: 20px;
            text-align: center;
        }
        .blood-type-a { background-color: #d32f2f; }
        .blood-type-b { background-color: #1976d2; }
        .blood-type-ab { background-color: #388e3c; }
        .blood-type-o { background-color: #ffa000; }
        .positive { color: #388e3c; }
        .negative { color: #d32f2f; }
        .error-high { background-color: rgba(220, 38, 38, 0.1); }
        .error-low { background-color: rgba(22, 101, 52, 0.1); }
        .section-title {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 3px;
            margin-top: 5px;
            border-left: 3px solid #d32f2f;
        }
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo" style="width: 50px; float: left; margin-right: 10px;">
        <div style="overflow: hidden;">
            <h1>LAPORAN PENGUJIAN PREDIKSI KEBUTUHAN DARAH</h1>
            <p>UDD PMI Kota Lhokseumawe | Tahun: {{ $tahun }} | Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }}</p>
            <p>Oleh: {{ Auth::user()->name ?? 'Admin' }}</p>
        </div>
    </div>

    @foreach($data as $golongan => $group)
    <div class="section-title">
        Golongan Darah <span class="blood-type blood-type-{{ strtolower($golongan) }}">{{ $golongan }}</span>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Bulan</th>
                <th style="width: 15%;">Aktual</th>
                <th style="width: 15%;">Prediksi</th>
                <th style="width: 15%;">Selisih</th>
                <th style="width: 15%;">Error (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($group['items'] as $item)
            <tr>
                <td>{{ $item['bulan'] }}</td>
                <td>{{ number_format($item['aktual'], 0, ',', '.') }}</td>
                <td>{{ number_format($item['prediksi'], 0, ',', '.') }}</td>
                <td class="{{ $item['selisih'] >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($item['selisih'], 0, ',', '.') }}
                </td>
                <td class="{{ $item['error'] > 20 ? 'error-high' : 'error-low' }}">
                    {{ number_format($item['error'], 2, ',', '.') }}%
                </td>
            </tr>
            @endforeach
            
            <tr class="total-row">
                <td>TOTAL</td>
                <td>{{ number_format($group['total']['aktual'], 0, ',', '.') }}</td>
                <td>{{ number_format($group['total']['prediksi'], 0, ',', '.') }}</td>
                <td class="{{ $group['total']['selisih'] >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($group['total']['selisih'], 0, ',', '.') }}
                </td>
                <td>
                    {{ number_format($group['total']['error'], 2, ',', '.') }}%
                </td>
            </tr>
        </tbody>
    </table>
    @endforeach

    <div class="footer">
        <p>Sistem Prediksi Kebutuhan Darah - UDD PMI Kota Lhokseumawe</p>
    </div>
</body>
</html>