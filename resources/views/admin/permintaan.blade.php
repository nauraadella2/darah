@extends('layouts.app')

@section('content')
  <div class="container-permintaan">
    <div class="header-permintaan">
      <h1>Data Permintaan Darah</h1>

      <div class="filter-group">
        <select name="bulan" class="filter-select">
          <option value="">Semua Bulan</option>
          <option value="1">Januari</option>
          <option value="2">Februari</option>
          <!-- dst sampai Desember -->
        </select>
        <select name="golongan" class="filter-select">
          <option value="">Semua Golongan</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="AB">AB</option>
          <option value="O">O</option>
        </select>
        <button class="filter-btn">Filter</button>
      </div>

      <div class="action-group">
        <button class="btn-primary" onclick="openModal()">+ Input Satu Data</button>
        <a href="{{ route('admin.input') }}" class="btn-secondary">📑 Input Banyak Data</a>
      </div>
    </div>

    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Golongan Darah</th>
            <th>Jumlah (Kantong)</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loop data dari controller -->
          {{-- @foreach ($dataPermintaan as $item) --}}
            <tr>
              <td>Januari 2020</td>
              <td>O</td>
              <td>10</td>
              <td>
                <button class="btn-edit">Edit</button>
                <button class="btn-delete">Hapus</button>
              </td>
            </tr>
          {{-- @endforeach --}}
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

@push('scripts')
<script>
  function openModal() {
    document.getElementById('modalInput').classList.remove('hidden');
  }

  function closeModal() {
    document.getElementById('modalInput').classList.add('hidden');
  }
</script>
@endpush
