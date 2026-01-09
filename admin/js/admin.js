// Admin Panel JavaScript

// Mobile menu toggle
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.querySelector('.sidebar');

if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768) {
        if (sidebar && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    }
});

// Auto-hide alerts after 5 seconds
const alerts = document.querySelectorAll('.alert');
alerts.forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
});

// Confirm before delete
const deleteButtons = document.querySelectorAll('[data-confirm]');
deleteButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        if (!confirm(button.dataset.confirm)) {
            e.preventDefault();
        }
    });
});
