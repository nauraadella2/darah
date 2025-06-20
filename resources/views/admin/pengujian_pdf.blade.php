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

    @foreach($data as $golongan => $items)
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
            @php
                $totalAktual = 0;
                $totalPrediksi = 0;
                $totalError = 0;
            @endphp
            
            @foreach($items as $item)
            <tr style="font-weight: bold; background-color: #f5f5f5;">
    <td>TOTAL</td>
    <td>{{ number_format($totalAktual, 0, ',', '.') }}</td>
    <td>{{ number_format($totalPrediksi, 0, ',', '.') }}</td>
    <td class="{{ ($totalPrediksi - $totalAktual) >= 0 ? 'positive' : 'negative' }}">
        {{ number_format($totalPrediksi - $totalAktual, 0, ',', '.') }}
    </td>
    <td>
        {{ count($items) > 0 ? number_format($totalError / count($items), 2, ',', '.') . '%' : '0%' }}
    </td>
</tr>
            @php
                $totalAktual += $item['aktual'];
                $totalPrediksi += $item['prediksi'];
                $totalError += abs($item['error']);
            @endphp
            @endforeach
            
            <tr style="font-weight: bold; background-color: #f5f5f5;">
                <td>TOTAL</td>
                <td>{{ number_format($totalAktual, 0, ',', '.') }}</td>
                <td>{{ number_format($totalPrediksi, 0, ',', '.') }}</td>
                <td class="{{ ($totalPrediksi - $totalAktual) >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($totalPrediksi - $totalAktual, 0, ',', '.') }}
                </td>
                <td>
                    {{ $items->count() > 0 ? number_format($totalError / $items->count(), 2, ',', '.') . '%' : '0%' }}
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