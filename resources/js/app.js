import Swal from 'sweetalert2';

window.Swal = Swal;

// Shared SweetAlert theme (empty to use standard SweetAlert2 defaults)
const swalTheme = {};

window.swalTheme = swalTheme;

window.confirmDelete = function(formId, itemName = 'data ini') {
    Swal.fire({
        title: 'Hapus data?',
        text: `"${itemName}" tidak bisa dikembalikan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
    }).then(r => r.isConfirmed && document.getElementById(formId).submit());
};

window.confirmToggle = function(formId, action) {
    Swal.fire({
        title: `${action} user ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
    }).then(r => r.isConfirmed && document.getElementById(formId).submit());
};

window.toast = function(icon, title) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon,
        title,
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
    });
};

// Global Escape Key to Close Modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.style.display = 'none';
        });
    }
});
