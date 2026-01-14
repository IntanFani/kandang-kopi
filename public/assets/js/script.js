document.addEventListener('DOMContentLoaded', function () {
    // === FUNGSI PENGAMAN GLOBAL ===
    window.handleEvent = (id, event, callback) => {
        const el = document.getElementById(id);
        if (el) el.addEventListener(event, callback);
    };

    // Logika Logout (Karena menu Keluar ada di sidebar setiap halaman)
    const logoutButtons = document.querySelectorAll('.bi-box-arrow-right'); 
    const modalLogout = document.getElementById('modalLogout');
    if (logoutButtons.length > 0 && modalLogout) {
        logoutButtons.forEach(btn => {
            btn.parentElement.addEventListener('click', (e) => {
                e.preventDefault();
                modalLogout.style.display = 'flex';
            });
        });
        handleEvent('closeLogoutX', 'click', () => modalLogout.style.display = 'none');
    }

    // Logika Tutup Modal Jika Klik Luar (Gunakan class generic)
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-overlay')) {
            e.target.style.display = 'none';
        }
    });
});