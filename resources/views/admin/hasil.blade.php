@extends('layouts.app')

@section('content')
    <div class="container-fluid" style="padding: 20px 0;">
        <div class="card shadow" style="border: none; border-radius: 8px;">
            <div class="card-header"
                style="background-color: #4e73df; color: white; padding: 15px 20px; border-radius: 8px 8px 0 0;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h4 style="margin: 0; font-size: 1.25rem;">
                        <i class="fas fa-chart-line" style="margin-right: 10px;"></i>Hasil Prediksi Kebutuhan Darah
                    </h4>
                    <div>
                        <span class="badge" style="background-color: white; color: #333; margin-right: 8px;">
                            <i class="fas fa-tint" style="margin-right: 5px;"></i>{{ $bloodType }}
                        </span>
                        <span class="badge" style="background-color: white; color: #333; margin-right: 8px;">
                            <i class="fas fa-calendar" style="margin-right: 5px;"></i>{{ $year }}
                        </span>
                        <span class="badge" style="background-color: white; color: #333;">
                            <i class="fas fa-calculator" style="margin-right: 5px;"></i>Alpha: {{ $alpha }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body" style="padding: 20px;">
                @if ($mode)
                    <div class="alert alert-info" style="padding: 10px 15px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between;">
                            <div><i class="fas fa-info-circle" style="margin-right: 8px;"></i>Mode:
                                <strong>{{ ucfirst($mode) }}</strong></div>
                            <div>Smoothed: <strong>{{ $lastSmoothed }}</strong></div>
                        </div>
                    </div>
                @endif

                <div class="row" style="margin: 0 -10px;">
                    @foreach ($data as $forecast)
                        <div class="col-xl-3 col-md-4 col-sm-6" style="padding: 10px;">
                            <div class="card"
                                style="border-left: 3px solid #4e73df; height: 100%; border-radius: 6px; transition: all 0.2s ease;">
                                <div class="card-body" style="padding: 15px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <h6 style="color: #4e73df; margin: 0; font-size: 0.9rem;">
                                            <i class="far fa-calendar"
                                                style="margin-right: 8px;"></i>{{ $forecast['period'] }}
                                        </h6>
                                        <span class="badge"
                                            style="background-color: #4e73df; color: white; border-radius: 50px; padding: 5px 10px;">
                                            {{ $forecast['forecast'] }}
                                        </span>
                                    </div>
                                    <hr style="margin: 8px 0; border-color: rgba(0,0,0,0.1);">
                                    <div style="display: flex;">
                                        <div style="flex: 1;">
                                            <small style="color: #6c757d; font-size: 0.75rem;">Base</small>
                                            <p style="margin: 0; font-size: 0.85rem;">{{ $forecast['base_forecast'] }}</p>
                                        </div>
                                        <div style="flex: 1;">
                                            <small style="color: #6c757d; font-size: 0.75rem;">Adj</small>
                                            <p style="margin: 0; font-size: 0.85rem;">{{ $forecast['seasonal_adjustment'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top: 20px;">
                    <div class="row">
                        <div class="col-md-7" style="padding-right: 15px;">
                            <div class="card" style="margin-bottom: 20px;">
                                <div class="card-header" style="background-color: #f8f9fa; padding: 10px 15px;">
                                    <h6 style="margin: 0; font-size: 0.9rem;"><i class="fas fa-history"
                                            style="margin-right: 8px;"></i>Detail Perhitungan</h6>
                                </div>
                                <div class="card-body" style="padding: 0;">
                                    <div style="overflow-x: auto;">
                                        <table style="width: 100%; margin-bottom: 0; font-size: 0.85rem;">
                                            <thead style="background-color: #f8f9fa;">
                                                <tr>
                                                    <th
                                                        style="padding: 8px 10px; text-align: left; border: 1px solid #dee2e6;">
                                                        Periode</th>
                                                    <th
                                                        style="padding: 8px 10px; text-align: left; border: 1px solid #dee2e6;">
                                                        Aktual</th>
                                                    <th
                                                        style="padding: 8px 10px; text-align: left; border: 1px solid #dee2e6;">
                                                        Smoothed</th>
                                                    <th
                                                        style="padding: 8px 10px; text-align: left; border: 1px solid #dee2e6;">
                                                        Forecast</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($historicalCalculations as $calc)
                                                    <tr>
                                                        <td style="padding: 8px 10px; border: 1px solid #dee2e6;">
                                                            {{ $calc['period'] }}</td>
                                                        <td style="padding: 8px 10px; border: 1px solid #dee2e6;">
                                                            {{ $calc['actual'] }}</td>
                                                        <td style="padding: 8px 10px; border: 1px solid #dee2e6;">
                                                            {{ $calc['smoothed'] }}</td>
                                                        <td style="padding: 8px 10px; border: 1px solid #dee2e6;">
                                                            {{ $calc['forecast'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5" style="padding-left: 15px;">
                            <div class="card" style="margin-bottom: 15px;">
                                <div class="card-body" style="padding: 15px;">
                                    <h6 style="margin-top: 0; margin-bottom: 10px; font-size: 0.9rem;">
                                        <i class="fas fa-info-circle" style="margin-right: 8px;"></i>Informasi Metode
                                    </h6>
                                    <p style="margin-bottom: 5px; font-size: 0.85rem;"><strong>Exponential
                                            Smoothing</strong> (α={{ $alpha }})</p>
                                    <p style="margin-bottom: 5px; font-size: 0.85rem;">Penyesuaian musiman: 12 bulan
                                        sebelumnya</p>
                                    <p style="margin-bottom: 0; font-size: 0.85rem;">Rumus: <code>S<sub>t</sub> =
                                            α×Y<sub>t</sub> + (1-α)×S<sub>t-1</sub></code></p>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <a href="{{ route('admin.prediksi') }}" class="btn btn-outline-primary"
                                    style="font-size: 0.85rem; padding: 5px 10px;">
                                    <i class="fas fa-arrow-left" style="margin-right: 5px;"></i>Kembali
                                </a>
                                <button class="btn btn-primary" onclick="window.print()"
                                    style="font-size: 0.85rem; padding: 5px 10px;">
                                    <i class="fas fa-print" style="margin-right: 5px;"></i>Cetak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 0.375rem;
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .col-xl-3 .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .badge {
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 50px;
        }

        @media (max-width: 1199.98px) {
            .col-xl-3 {
                flex: 0 0 25%;
                max-width: 25%;
            }
        }

        @media (max-width: 991.98px) {
            .col-md-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }
        }

        @media (max-width: 767.98px) {
            .col-sm-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 575.98px) {
            .col-sm-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        @media print {
            .btn {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
@endsection
