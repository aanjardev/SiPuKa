/**
 * Alert Helper System untuk SiDiKa Admin
 * Menggunakan SweetAlert2 untuk konfirmasi dan notifikasi
 */

function confirmDelete(message = 'Data ini akan dihapus secara permanen!', title = 'Yakin ingin menghapus?', confirmText = 'Lanjutkan', cancelText = 'Batal') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmText || 'Lanjutkan',
        cancelButtonText: cancelText || 'Batal'
    });
}

function confirmAction(message, title = 'Konfirmasi', confirmText = 'Ya', cancelText = 'Batal') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText
    });
}

function confirmRegenerateToken(message = 'Generate ulang token aktivasi? Token lama akan tidak berlaku.') {
    return Swal.fire({
        title: 'Konfirmasi Generate Token',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                <p style="margin-bottom: 15px;">${message}</p>
                <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 12px; margin-top: 15px;">
                    <p style="margin: 0; font-size: 14px; color: #856404;">
                        <strong>📌 Informasi:</strong> Token aktivasi berlaku selama <strong>3 hari (72 jam)</strong>. 
                        Karyawan harus segera melakukan aktivasi sebelum token kadaluarsa.
                    </p>
                </div>
            </div>
        `
    });
}

function showSuccess(message, title = 'Berhasil!') {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

function showError(message, title = 'Terjadi Kesalahan') {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonText: 'OK'
    });
}

function showWarning(message, title = 'Perhatian') {
    return Swal.fire({
        icon: 'warning',
        title: title,
        text: message,
        confirmButtonText: 'OK'
    });
}

function showInfo(message, title = 'Informasi') {
    return Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

document.addEventListener('DOMContentLoaded', () => {

    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const form = this.closest('form');
            if (!form) return;

            const message = this.dataset.message || 'Data ini akan dihapus secara permanen!';
            const title = this.dataset.title || 'Yakin ingin menghapus?';

            confirmDelete(message, title).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Handle form dengan onsubmit confirm (legacy support)
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
        const originalOnsubmit = form.onsubmit;
        form.onsubmit = null;
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Extract message from onsubmit attribute if exists
            const onsubmitAttr = form.getAttribute('onsubmit');
            let message = 'Data ini akan dihapus secara permanen!';
            let title = 'Yakin ingin menghapus?';
            
            if (onsubmitAttr) {
                const match = onsubmitAttr.match(/confirm\(['"]([^'"]+)['"]\)/);
                if (match) {
                    message = match[1];
                }
            }
            
            confirmDelete(message, title).then((result) => {
                if (result.isConfirmed) {
                    form.removeAttribute('onsubmit');
                    form.submit();
                }
            });
        });
    });

    const flashSuccess = document.querySelector('meta[name="flash-success"]');
    if (flashSuccess && flashSuccess.content) {
        showSuccess(flashSuccess.content);
    }
});

window.confirmDelete = confirmDelete;
window.confirmAction = confirmAction;
window.confirmRegenerateToken = confirmRegenerateToken;
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;
