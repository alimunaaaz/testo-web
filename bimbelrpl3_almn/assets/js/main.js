/* ============================================
   FILE: assets/js/main.js
   Fungsi: JavaScript utility untuk aplikasi
   ============================================ */


/* ===== 1. AUTO-DISMISS ALERT =====
   Alert success/error akan hilang otomatis setelah 5 detik
*/
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible');

    alerts.forEach(function(alert) {
        setTimeout(function() {
            // Cek apakah alert masih ada di DOM
            if (alert.parentNode) {
                // Animasi fade out
                alert.classList.add('fade');
                alert.classList.remove('show');

                // Hapus dari DOM setelah animasi selesai (500ms)
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500);
            }
        }, 5000); // 5000ms = 5 detik
    });
});


/* ===== 2. CONFIRM BEFORE ACTION =====
   Tambahkan class="confirm-action" pada tombol/link
   untuk minta konfirmasi sebelum jalankan
*/
document.addEventListener('DOMContentLoaded', function() {
    const confirmButtons = document.querySelectorAll('.confirm-action');

    confirmButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            const message = btn.getAttribute('data-message') || 'Apakah Anda yakin?';
            if (!confirm(message)) {
                e.preventDefault(); // Batalkan action
            }
        });
    });
});


/* ===== 3. ACTIVE NAV LINK =====
   Otomatis tandai nav link yang sesuai dengan URL saat ini
*/
document.addEventListener('DOMContentLoaded', function() {
    const currentUrl = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(function(link) {
        const href = link.getAttribute('href');
        if (href && currentUrl.includes(href.replace('./', '').replace('.php', ''))) {
            // Tambahkan class 'active' pada link yang sesuai
            link.classList.add('active');
        }
    });
});


/* ===== 4. SMOOTH SCROLL =====
   Untuk link yang mengarah ke section dalam satu halaman (#section)
*/
document.addEventListener('DOMContentLoaded', function() {
    const smoothLinks = document.querySelectorAll('a[href^="#"]');

    smoothLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');

            // Jika href hanya "#", jangan jalankan smooth scroll
            if (targetId === '#') return;

            e.preventDefault();
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});


/* ===== 5. BACK TO TOP BUTTON =====
   Tampilkan tombol "Kembali ke atas" saat scroll lebih dari 300px
*/
document.addEventListener('DOMContentLoaded', function() {
    // Buat tombol back-to-top secara dinamis
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '';
    backToTopBtn.className = 'btn btn-primary back-to-top';
    backToTopBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: none;
        z-index: 999;
        box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        justify-content: center;
        align-items: center;
        font-size: 1.2rem;
    `;
    document.body.appendChild(backToTopBtn);

    // Tampilkan/sembunyikan berdasarkan scroll position
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopBtn.style.display = 'flex';
        } else {
            backToTopBtn.style.display = 'none';
        }
    });

    // Klik tombol → scroll ke atas
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});


/* ===== 6. LOADING STATE PADA FORM =====
   Saat form di-submit, tombol berubah menjadi "Loading..."
   untuk mencegah double-click
*/
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');

    forms.forEach(function(form) {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = ' Memproses...';
            }
        });
    });
});