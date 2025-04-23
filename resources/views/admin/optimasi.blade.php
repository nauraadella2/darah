@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
  <h2 class="text-2xl font-bold mb-4">🧪 Optimasi Nilai Alpha</h2>

  <div class="bg-white rounded-lg shadow p-6">
    <p class="mb-4">Sistem akan menghitung nilai error (MAPE) untuk setiap nilai alpha dari 0.1 hingga 0.9.</p>

    <form action="{{ route('admin.optimasi') }}" method="POST" class="mb-6">
      @csrf
      <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
        Mulai Optimasi Alpha
      </button>
    </form>

    {{-- @isset($hasilOptimasi) --}}
    <div class="mb-6">
      <h3 class="text-lg font-semibold mb-2">Hasil Optimasi:</h3>
      <table class="w-full text-sm text-left text-gray-700 border">
        <thead class="bg-red-100">
          <tr>
            <th class="px-4 py-2 border">Alpha</th>
            <th class="px-4 py-2 border">MAPE (%)</th>
          </tr>
        </thead>
        <tbody>
          {{-- @foreach($hasilOptimasi as $alpha => $mape) --}}
          <tr class="bg-green-100 font-semibold"> 0,1
            <td class="px-4 py-2 border text-center">Alpha</td>
            <td class="px-4 py-2 border text-center">1</td>
          </tr>
          {{-- @endforeach --}}
        </tbody>
      </table>
    </div>

    <div class="text-green-700 font-bold">
     Alpha terbaik 0,1 {{-- Alpha terbaik: <span class="text-red-600">{{ $alphaTerbaik }}</span> (MAPE: {{ number_format($hasilOptimasi[$alphaTerbaik], 2) }}%) --}}
    </div>
    {{-- @endisset --}}
  </div>
</div>
@endsection
