// assets/js/theme-engine.js

const toggleTheme = () => {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcons(newTheme);
};

const updateThemeIcons = (theme) => {
    const icons = document.querySelectorAll('.theme-icon');
    icons.forEach(icon => {
        if (theme === 'dark') {
            icon.className = 'fas fa-sun theme-icon';
        } else {
            icon.className = 'fas fa-moon theme-icon';
        }
    });
};

// Eksekusi langsung saat file di-load agar tidak ada 'flash' warna putih
(function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Tunggu sampai HTML selesai di-render baru ganti iconnya
    window.addEventListener('DOMContentLoaded', () => {
        updateThemeIcons(savedTheme);
    });
})();