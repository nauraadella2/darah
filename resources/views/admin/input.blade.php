@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
  <h2 class="text-2xl font-bold mb-6 text-red-600">📝 Input Banyak Data Permintaan Darah</h2>

  <form id="formData">
    <div class="overflow-x-auto bg-white shadow rounded-lg p-4">
      <table id="inputTable">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Golongan Darah</th>
            <th>Jumlah Permintaan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="date" name="tanggal[]"></td>
            <td>
              <select name="golongan[]">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="AB">AB</option>
                <option value="O">O</option>
              </select>
            </td>
            <td><input type="number" name="jumlah[]" min="1"></td>
            <td><button type="button" onclick="hapusBaris(this)" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button></td>
          </tr>
        </tbody>
      </table>
      <button type="button" onclick="tambahBaris()" class="mt-4 bg-green-500 text-white px-3 py-1 rounded">+ Tambah Baris</button>
    </div>

    <div class="mt-6">
      <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan Data</button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  function tambahBaris() {
    const table = document.getElementById('inputTable').getElementsByTagName('tbody')[0];
    const row = table.insertRow();

    row.innerHTML = `
      <td><input type="date" name="tanggal[]"></td>
      <td>
        <select name="golongan[]">
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="AB">AB</option>
          <option value="O">O</option>
        </select>
      </td>
      <td><input type="number" name="jumlah[]" min="1"></td>
      <td><button type="button" onclick="hapusBaris(this)" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button></td>
    `;
  }

  function hapusBaris(button) {
    const row = button.closest('tr');
    row.remove();
  }

  document.getElementById('formData').addEventListener('submit', function (e) {
    e.preventDefault();
    alert('Data berhasil disimpan (simulasi).');
  });
</script>
@endpush
