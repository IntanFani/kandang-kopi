<div class="modal-overlay" id="modalLogout">
    <div class="modal-content logout-card">
        <button class="close-x" id="closeLogoutX">&times;</button>
        <div class="logout-icon">
            <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#A68B6D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/>
            </svg>
        </div>
        <h3>ANDA YAKIN INGIN KELUAR ?</h3>
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary btn-logout-confirm">KELUAR</button>
        </form>
    </div>
</div>