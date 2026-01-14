<div class="topbar">
    <div class="top-left">
        <h1>Hallo, {{ Auth::user()->name }} 👋</h1>
        <p>Halo! Ini update keuangan harianmu</p>
    </div>
    <div class="top-right">
        <div class="user-profile-pill">
            <div class="user-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <div class="user-details">
                {{-- Mengambil kolom 'name' dari tabel users --}}
                <span class="user-fullname">{{ Auth::user()->name }}</span>
                {{-- Mengambil kolom 'username' atau bisa diganti email --}}
                <span class="user-handle">{{ Auth::user()->username }}</span>
            </div>
        </div>
    </div>
</div>