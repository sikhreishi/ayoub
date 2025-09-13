@extends('layouts.app')

@section('content')
<div class="container-fluid dashboard-stats">
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-4">
            <div class="stat-card stat-users">
                <div class="stat-accent"></div>
                <div class="d-flex align-items-center {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                    <div class="stat-icon text-primary"><i class="bi bi-people"></i></div>
                    <div class="{{ app()->getLocale() == 'ar' ? 'me-3 text-end' : 'ms-3' }}">
                        <div class="stat-label">{{ __('dashboard.stats.total_users') }}</div>
                        <div class="stat-value">{{ number_format($totalUsers) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="stat-card stat-drivers">
                <div class="stat-accent"></div>
                <div class="d-flex align-items-center {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                    <div class="stat-icon text-success"><i class="bi bi-person-badge"></i></div>
                    <div class="{{ app()->getLocale() == 'ar' ? 'me-3 text-end' : 'ms-3' }}">
                        <div class="stat-label">{{ __('dashboard.stats.total_drivers') }}</div>
                        <div class="stat-value">{{ number_format($totalDrivers) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="stat-card stat-trips">
                <div class="stat-accent"></div>
                <div class="d-flex align-items-center {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                    <div class="stat-icon text-warning"><i class="bi bi-car-front"></i></div>
                    <div class="{{ app()->getLocale() == 'ar' ? 'me-3 text-end' : 'ms-3' }}">
                        <div class="stat-label">{{ __('dashboard.stats.total_trips') }}</div>
                        <div class="stat-value">{{ number_format($totalTrips) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-4">
            <div class="stat-card stat-completed">
                <div class="stat-accent"></div>
                <div class="d-flex align-items-center {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                    <div class="stat-icon text-success"><i class="bi bi-check-circle"></i></div>
                    <div class="{{ app()->getLocale() == 'ar' ? 'me-3 text-end' : 'ms-3' }}">
                        <div class="stat-label">{{ __('dashboard.stats.completed_trips') }}</div>
                        <div class="stat-value">{{ number_format($completedTrips) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="stat-card stat-cancelled">
                <div class="stat-accent"></div>
                <div class="d-flex align-items-center {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                    <div class="stat-icon text-danger"><i class="bi bi-x-circle"></i></div>
                    <div class="{{ app()->getLocale() == 'ar' ? 'me-3 text-end' : 'ms-3' }}">
                        <div class="stat-label">{{ __('dashboard.stats.cancelled_trips') }}</div>
                        <div class="stat-value">{{ number_format($cancelledTrips) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="stat-card stat-ongoing">
                <div class="stat-accent"></div>
                <div class="d-flex align-items-center {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                    <div class="stat-icon text-warning"><i class="bi bi-clock-history"></i></div>
                    <div class="{{ app()->getLocale() == 'ar' ? 'me-3 text-end' : 'ms-3' }}">
                        <div class="stat-label">{{ __('dashboard.stats.ongoing_trips') }}</div>
                        <div class="stat-value">{{ number_format($ongoingTrips) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card shadow-lg border-0 chart-card">
                <div class="card-header bg-gradient text-white text-center">
                    <h5 class="mb-0">{{ __('dashboard.stats.trips_distribution') }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="chart-container">
                        <canvas id="tripsChart"></canvas>
                    </div>
                    <div class="mt-4 d-flex justify-content-around chart-stats">
                        <div class="stat-item d-flex align-items-center">
                            <div class="stat-color completed me-2"></div>
                            <div>
                                <div class="stat-label">{{ __('dashboard.stats.completed') }}</div>
                                <div class="stat-number">{{ number_format($completedTrips) }}</div>
                            </div>
                        </div>
                        <div class="stat-item d-flex align-items-center">
                            <div class="stat-color cancelled me-2"></div>
                            <div>
                                <div class="stat-label">{{ __('dashboard.stats.cancelled') }}</div>
                                <div class="stat-number">{{ number_format($cancelledTrips) }}</div>
                            </div>
                        </div>
                        <div class="stat-item d-flex align-items-center">
                            <div class="stat-color ongoing me-2"></div>
                            <div>
                                <div class="stat-label">{{ __('dashboard.stats.ongoing') }}</div>
                                <div class="stat-number">{{ number_format($ongoingTrips) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="total-trips mt-4 text-center">
                        <div class="total-label">{{ __('dashboard.stats.total_trips') }}</div>
                        <div class="total-number">{{ number_format($totalTrips) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('plugin-styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-stats .stat-card {
            border-radius: 1.2rem;
            box-shadow: 0 2px 16px 0 rgba(60,72,100,.08), 0 1.5px 4px 0 rgba(60,72,100,.04);
            background: #fff;
            padding: 1.5rem 1.2rem;
            position: relative;
            transition: transform 0.15s, box-shadow 0.15s;
            min-height: 110px;
            overflow: hidden;
        }
        .dashboard-stats .stat-card:hover {
            transform: translateY(-4px) scale(1.025);
            box-shadow: 0 6px 32px 0 rgba(60,72,100,.16), 0 2px 8px 0 rgba(60,72,100,.08);
        }
        .dashboard-stats .stat-accent {
            height: 5px;
            width: 100%;
            position: absolute;
            top: 0; left: 0;
            border-radius: 1.2rem 1.2rem 0 0;
            background: linear-gradient(90deg, #4e73df 0%, #1cc88a 100%);
            opacity: 0.18;
        }
        .dashboard-stats .stat-users .stat-accent { background: linear-gradient(90deg, #4e73df 0%, #36b9cc 100%); }
        .dashboard-stats .stat-drivers .stat-accent { background: linear-gradient(90deg, #1cc88a 0%, #198754 100%); }
        .dashboard-stats .stat-trips .stat-accent { background: linear-gradient(90deg, #f6c23e 0%, #e0a800 100%); }
        .dashboard-stats .stat-earnings .stat-accent { background: linear-gradient(90deg, #36b9cc 0%, #0dcaf0 100%); }
        .dashboard-stats .stat-completed .stat-accent { background: linear-gradient(90deg, #198754 0%, #1cc88a 100%); }
        .dashboard-stats .stat-cancelled .stat-accent { background: linear-gradient(90deg, #dc3545 0%, #f6c23e 100%); }
        .dashboard-stats .stat-ongoing .stat-accent { background: linear-gradient(90deg, #ffc107 0%, #fd7e14 100%); }
        .dashboard-stats .stat-wallets .stat-accent { background: linear-gradient(90deg, #36b9cc 0%, #4e73df 100%); }
        .dashboard-stats .stat-balance .stat-accent { background: linear-gradient(90deg, #4e73df 0%, #0dcaf0 100%); }
        .dashboard-stats .stat-icon {
            font-size: 2.7rem;
            opacity: 0.85;
            min-width: 48px;
            text-align: center;
        }
        .dashboard-stats .stat-label {
            font-size: 1.05rem;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 0.15rem;
        }
        .dashboard-stats .stat-value {
            font-size: 2.1rem;
            font-weight: 700;
            color: #222;
            letter-spacing: 1px;
        }
        .dashboard-stats .chart-card {
            border-radius: 1.2rem;
            background: #fff;
            box-shadow: 0 2px 16px 0 rgba(60,72,100,.08), 0 1.5px 4px 0 rgba(60,72,100,.04);
            overflow: hidden;
        }
        .dashboard-stats .chart-card .card-header.bg-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none;
        }
        .dashboard-stats .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 350px;
        }
        .dashboard-stats .chart-stats .stat-item {
            padding: 0.75rem;
            border-radius: 0.5rem;
            background: rgba(102, 126, 234, 0.05);
            transition: all 0.3s ease;
        }
        .dashboard-stats .chart-stats .stat-item:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateX(5px);
        }
        .dashboard-stats .stat-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .dashboard-stats .stat-color.completed {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        .dashboard-stats .stat-color.cancelled {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
        }
        .dashboard-stats .stat-color.ongoing {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        .dashboard-stats .chart-stats .stat-label {
            font-size: 0.9rem;
            color: #f0f0f0f0;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        .dashboard-stats .chart-stats .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fdfdfdfd;
        }
        .dashboard-stats .total-trips {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            padding: 1rem;
            border-radius: 0.75rem;
        }
        .dashboard-stats .total-label {
            font-size: 0.9rem;
            color: #f0f0f0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .dashboard-stats .total-number {
            font-size: 2rem;
            font-weight: 800;
            color: #fdfdfdfd;
            margin-top: 0.25rem;
        }
        @media (max-width: 767px) {
            .dashboard-stats .stat-value { font-size: 1.4rem; }
            .dashboard-stats .stat-label { font-size: 0.95rem; }
            .dashboard-stats .stat-icon { font-size: 2rem; }
        }
        body.dark-mode .dashboard-stats .stat-card, body.dark-mode .dashboard-stats .chart-card {
            background: #23272b;
            color: #f8f9fa;
        }
        body.dark-mode .dashboard-stats .stat-label { color: #adb5bd; }
        body.dark-mode .dashboard-stats .stat-value { color: #fff; }
    </style>
@endpush
@push('plugin-scripts')
    <script src="/assets/plugins/chartjs/js/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('tripsChart').getContext('2d');

            // Create gradient backgrounds
            const completedGradient = ctx.createLinearGradient(0, 0, 0, 300);
            completedGradient.addColorStop(0, 'rgba(40, 167, 69, 0.9)');
            completedGradient.addColorStop(1, 'rgba(32, 201, 151, 0.7)');

            const cancelledGradient = ctx.createLinearGradient(0, 0, 0, 300);
            cancelledGradient.addColorStop(0, 'rgba(220, 53, 69, 0.9)');
            cancelledGradient.addColorStop(1, 'rgba(253, 126, 20, 0.7)');

            const ongoingGradient = ctx.createLinearGradient(0, 0, 0, 300);
            ongoingGradient.addColorStop(0, 'rgba(255, 193, 7, 0.9)');
            ongoingGradient.addColorStop(1, 'rgba(253, 126, 20, 0.7)');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Cancelled', 'Ongoing'],
                    datasets: [{
                        data: [
                            {{ $completedTrips }},
                            {{ $cancelledTrips }},
                            {{ $ongoingTrips }}
                        ],
                        backgroundColor: [
                            completedGradient,
                            cancelledGradient,
                            ongoingGradient
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(255, 193, 7, 1)'
                        ],
                        borderWidth: 3,
                        hoverBorderWidth: 5,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            display: false // Hide default legend since we have custom stats
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed * 100) / total).toFixed(1);
                                    return context.label + ': ' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    },
                    elements: {
                        arc: {
                            borderJoinStyle: 'round'
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'point'
                    }
                }
            });
        });
    </script>
@endpush
@endsection


