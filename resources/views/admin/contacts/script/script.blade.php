<script>
    // ===========================
    // Contact Management Functions (EN / AR)
    // ===========================

    // Set current language: 'en' or 'ar'
    const LANG = document.documentElement.lang || 'en';

    const TEXT = {
        en: {
            errorFetch: 'Error occurred while fetching data',
            connectionError: 'Connection error occurred',
            deleteConfirm: 'Are you sure you want to delete this message?',
            deleted: 'Deleted successfully',
            errorDelete: 'Error occurred while deleting',
            name: 'Name',
            email: 'Email',
            phone: 'Phone',
            date: 'Date',
            time: 'Time',
            user: 'User',
            role: 'Role',
            guest: 'Guest',
            messageContent: 'Message Content',
            close: 'Close',
            deleteMessage: 'Delete Message',
            totalMessages: 'Total Messages',
            userMessages: 'User Messages',
            driverMessages: 'Driver Messages',
            guestMessages: 'Guest Messages',
            thisWeek: 'This Week'
        },
        ar: {
            errorFetch: 'حدث خطأ أثناء جلب البيانات',
            connectionError: 'حدث خطأ في الاتصال',
            deleteConfirm: 'هل أنت متأكد أنك تريد حذف هذه الرسالة؟',
            deleted: 'تم الحذف بنجاح',
            errorDelete: 'حدث خطأ أثناء الحذف',
            name: 'الاسم',
            email: 'البريد الإلكتروني',
            phone: 'الهاتف',
            date: 'التاريخ',
            time: 'الوقت',
            user: 'المستخدم',
            role: 'الدور',
            guest: 'زائر',
            messageContent: 'محتوى الرسالة',
            close: 'إغلاق',
            deleteMessage: 'حذف الرسالة',
            totalMessages: 'إجمالي الرسائل',
            userMessages: 'رسائل المستخدمين',
            driverMessages: 'رسائل السائقين',
            guestMessages: 'رسائل الزوار',
            thisWeek: 'هذا الأسبوع'
        }
    };

    function viewContact(id) {
        fetch(`/dashboard/admin/contacts/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) showContactModal(data.data);
                else showToast('error', TEXT[LANG].errorFetch);
            })
            .catch(() => showToast('error', TEXT[LANG].connectionError));
    }

    function showContactModal(contact) {
        const modalHTML = `
        <div class="modal fade" id="contactModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${TEXT[LANG].messageContent}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>${TEXT[LANG].name}:</strong> ${contact.name}</p>
                                <p><strong>${TEXT[LANG].email}:</strong> ${contact.email}</p>
                                <p><strong>${TEXT[LANG].phone}:</strong> ${contact.phone || TEXT[LANG].guest}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>${TEXT[LANG].date}:</strong> ${new Date(contact.created_at).toLocaleDateString()}</p>
                                <p><strong>${TEXT[LANG].time}:</strong> ${new Date(contact.created_at).toLocaleTimeString()}</p>
                                <p><strong>${TEXT[LANG].user}:</strong> ${contact.user?.name || TEXT[LANG].guest}</p>
                                <p><strong>${TEXT[LANG].role}:</strong> ${contact.role ? (Array.isArray(contact.role) ? contact.role.join(', ') : contact.role) : TEXT[LANG].guest}</p>
                            </div>
                        </div>
                        <div class="mt-3 border p-3 rounded bg-light">
                            ${contact.message}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">${TEXT[LANG].close}</button>
                        <button class="btn btn-danger" onclick="deleteContact(${contact.id})" data-bs-dismiss="modal">
                            ${TEXT[LANG].deleteMessage}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `;

        document.getElementById('contactModal')?.remove();
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        new bootstrap.Modal(document.getElementById('contactModal')).show();
    }

    function getCurrentTableId() {
        const table = document.querySelector('.dataTable');
        return table ? table.id : null;
    }

    function deleteContact(id) {
        if (!confirm(TEXT[LANG].deleteConfirm)) return;

        fetch(`/dashboard/admin/contacts/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message || TEXT[LANG].deleted);
                reloadDataTable();
                loadContactStats();
            } else showToast('error', data.message || TEXT[LANG].errorDelete);
        })
        .catch(() => showToast('error', TEXT[LANG].errorDelete));
    }

    function reloadDataTable() {
        const table = document.querySelector('.dataTable');
        if (table && window.$ && $.fn.DataTable) {
            $('#' + table.id).DataTable().ajax.reload(null, false);
        }
    }

    function loadContactStats() {
        const container = document.getElementById('contact-stats');
        if (!container) return;

        fetch('/dashboard/admin/contacts/stats/overview')
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;
                const stats = data.data;
                container.innerHTML = `
                    <div class="row">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white"><div class="card-body"><h5>${TEXT[LANG].totalMessages}</h5><h3>${stats.total}</h3></div></div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white"><div class="card-body"><h5>${TEXT[LANG].userMessages}</h5><h3>${stats.user_messages}</h3></div></div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white"><div class="card-body"><h5>${TEXT[LANG].driverMessages}</h5><h3>${stats.driver_messages}</h3></div></div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white"><div class="card-body"><h5>${TEXT[LANG].guestMessages}</h5><h3>${stats.guest_messages}</h3></div></div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white"><div class="card-body"><h5>${TEXT[LANG].thisWeek}</h5><h3>${stats.recent}</h3></div></div>
                        </div>
                    </div>
                `;
            });
    }

    function showToast(type, message) {
        if (window.showToast) window.showToast(type, message);
        else alert(message);
    }

    $(document).ready(() => loadContactStats());
</script>
