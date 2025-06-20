<!DOCTYPE html>
<html>
<head>
    <title>Laporan Prediksi Darah</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; margin: 0; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #d32f2f; padding-bottom: 10px; }
        .header h1 { color: #d32f2f; margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; font-size: 14px; }
        .logo { height: 70px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #d32f2f; color: white; padding: 10px; text-align: center; }
        td { padding: 8px; border: 1px solid #ddd; text-align: center; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 30px; text-align: right; font-size: 12px; color: #666; }
        .total-row { font-weight: bold; background-color: #ffebee; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo" style="width: 80px; float: left; margin-right: 20px;">
        <h1>LAPORAN PREDIKSI KEBUTUHAN DARAH</h1>
        <p>UDD PMI Kota Lhokseumawe</p>@php
use Carbon\Carbon;
@endphp
        <p>Dicetak pada: {{ Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan-Tahun</th>
                <th>Gol. A</th>
                <th>Gol. B</th>
                <th>Gol. AB</th>
                <th>Gol. O</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
    @php 
        $total = ['A' => 0, 'B' => 0, 'AB' => 0, 'O' => 0];
        $hasData = count($predictions) > 0;
    @endphp
    
    @if($hasData)
        @foreach($predictions as $key => $item)
            @if(is_array($item)) {{-- Pastikan $item adalah array --}}
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['tanggal'] ?? '' }}</td>
                    <td>{{ number_format($item['gol_a'] ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($item['gol_b'] ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($item['gol_ab'] ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($item['gol_o'] ?? 0, 0, ',', '.') }}</td>
                    <td>
                        {{ number_format( 
                            ($item['gol_a'] ?? 0) + 
                            ($item['gol_b'] ?? 0) + 
                            ($item['gol_ab'] ?? 0) + 
                            ($item['gol_o'] ?? 0), 
                            0, ',', '.' 
                        ) }}
                    </td>
                </tr>
                @php
                    $total['A'] += $item['gol_a'] ?? 0;
                    $total['B'] += $item['gol_b'] ?? 0;
                    $total['AB'] += $item['gol_ab'] ?? 0;
                    $total['O'] += $item['gol_o'] ?? 0;
                @endphp
            @endif
        @endforeach
    @else
        <tr>
            <td colspan="7" style="text-align: center;">Tidak ada data prediksi</td>
        </tr>
    @endif

    @if($hasData)
        <tr class="total-row">
            <td colspan="2">TOTAL</td>
            <td>{{ number_format($total['A'], 0, ',', '.') }}</td>
            <td>{{ number_format($total['B'], 0, ',', '.') }}</td>
            <td>{{ number_format($total['AB'], 0, ',', '.') }}</td>
            <td>{{ number_format($total['O'], 0, ',', '.') }}</td>
            <td>{{ number_format(array_sum($total), 0, ',', '.') }}</td>
        </tr>
    @endif
</tbody>
    </table>

    {{-- <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name ?? 'Admin' }}</p>
    </div> --}}
</body>
</html>