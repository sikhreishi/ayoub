<style>
/* Chat-like Message Layout */
.message-wrapper {
    width: 100%;
    margin-bottom: 1rem;
}

.message-left {
    display: flex;
    justify-content: flex-start;
}

.message-right {
    display: flex;
    justify-content: flex-end;
}

/* Reply Items Enhancement */
.reply-item {
    max-width: 70%;
    padding: 1rem;
    border-radius: 15px;
    margin-bottom: 0.5rem;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
    position: relative;
}

/* Current User (Right Side) */
.current-user-reply {
    background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
    color: white;
    border-top-right-radius: 5px;
    margin-left: auto;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.current-user-reply strong {
    color: white;
}

.current-user-content {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.75rem;
    border-radius: 8px;
    margin-top: 0.5rem;
    color: white;
    border: none;
}

.current-user-avatar {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 2px 10px rgba(16, 185, 129, 0.4);
}

/* Other Users (Left Side) */
.other-user-reply {
    background-color: var(--bg-tertiary);
    border-top-left-radius: 5px;
    margin-right: auto;
}

.other-user-content {
    background-color: var(--bg-secondary);
    padding: 0.75rem;
    border-radius: 8px;
    border-left: 3px solid var(--accent-primary);
    margin-top: 0.5rem;
    color: var(--text-primary);
}

/* Internal Reply Styling */
.internal-reply {
    position: relative;
}

.internal-reply::before {
    content: "🔒";
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 1rem;
    background: var(--warning);
    border-radius: 50%;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.internal-reply.current-user-reply {
    border: 2px solid var(--warning);
}

.internal-reply.other-user-reply {
    background: linear-gradient(135deg, #451a03 0%, #78350f 100%);
    border-left: 4px solid var(--warning);
}

/* Avatar Circle Enhancement */
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1rem;
    box-shadow: 0 2px 10px rgba(59, 130, 246, 0.4);
    transition: transform 0.3s ease;
    border: 2px solid var(--border-color);
    flex-shrink: 0;
}

/* Message Header */
.message-header {
    margin-bottom: 0.25rem;
}

/* Dark Theme Variables */
:root {
    --bg-primary: #0f1419;
    --bg-secondary: #1a1f2e;
    --bg-tertiary: #242b3d;
    --bg-card: #1e2532;
    --bg-card-hover: #252c3a;
    --text-primary: #e2e8f0;
    --text-secondary: #94a3b8;
    --text-muted: #64748b;
    --border-color: #334155;
    --accent-primary: #3b82f6;
    --accent-secondary: #8b5cf6;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #06b6d4;
}

body {
    background-color: var(--bg-primary);
    color: var(--text-primary);
}

/* Global Improvements */
.container-fluid {
    background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
    min-height: 100vh;
    padding: 2rem;
}

/* Enhanced Card Styling */
.card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 15px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
    margin-bottom: 2rem;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
    border-color: var(--accent-primary);
    background-color: var(--bg-card-hover);
}

.card-header {
    background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
    color: white;
    border-bottom: 1px solid var(--border-color);
    padding: 1.25rem 1.5rem;
    font-weight: 600;
}

.card-header h5, .card-header h6 {
    color: white;
    margin: 0;
    font-weight: 600;
}

.card-body {
    padding: 1.5rem;
    background-color: var(--bg-card);
    color: var(--text-primary);
}

.card-footer {
    background-color: var(--bg-tertiary);
    border-top: 1px solid var(--border-color);
    padding: 1.5rem;
}

/* Status and Priority Badges */
.badge {
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 25px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid transparent;
}

.badge-success {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    color: white;
    box-shadow: 0 2px 10px rgba(16, 185, 129, 0.3);
}

.badge-warning {
    background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
    color: white;
    box-shadow: 0 2px 10px rgba(245, 158, 11, 0.3);
}

.badge-info {
    background: linear-gradient(135deg, var(--info) 0%, #0891b2 100%);
    color: white;
    box-shadow: 0 2px 10px rgba(6, 182, 212, 0.3);
}

.badge-primary {
    background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
    color: white;
    box-shadow: 0 2px 10px rgba(59, 130, 246, 0.3);
}

.badge-danger {
    background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
    color: white;
    box-shadow: 0 2px 10px rgba(239, 68, 68, 0.3);
}

.badge-secondary {
    background: linear-gradient(135deg, #475569 0%, #334155 100%);
    color: white;
    box-shadow: 0 2px 10px rgba(71, 85, 105, 0.3);
}

/* Form Enhancements */
.form-control {
    background-color: var(--bg-tertiary);
    border: 2px solid var(--border-color);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    color: var(--text-primary);
    transition: all 0.3s ease;
}

.form-control:focus {
    background-color: var(--bg-card);
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    transform: translateY(-1px);
    color: var(--text-primary);
}

.form-control::placeholder {
    color: var(--text-muted);
}

select.form-control {
    cursor: pointer;
}

select.form-control option {
    background-color: var(--bg-tertiary);
    color: var(--text-primary);
}

.form-group label {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
}

/* Button Enhancements */
.btn {
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
    background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
}

.btn-secondary {
    background: linear-gradient(135deg, #475569 0%, #334155 100%);
    box-shadow: 0 4px 15px rgba(71, 85, 105, 0.4);
    color: white;
}

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(71, 85, 105, 0.6);
    background: linear-gradient(135deg, #3f4654 0%, #2d3748 100%);
}

/* Description Box Enhancement */
.bg-light {
    background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%) !important;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.5rem;
    color: var(--text-primary);
}

/* Text Colors */
strong {
    color: var(--text-primary);
}

.text-muted {
    color: var(--text-muted) !important;
}

.text-gray-800 {
    color: var(--text-primary) !important;
}

.text-light {
    color: rgba(255, 255, 255, 0.8) !important;
}

/* Links */
a {
    color: var(--accent-primary);
    text-decoration: none;
}

a:hover {
    color: #60a5fa;
    text-decoration: none;
}

/* Breadcrumb */
.breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
}

.breadcrumb-item a {
    color: var(--accent-primary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb-item a:hover {
    color: #60a5fa;
}

.breadcrumb-item.active {
    color: var(--text-secondary);
}

/* HR Styling */
hr {
    border: none;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--border-color), transparent);
    margin: 1.5rem 0;
}

/* Custom Scrollbar for Replies Container */
#repliesContainer {
    max-height: 600px;
    overflow-y: auto;
    padding: 1rem;
    scrollbar-width: thin;
    scrollbar-color: var(--accent-primary) var(--bg-tertiary);
}

#repliesContainer::-webkit-scrollbar {
    width: 8px;
}

#repliesContainer::-webkit-scrollbar-track {
    background: var(--bg-tertiary);
    border-radius: 10px;
}

#repliesContainer::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
    border-radius: 10px;
}

#repliesContainer::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
}

/* Custom Checkbox Styling */
.form-check-input {
    background-color: var(--bg-tertiary);
    border-color: var(--border-color);
}

.form-check-input:checked {
    background-color: var(--accent-primary);
    border-color: var(--accent-primary);
}

.form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

.form-check-label {
    font-weight: 500;
    color: var(--text-secondary);
}

/* Loading State */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Responsive Improvements */
@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }

    .reply-item {
        max-width: 85%;
        padding: 0.75rem;
    }

    .avatar-circle {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }

    .btn {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
}
</style>
