<script>
    // Contact Management Functions

// View specific contact details
function viewContact(id) {
    fetch(`/dashboard/admin/contacts/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showContactModal(data.data);
            } else {
                showToast('error', 'Error occurred while fetching data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Connection error occurred');
        });
}

// Display contact in modal
function showContactModal(contact) {
    const modalHTML = `
        <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contactModalLabel">Contact Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> ${contact.name}</p>
                                <p><strong>Email:</strong> ${contact.email}</p>
                                <p><strong>Phone:</strong> ${contact.phone || 'Not specified'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Date:</strong> ${new Date(contact.created_at).toLocaleDateString('en-US')}</p>
                                <p><strong>Time:</strong> ${new Date(contact.created_at).toLocaleTimeString('en-US')}</p>
                                <p><strong>User:</strong> ${contact.user ? contact.user.name : 'Guest'}</p>
                                <p><strong>Role:</strong> ${contact.role ? (Array.isArray(contact.role) ? contact.role.join('، ') : contact.role) : 'زائر'}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6>Message Content:</h6>
                                <div class="border p-3 bg-light rounded">
                                    ${contact.message}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger" onclick="deleteContact(${contact.id})" data-bs-dismiss="modal">
                            Delete Message
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if found
    const existingModal = document.getElementById('contactModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Add new modal
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('contactModal'));
    modal.show();
}

// Helper: get current table id from blade variable or fallback
function getCurrentTableId() {
    // Try to find the first .dataTable in the page
    var table = document.querySelector('.dataTable');
    return table ? table.id : null;
}

// Delete contact via AJAX and reload DataTable
function deleteContact(id) {
    if (confirm('Are you sure you want to delete this message?')) {
        fetch(`/dashboard/admin/contacts/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message || 'Deleted successfully');
                reloadDataTable();
                loadContactStats();
            } else {
                showToast('error', data.message || 'Error occurred while deleting');
            }
        })
        .catch(error => {
            showToast('error', 'Error occurred while deleting');
        });
    }
}

// Reload DataTable (yajra)
function reloadDataTable() {
    var tableId = getCurrentTableId();
    if (tableId && window.$ && $.fn.DataTable) {
        $('#' + tableId).DataTable().ajax.reload(null, false); // false = don't reset pagination
    }
}

// Load statistics (if stats container exists)
function loadContactStats() {
    if (!document.getElementById('contact-stats')) return;
    fetch('/dashboard/admin/contacts/stats/overview')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatsDisplay(data.data);
            }
        });
}

// Update statistics display
function updateStatsDisplay(stats) {
    const statsContainer = document.getElementById('contact-stats');
    if (statsContainer) {
        statsContainer.innerHTML = `
            <div class="row">
                <div class="col-md-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Messages</h5>
                            <h3>${stats.total}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">User Messages</h5>
                            <h3>${stats.user_messages}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Driver Messages</h5>
                            <h3>${stats.driver_messages}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Guest Messages</h5>
                            <h3>${stats.guest_messages}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <h5 class="card-title">This Week</h5>
                            <h3>${stats.recent}</h3>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

// Show toast (uses your existing utils.js)
function showToast(type, message) {
    if (window.showToast) {
        window.showToast(type, message);
    } else {
        alert(message);
    }
}

// On page load, load stats
$(document).ready(function() {
    loadContactStats();
});
</script>
