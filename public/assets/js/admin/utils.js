window.showToast = function (type, message) {
    // Ensure there's a container for the toasts
    const toastContainer = document.querySelector('.toast-container') || (() => {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
        return container;
    })();

    // Determine the toast color based on the type
    const toastTypeClass = type === 'success' ? 'success' : type === 'error' ? 'danger' : 'secondary';

    // Create and insert the toast HTML
    const toastHTML = `
        <div class="toast align-items-center bg-${toastTypeClass} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>`;

    // Insert the toast into the container and show it
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    const newToast = toastContainer.lastElementChild;
    const bsToast = new bootstrap.Toast(newToast);
    bsToast.show();

    // Remove the toast after 5 seconds
    setTimeout(() => {
        if (newToast) newToast.remove();
    }, 5000);
};

