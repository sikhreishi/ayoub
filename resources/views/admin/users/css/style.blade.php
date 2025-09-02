    <style>
        :root {
            --card-gradient: linear-gradient(127.09deg, rgba(6, 11, 40, 0.94) 19.41%, rgba(10, 14, 35, 0.49) 76.65%);
            --card-border-color: rgba(255, 255, 255, 0.15);
            --card-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            --highlight-color: #007bff;
            --text-primary: #e6ecf0;
            --text-secondary: #a7acb1;
            --text-muted: #878d96;
            --badge-verified: linear-gradient(310deg, #17ad37, #98ec2d);
            --badge-unverified: linear-gradient(310deg, #f5b60a, #ff9800);
            --btn-gradient: linear-gradient(310deg, #7928ca, #ff0080);
            --hover-transition: all 0.3s ease;
            --permission-group-bg: rgba(10, 14, 35, 0.7);
            --permission-item-bg: rgba(15, 21, 53, 0.5);
            --permission-item-hover: rgba(0, 123, 255, 0.1);
        }

        /* Card styling */
        .card {
            background-image: var(--card-gradient);
            border: 1px solid var(--card-border-color);
            box-shadow: var(--card-shadow);
            transition: var(--hover-transition);
        }

        .card-header {
            background-color: rgba(6, 11, 40, 0.7);
            border-bottom: 1px solid var(--card-border-color);
        }

        .card-title {
            color: var(--text-primary);
            font-weight: 600;
        }

        /* User info card */
        .user-info-card .avatar-img {
            border: 3px solid var(--highlight-color);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .avatar-placeholder {
            background: linear-gradient(310deg, #7928ca, #ff0080);
            border: 3px solid var(--card-border-color);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .user-info-text {
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .form-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        /* Status badges */
        .status-badge {
            padding: 0.35rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 6px;
        }

        .status-badge.verified {
            background-image: var(--badge-verified);
            color: #fff;
        }

        .status-badge.unverified {
            background-image: var(--badge-unverified);
            color: #fff;
        }

        /* Role cards */
        .role-card {
            transition: var(--hover-transition);
            cursor: pointer;
            border-radius: 8px;
            border: 1px solid var(--card-border-color);
            overflow: hidden;
        }

        .role-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            transform: translateY(-3px);
        }

        .role-card.selected {
            border-color: var(--highlight-color);
            background-image: linear-gradient(127.09deg, rgba(6, 11, 40, 0.94) 19.41%, rgba(16, 26, 77, 0.75) 76.65%);
        }

        /* Form elements */
        .form-check-input:checked {
            background-color: var(--highlight-color);
            border-color: var(--highlight-color);
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
        }

        .form-check-label {
            color: var(--text-primary);
        }

        /* Buttons */
        .btn-primary {
            background-image: var(--btn-gradient);
            border: none;
            box-shadow: 0 4px 6px rgba(50, 50, 93, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: var(--hover-transition);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }

        .action-btn {
            border-radius: 6px;
            font-weight: 500;
            transition: var(--hover-transition);
        }

        .btn-outline-primary {
            color: var(--highlight-color);
            border-color: var(--highlight-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--highlight-color);
            color: #fff;
        }

        .btn-outline-info {
            color: #0dcaf0;
            border-color: #0dcaf0;
        }

        .btn-outline-info:hover {
            background-color: #0dcaf0;
            color: #fff;
        }

        /* Icons */
        .material-icons-outlined {
            vertical-align: middle;
        }

        .loading-icon {
            font-size: 2rem;
            color: var(--text-secondary);
            animation: spin 1.5s linear infinite;
            display: inline-block;
        }

        .role-icon {
            color: var(--highlight-color);
        }

        .info-icon {
            color: var(--text-secondary);
        }

        /* Text colors */
        .text-muted {
            color: var(--text-muted) !important;
        }

        .role-meta {
            line-height: 1.5;
        }

        /* Modal styling */
        .modal-content {
            background-image: var(--card-gradient);
            border: 1px solid var(--card-border-color);
            border-radius: 10px;
        }

        .modal-header {
            border-bottom: 1px solid var(--card-border-color);
            padding: 1rem 1.5rem;
        }

        .modal-title {
            color: var(--text-primary);
            font-weight: 600;
        }

        .modal-body {
            padding: 1.5rem;
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Permission groups styling */
        .permission-group {
            background-color: var(--permission-group-bg);
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 1px solid var(--card-border-color);
            overflow: hidden;
        }

        .permission-group-header {
            padding: 0.75rem 1rem;
            background-color: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid var(--card-border-color);
        }

        .permission-group-title {
            color: var(--text-primary);
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .permission-group-title i {
            margin-right: 0.5rem;
            color: var(--highlight-color);
        }

        .permission-group-body {
            padding: 1rem;
        }

        .permission-item {
            background-color: var(--permission-item-bg);
            border-radius: 6px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            transition: var(--hover-transition);
        }

        .permission-item:hover {
            background-color: var(--permission-item-hover);
        }

        .permission-item:last-child {
            margin-bottom: 0;
        }

        .permission-icon {
            margin-right: 0.75rem;
            color: var(--highlight-color);
            font-size: 1.25rem;
        }

        .permission-name {
            color: var(--text-primary);
            font-weight: 500;
        }

        .permission-description {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Animations */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {
            0% {
                opacity: 0.6;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.6;
            }
        }

        .pulse-animation {
            animation: pulse 1.5s infinite ease-in-out;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .card-header .d-flex {
                margin-top: 1rem;
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
