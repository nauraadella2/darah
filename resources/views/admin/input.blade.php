@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Input Data Permintaan Darah Tahunan</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.store') }}" method="POST">
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
                    <td><input type="number" name="gol_a[]" value="{{ random_int(40, 80) }}" class="form-control" min="0" required></td>
                    <td><input type="number" name="gol_b[]" value="{{ random_int(35, 70) }}" class="form-control" min="0" required></td>
                    <td><input type="number" name="gol_ab[]" value="{{ random_int(10, 30) }}" class="form-control" min="0" required></td>
                    <td><input type="number" name="gol_o[]" value="{{ random_int(90, 150) }}" class="form-control" min="0" required></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary mt-3">Simpan Data</button>
    </form>
</div>
@endsection