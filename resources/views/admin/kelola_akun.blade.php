@extends('layouts.main')

@section('container')
<link rel="stylesheet" href="{{ asset('assets/css/kelola_akun.css') }}" />

<div class="main-content">
    <button class="btn-add" onclick="document.getElementById('modalAkun').style.display='flex'">
        <i class="bi bi-plus"></i> Tambah Akun
    </button>

    <div class="content">
        <div class="table-kontainer">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kasirs as $k)
                    <tr>
                        <td>{{ $k->name }}</td>
                        <td>{{ $k->username }}</td>
                        <td>********</td>
                        <td class="actions">
                            {{-- Tombol Edit --}}
                            <button class="btn-edit" 
                                    style="background: none; border: none; cursor: pointer;" 
                                    onclick="openEditModal('{{ $k->id }}', '{{ $k->name }}', '{{ $k->username }}')">
                                <i class="bi bi-pencil-square" style="color: orange; font-size: 1.2rem;"></i>
                            </button>

                            {{-- Tombol Hapus --}}
                            <form action="{{ route('user.destroy', $k->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" style="background: none; border: none; cursor: pointer;" onclick="return confirm('Hapus kasir ini?')">
                                    <i class="bi bi-trash" style="color: red; font-size: 1.2rem;"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal-overlay" id="modalAkun" style="display: none;">
    <div class="modal-content">
        <h2>Tambah Akun Baru</h2>
        <form action="{{ route('user.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('modalAkun').style.display='none'" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal-overlay" id="modalEditAkun" style="display: none;">
    <div class="modal-content">
        <h2>Edit Akun Kasir</h2>
        <form id="formEditAkun" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="edit_username" required>
            </div>

            <div class="form-group">
                <label>Password Baru (Kosongkan jika tidak ganti)</label>
                <input type="password" name="password" placeholder="Minimal 5 karakter">
            </div>

            <div class="modal-footer">
                <button type="button"
                        onclick="document.getElementById('modalEditAkun').style.display='none'"
                        class="btn-secondary">
                    Batal
                </button>
                <button type="submit" class="btn-primary">
                    Update Data
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    function openEditModal(id, name, username) {
        const form = document.getElementById('formEditAkun');

        // SESUAI ROUTE
        form.action = "{{ route('user.update', ':id') }}".replace(':id', id);

        document.getElementById('edit_name').value = name;
        document.getElementById('edit_username').value = username;

        // Kosongkan password setiap modal dibuka
        const passwordInput = form.querySelector('input[name="password"]');
        if (passwordInput) passwordInput.value = '';

        document.getElementById('modalEditAkun').style.display = 'flex';
    }

    // PENUTUP MODAL 
    window.onclick = function(event) {
        const modalTambah = document.getElementById('modalAkun');
        const modalEdit = document.getElementById('modalEditAkun');

        if (modalTambah && event.target === modalTambah) {
            modalTambah.style.display = 'none';
        }

        if (modalEdit && event.target === modalEdit) {
            modalEdit.style.display = 'none';
        }
    }
</script>


@if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif

@endsection