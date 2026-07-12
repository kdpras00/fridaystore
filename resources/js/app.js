import Swal from 'sweetalert2';
import DataTable from 'datatables.net';
// Side-effect imports — each module registers itself with DataTable internally
import 'datatables.net-responsive';
import 'datatables.net-responsive-dt';
import 'datatables.net-buttons';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';

window.Swal = Swal;
window.swalTheme = {};

// ── DataTables global defaults ───────────────────────────────
DataTable.defaults.language = {
    search:            '',
    searchPlaceholder: 'Cari…',
    lengthMenu:        'Tampilkan _MENU_ baris',
    info:              '_START_–_END_ dari _TOTAL_',
    infoEmpty:         '0 data',
    infoFiltered:      '(dari _MAX_ total)',
    paginate: { first: '«', last: '»', next: '›', previous: '‹' },
    emptyTable:        'Tidak ada data',
    zeroRecords:       'Tidak ada hasil yang cocok',
    loadingRecords:    'Memuat…',
};

// Responsive: last column is the expand/collapse control (official DT pattern)
DataTable.defaults.responsive = {
    details: {
        type:   'column',
        target: -1,
    }
};
// NOTE: Per-table columnDefs override this global, so each view must include
// { className: 'dtr-control', orderable: false, targets: -1 } in its own columnDefs.
// We do NOT set global columnDefs here to avoid merge conflicts.
DataTable.defaults.autoWidth   = false;
DataTable.defaults.pageLength  = 25;
DataTable.defaults.lengthMenu  = [[10, 25, 50, -1], [10, 25, 50, 'Semua']];
DataTable.defaults.dom         = '<"dt-top"f l>rt<"dt-bottom"ip>';

window.DT_EXPORT_BUTTONS = [
    {
        extend:        'csvHtml5',
        text:          'CSV',
        className:     'btn btn-ghost btn-sm',
        exportOptions: { columns: ':not(.dt-no-export)' },
    },
    {
        extend:        'print',
        text:          'Print',
        className:     'btn btn-ghost btn-sm',
        autoPrint:     false,
        exportOptions: { columns: ':not(.dt-no-export)' },
    },
];

// ── Expose and signal readiness ──────────────────────────────
// Vite loads this as type="module" (deferred), so inline @push('scripts')
// blocks may execute before window.DataTable is set.
// Fire a custom event so views can safely wait for it.
window.DataTable = DataTable;
document.dispatchEvent(new CustomEvent('datatables:ready'));

// ── SweetAlert helpers ───────────────────────────────────────
window.confirmDelete = function (formId, itemName = 'data ini') {
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

window.confirmToggle = function (formId, action) {
    Swal.fire({
        title: `${action} user ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
    }).then(r => r.isConfirmed && document.getElementById(formId).submit());
};

window.toast = function (icon, title) {
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

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(m => {
            m.style.display = 'none';
        });
    }
});
