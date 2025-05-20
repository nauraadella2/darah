@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Input Prediksi Permintaan Darah</h2>
    <form action="{{ route('admin.prediksi.store') }}" method="POST">
        @csrf
        <div>
            <label>Tahun:</label>
            <input type="number" name="tahun" required>
        </div>

        @for ($i = 1; $i <= 12; $i++)
            <h4>Bulan {{ $i }}</h4>
            <input type="hidden" name="bulan[]" value="{{ $i }}">
            <div>
                <label>Golongan A:</label>
                <input type="number" name="gol_a[]" min="0" required>
            </div>
            <div>
                <label>Golongan B:</label>
                <input type="number" name="gol_b[]" min="0" required>
            </div>
            <div>
                <label>Golongan AB:</label>
                <input type="number" name="gol_ab[]" min="0" required>
            </div>
            <div>
                <label>Golongan O:</label>
                <input type="number" name="gol_o[]" min="0" required>
            </div>
        @endfor

        <button type="submit">Simpan</button>
    </form>
</div>
@endsection
