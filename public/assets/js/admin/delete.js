document.addEventListener('DOMContentLoaded', function () {
    let deleteUrl, deleteTable;
    const modalEl = document.getElementById('confirmDeleteModal');
    const confirmBtn = modalEl.querySelector('#confirmDeleteBtn');
    const bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });

    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-item');

        if (!btn) return;
        deleteUrl = btn.dataset.url;
        deleteTable = btn.dataset.table;
        bsModal.show();
    });

    confirmBtn.addEventListener('click', function () {
        if (!deleteUrl) return;

        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {

                    const row = $(deleteTable).find(`button[data-id="${data.id}"]`).closest('tr');
                    row.remove();
                    showToast('success', data.message);
                } else {

                    showToast('error', data.message || 'Delete failed');
                }
            })
            .catch(error => {

                console.error('Error during fetch:', error);
                showToast('error', 'Delete failed');
            })
            .finally(() => {

                bsModal.hide();
                deleteUrl = deleteTable = null;
                document.body.classList.remove('modal-open');
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            });
    });
});
