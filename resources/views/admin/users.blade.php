@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
  <h2 class="text-2xl font-bold mb-6 text-red-600">👥 Manajemen User</h2>

  <!-- Tombol Tambah -->
  <div class="mb-4">
    <button onclick="openModal('modalTambahUser')">+ Tambah User</button>
  </div>

  <!-- Tabel User -->
  <div class="bg-white shadow rounded-lg p-6">
    <div class="overflow-x-auto">
      <table>
        <thead>
          <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Ahmad Hadi</td>
            <td>hadi@example.com</td>
            <td>Admin</td>
            <td>
              <button onclick="openModal('modalEditUser')">Edit</button>
              <button class="bg-red-500 hover:bg-red-600 text-white">Hapus</button>
            </td>
          </tr>
          <tr>
            <td>Rina Marlina</td>
            <td>rina@example.com</td>
            <td>Petugas</td>
            <td>
              <button onclick="openModal('modalEditUser')">Edit</button>
              <button class="bg-red-500 hover:bg-red-600 text-white">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah User -->
<div id="modalTambahUser" class="modal hidden">
  <div class="modal-content">
    <h3 class="text-lg font-semibold mb-4">Tambah User</h3>
    <form>
      <label>Nama:</label>
      <input type="text" required>

      <label>Email:</label>
      <input type="email" required>

      <label>Password:</label>
      <input type="password" required>

      <label>Role:</label>
      <select required>
        <option value="admin">Admin</option>
        <option value="petugas">Petugas</option>
      </select>

      <div class="modal-buttons">
        <button type="submit">Simpan</button>
        <button type="button" onclick="closeModal('modalTambahUser')" class="bg-gray-500 hover:bg-gray-600">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit User -->
<div id="modalEditUser" class="modal hidden">
  <div class="modal-content">
    <h3 class="text-lg font-semibold mb-4">Edit User</h3>
    <form>
      <label>Nama:</label>
      <input type="text" value="Ahmad Hadi">

      <label>Email:</label>
      <input type="email" value="hadi@example.com">

      <label>Role:</label>
      <select>
        <option value="admin" selected>Admin</option>
        <option value="petugas">Petugas</option>
      </select>

      <div class="modal-buttons">
        <button type="submit">Update</button>
        <button type="button" onclick="closeModal('modalEditUser')" class="bg-gray-500 hover:bg-gray-600">Batal</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
  }

  function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
  }
</script>
@endpush
