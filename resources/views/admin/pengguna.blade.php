@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <h2><i class="bx bx-user"></i> Halaman Pengguna</h2>
    </div>

    <div class="user-summary-row">
        <div class="user-card">
            <div class="icon-box bg-danger" style="background-color: #d32f2f"><i class="bx bx-user-pin"></i></div>
            <div><p class="label">Total Admin</p><h3>{{ $adminCount }} Pengguna</h3></div>
        </div>
        <div class="user-card">
            <div class="icon-box bg-danger" style="background-color: #d32f2f"><i class="bx bx-user-check"></i></div>
            <div><p class="label">Total Petugas</p><h3>{{ $petugasCount }} Pengguna</h3></div>
        </div>
    </div>
    <div class="chart-card mt-4">
        <div class="card-header"><h3><i class="bx bx-table"></i> Daftar Pengguna</h3><button onclick="showAddModal()" class="btn-tambah"><i class="bx bx-plus"></i> Tambah Pengguna</button></div>
        
        <div class="card-body">
            <table class="custom-table" id="userTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $i => $user)
                    <tr data-id="{{ $user->id }}">
                        <td>{{ $i + 1 }}</td>
                        <td class="td-nama">{{ $user->name }}</td>
                        <td class="td-email">{{ $user->email }}</td>
                        <td class="td-role">
                            <span class="badge-role {{ $user->role == 'admin' ? 'admin' : 'petugas' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <button class="btn-aksi edit-btn" onclick="showEditModal({{ $user->id }})"><i class="bx bx-edit"></i></button>
                            <button class="btn-aksi delete-btn" onclick="deleteUser({{ $user->id }})"><i class="bx bx-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showAddModal() {
        Swal.fire({
            title: 'Tambah Pengguna',
            html: `
                <input type="text" id="swal-nama" class="swal2-input" placeholder="Nama">
                <input type="email" id="swal-email" class="swal2-input" placeholder="Email">
                <input type="password" id="swal-password" class="swal2-input" placeholder="Password">
                <select id="swal-role" class="swal2-select">
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas</option>
                </select>
            `,
            confirmButtonText: 'Tambah',
            focusConfirm: false,
            preConfirm: () => {
                const name = document.getElementById('swal-nama').value;
                const email = document.getElementById('swal-email').value;
                const password = document.getElementById('swal-password').value;
                const role = document.getElementById('swal-role').value;

                if (!name || !email || !password || !role) {
                    Swal.showValidationMessage('Semua kolom harus diisi');
                    return false;
                }

                return fetch(`{{ route('admin.pengguna.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ name, email, password, role })
                })
                .then(res => {
                    if (!res.ok) throw new Error('Gagal');
                    return res.json();
                }).then(data => {
                    Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
                }).catch(err => Swal.fire('Gagal', 'Periksa input!', 'error'));
            }
        });
    }

    function showEditModal(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        const nama = row.querySelector('.td-nama').innerText;
        const email = row.querySelector('.td-email').innerText;
        const role = row.querySelector('.td-role span').innerText.toLowerCase();

        Swal.fire({
            title: 'Edit Pengguna',
            html: `
                <input type="text" id="swal-nama" class="swal2-input" value="${nama}">
                <input type="email" id="swal-email" class="swal2-input" value="${email}">
                <input type="password" id="swal-password" class="swal2-input" placeholder="Kosongkan jika tidak diubah">
                <select id="swal-role" class="swal2-select">
                    <option value="admin" ${role === 'admin' ? 'selected' : ''}>Admin</option>
                    <option value="petugas" ${role === 'petugas' ? 'selected' : ''}>Petugas</option>
                </select>
            `,
            confirmButtonText: 'Simpan',
            preConfirm: () => {
                return fetch(`/admin/pengguna/update/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: document.getElementById('swal-nama').value,
                        email: document.getElementById('swal-email').value,
                        role: document.getElementById('swal-role').value,
                        password: document.getElementById('swal-password').value // bisa kosong
                    })
                })
                .then(res => {
                    if (!res.ok) throw new Error('Gagal');
                    return res.json();
                }).then(data => {
                    Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
                }).catch(err => Swal.fire('Gagal', 'Periksa input!', 'error'));
            }
        });
    }

    function deleteUser(id) {
        Swal.fire({
            title: 'Hapus Pengguna?',
            text: 'Data tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/pengguna/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => Swal.fire('Terhapus!', data.message, 'success').then(() => location.reload()))
                .catch(() => Swal.fire('Gagal', 'Terjadi kesalahan!', 'error'));
            }
        });
    }
</script>

<style>
    .user-summary-row {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .user-card {
        flex: 1;
        min-width: 220px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 20px;
        display: flex;
        gap: 16px;
        align-items: center;
    }

    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .label {
        margin: 0;
        font-size: 0.9rem;
        color: #6b7280;
    }

    .user-card h3 {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 4px 0 0;
        color: #111827;
    }

    .btn-tambah {
        background-color: #d32f2f;
        color: #eee;
        padding: 10px 16px;
        font-size: 0.95rem;
        font-weight: 500;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-tambah:hover {
        background-color: #dc2626;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
        background-color: 
    }

    .custom-table th,
    .custom-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f3f4f6;
        text-align: left;
    }

    .custom-table thead th {
        background-color: #ffe5e5;
        color: #374151;
        font-weight: 600;
    }

    .custom-table tbody tr:hover {
        background-color: #f1f5f9;
    }

    .badge-role {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        color: white;
        display: inline-block;
    }

    .badge-role.admin {
        background-color: #dc2626;
    }

    .badge-role.petugas {
        background-color: #858c99;
    }

    .btn-aksi {
        border: none;
        background: none;
        padding: 6px 8px;
        margin-right: 4px;
        cursor: pointer;
        font-size: 1rem;
    }

    .edit-btn {
        color: #f59e0b;
    }

    .delete-btn {
        color: #ef4444;
    }

    .btn-aksi:hover {
        transform: scale(1.1);
    }
</style>
@endsection
