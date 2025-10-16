<?php
/**
 * Admin Analytics Dashboard
 * 
 * Displays detailed visitor statistics and analytics for administrators.
 * 
 * @package CodeGaming
 * @subpackage Admin
 * @version 1.0.0
 * @author CodeGaming Team
 */

require_once 'includes/Auth.php';

$auth = Auth::getInstance();

// Redirect if not logged in or not admin
if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header('Location: login.php');
    exit;
}

$currentUser = $auth->getCurrentUser();
$currentRole = $auth->getCurrentRole();

// Set default time range (30 days)
$days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
$days = max(1, min(365, $days)); // Limit between 1 and 365 days
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - CodeGaming Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #123524;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --secondary-color: #858796;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }
        
        body {
            background-color: #f8f9fc;
            color: #5a5c69;
        }
        
        .sidebar {
            background: #123524;
            background: linear-gradient(180deg, #123524 10%, #224abe 100%);
            min-height: 100vh;
            color: white;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.35rem;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }
        
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .bg-success {
            background-color: var(--success-color) !important;
        }
        
        .bg-info {
            background-color: var(--info-color) !important;
        }
        
        .bg-warning {
            background-color: var(--warning-color) !important;
        }
        
        .bg-danger {
            background-color: var(--danger-color) !important;
        }
        
        .stat-card {
            border-left: 0.25rem solid;
            border-radius: 0.35rem;
        }
        
        .stat-card.primary {
            border-left-color: var(--primary-color);
        }
        
        .stat-card.success {
            border-left-color: var(--success-color);
        }
        
        .stat-card.info {
            border-left-color: var(--info-color);
        }
        
        .stat-card.warning {
            border-left-color: var(--warning-color);
        }
        
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #5a5c69;
        }
        
        .stat-card .stat-label {
            font-size: 0.875rem;
            color: #858796;
            text-transform: uppercase;
            font-weight: 700;
        }
        
        .stat-card .stat-icon {
            font-size: 2rem;
            color: #dddfeb;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            color: #858796;
        }
        
        .time-range-selector .btn {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4>CodeGaming</h4>
                        <p class="text-white-50 mb-0">Analytics Dashboard</p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">
                                <i class="fas fa-fw fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_analytics.php">
                                <i class="fas fa-fw fa-chart-line"></i>
                                Analytics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_users.php">
                                <i class="fas fa-fw fa-users"></i>
                                Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_announcements.php">
                                <i class="fas fa-fw fa-bullhorn"></i>
                                Announcements
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link" href="home_page.php">
                                <i class="fas fa-fw fa-arrow-left"></i>
                                Back to Site
                            </a>
                        </li>
                    </ul>
                    
                    <div class="position-absolute bottom-0 start-0 p-3 w-100">
                        <div class="text-center text-white-50 small">
                            <p class="mb-1">Logged in as: <strong><?php echo htmlspecialchars($currentUser['username']); ?></strong></p>
                            <a href="logout.php" class="btn btn-sm btn-outline-light w-100">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Analytics Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="exportPdf">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="exportCsv">
                                <i class="fas fa-file-csv me-1"></i> Export CSV
                            </button>
                        </div>
                        <div class="time-range-selector">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary" data-days="7">7d</button>
                                <button type="button" class="btn btn-outline-secondary active" data-days="30">30d</button>
                                <button type="button" class="btn btn-outline-secondary" data-days="90">90d</button>
                                <button type="button" class="btn btn-outline-secondary" data-days="180">180d</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card primary h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="stat-label">Total Page Views</div>
                                        <div class="stat-value" id="totalPageViews">0</div>
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            <span id="pageViewsChange" class="text-success">0%</span> from last period
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-eye stat-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card success h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="stat-label">Unique Visitors</div>
                                        <div class="stat-value" id="totalUniqueVisitors">0</div>
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            <span id="uniqueVisitorsChange" class="text-success">0%</span> from last period
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users stat-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card info h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="stat-label">Avg. Session Duration</div>
                                        <div class="stat-value" id="avgSessionDuration">0:00</div>
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            <span id="sessionDurationChange" class="text-success">0%</span> from last period
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock stat-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card warning h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="stat-label">Bounce Rate</div>
                                        <div class="stat-value" id="bounceRate">0%</div>
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            <span id="bounceRateChange" class="text-danger">0%</span> from last period
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-sign-out-alt stat-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Row -->
                <div class="row mb-4">
                    <!-- Visitor Trends -->
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold">Visitor Trends</h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="visitorTrendsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="visitorTrendsDropdown">
                                        <li><a class="dropdown-item" href="#">Export Data</a></li>
                                        <li><a class="dropdown-item" href="#">Print Chart</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#">Refresh</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="visitorTrendsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Top Pages -->
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">Top Pages</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <!-- Top pages will be loaded here by JavaScript -->
                                    <div class="text-center py-4" id="topPagesLoader">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Second Row -->
                <div class="row">
                    <!-- Referrers -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">Top Referrers</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="referrerChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Browsers & OS -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold">Browser & OS Usage</h6>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary active" data-chart="browser">Browsers</button>
                                    <button type="button" class="btn btn-outline-secondary" data-chart="os">OS</button>
                                    <button type="button" class="btn btn-outline-secondary" data-chart="device">Devices</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="browserOsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Data Tables Row -->
                <div class="row">
                    <!-- Recent Visitors -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold">Recent Visitors</h6>
                                <div>
                                    <div class="input-group input-group-sm" style="width: 200px;">
                                        <input type="text" class="form-control" placeholder="Search visitors...">
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="recentVisitorsTable">
                                        <thead>
                                            <tr>
                                                <th>IP Address</th>
                                                <th>Country</th>
                                                <th>Browser</th>
                                                <th>OS</th>
                                                <th>Device</th>
                                                <th>Last Visit</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Recent visitors will be loaded here by JavaScript -->
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="text-muted small">
                                        Showing <span id="visitorStart">1</span> to <span id="visitorEnd">10</span> of <span id="visitorTotal">0</span> entries
                                    </div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0">
                                            <li class="page-item disabled">
                                                <a class="page-link" href="#" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                                            <li class="page-item">
                                                <a class="page-link" href="#" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the analytics dashboard
            initializeAnalyticsDashboard();
            
            // Time range selector
            document.querySelectorAll('.time-range-selector .btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const days = this.getAttribute('data-days');
                    window.location.href = `admin_analytics.php?days=${days}`;
                });
            });
            
            // Set active button for current time range
            document.querySelectorAll(`.time-range-selector .btn[data-days="<?php echo $days; ?>"]`).forEach(btn => {
                btn.classList.add('active');
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-primary');
            });
            
            // Chart type toggle
            document.querySelectorAll('[data-chart]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const chartType = this.getAttribute('data-chart');
                    // Update active state
                    document.querySelectorAll('[data-chart]').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    // Update chart
                    updateBrowserOsChart(chartType);
                });
            });
        });
        
        function initializeAnalyticsDashboard() {
            // Load data from API
            const baseUrl = window.location.origin + '/CodeGaming';
            fetch(`${baseUrl}/api/get_visitor_stats.php?days=<?php echo $days; ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateStatsCards(data.data);
                        renderVisitorTrendsChart(data.data);
                        renderTopPages(data.data.top_pages || []);
                        renderReferrerChart(data.data.referrers || []);
                        renderBrowserOsChart(data.data.browsers || [], 'browser');
                        renderRecentVisitors(data.data.recent_visitors || []);
                    } else {
                        console.error('Failed to load analytics data:', data.error);
                        showError('Failed to load analytics data. Please try again later.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching analytics data:', error);
                    showError('An error occurred while loading analytics data.');
                });
        }
        
        function updateStatsCards(stats) {
            // Update stats cards with data
            document.getElementById('totalPageViews').textContent = stats.total_visits.toLocaleString();
            document.getElementById('totalUniqueVisitors').textContent = stats.unique_visits.toLocaleString();
            
            // Calculate average session duration (mock data)
            const avgDuration = Math.floor(Math.random() * 5) + 1 + Math.floor(Math.random() * 60) / 100;
            document.getElementById('avgSessionDuration').textContent = 
                `${Math.floor(avgDuration)}:${Math.floor((avgDuration % 1) * 60).toString().padStart(2, '0')}`;
                
            // Calculate bounce rate (mock data)
            const bounceRate = Math.floor(Math.random() * 30) + 40; // 40-70%
            document.getElementById('bounceRate').textContent = `${bounceRate}%`;
            
            // Update changes (mock data)
            const changes = ['+', '-'];
            const randomChange = changes[Math.floor(Math.random() * changes.length)];
            const randomPercent = Math.floor(Math.random() * 20) + 1;
            
            document.getElementById('pageViewsChange').textContent = 
                `${randomChange}${randomPercent}%`;
            document.getElementById('uniqueVisitorsChange').textContent = 
                `${randomChange}${randomPercent - 2}%`;
            document.getElementById('sessionDurationChange').textContent = 
                `${randomChange}${randomPercent + 2}%`;
            document.getElementById('bounceRateChange').textContent = 
                `${randomChange}${randomPercent - 5}%`;
                
            // Set color based on change
            const updateChangeColor = (elementId, isPositive) => {
                const element = document.getElementById(elementId);
                element.classList.remove('text-success', 'text-danger');
                element.classList.add(isPositive ? 'text-success' : 'text-danger');
            };
            
            updateChangeColor('pageViewsChange', randomChange === '+');
            updateChangeColor('uniqueVisitorsChange', randomChange === '+');
            updateChangeColor('sessionDurationChange', randomChange === '+');
            updateChangeColor('bounceRateChange', randomChange === '-');
        }
        
        function renderVisitorTrendsChart(data) {
            const ctx = document.getElementById('visitorTrendsChart').getContext('2d');
            
            // Prepare data
            const labels = data.daily_stats.map(day => {
                const date = new Date(day.date);
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            });
            
            const pageViews = data.daily_stats.map(day => day.page_views);
            const uniqueVisits = data.daily_stats.map(day => day.unique_visits);
            
            // Create chart
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Page Views',
                            data: pageViews,
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.05)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#4e73df',
                            pointBorderColor: '#fff',
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#4e73df',
                            pointHoverBorderColor: '#fff',
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Unique Visitors',
                            data: uniqueVisits,
                            borderColor: '#1cc88a',
                            backgroundColor: 'rgba(28, 200, 138, 0.05)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#1cc88a',
                            pointBorderColor: '#fff',
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#1cc88a',
                            pointHoverBorderColor: '#fff',
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 12,
                            displayColors: true,
                            intersect: false,
                            mode: 'index',
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7,
                                maxRotation: 0,
                                autoSkip: true
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function renderTopPages(pages) {
            const container = document.querySelector('#topPagesLoader').parentNode;
            container.innerHTML = '';
            
            if (!pages || pages.length === 0) {
                container.innerHTML = '<div class="text-center py-4 text-muted">No data available</div>';
                return;
            }
            
            pages.forEach((page, index) => {
                const pageUrl = page.page.length > 30 ? page.page.substring(0, 30) + '...' : page.page;
                const percentage = Math.round((page.visits / pages[0].visits) * 100);
                
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                item.innerHTML = `
                    <div class="me-3">
                        <div class="text-truncate" style="max-width: 200px;" title="${page.page}">
                            ${pageUrl}
                        </div>
                        <div class="progress mt-1" style="height: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: ${percentage}%" 
                                 aria-valuenow="${percentage}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <span class="badge bg-primary rounded-pill">${page.visits.toLocaleString()}</span>
                `;
                container.appendChild(item);
            });
        }
        
        function renderReferrerChart(referrers) {
            const ctx = document.getElementById('referrerChart').getContext('2d');
            
            // Prepare data
            const labels = referrers.map(ref => ref.referrer);
            const data = referrers.map(ref => ref.count);
            
            // Colors
            const backgroundColors = [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                '#5a5c69', '#858796', '#e83e8c', '#fd7e14', '#20c9a6'
            ];
            
            // Create chart
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: backgroundColors.map(c => `${c}cc`),
                        hoverBorderColor: 'rgba(234, 236, 244, 1)',
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
        
        let browserOsChart = null;
        
        function renderBrowserOsChart(data, type = 'browser') {
            const ctx = document.getElementById('browserOsChart').getContext('2d');
            
            // Prepare data based on type
            let labels = [];
            let chartData = [];
            
            if (type === 'browser' && data.browsers) {
                labels = data.browsers.map(item => item.browser);
                chartData = data.browsers.map(item => item.count);
            } else if (type === 'os' && data.os) {
                labels = data.os.map(item => item.os);
                chartData = data.os.map(item => item.count);
            } else if (type === 'device' && data.devices) {
                labels = data.devices.map(item => item.device);
                chartData = data.devices.map(item => item.count);
            }
            
            // Colors
            const backgroundColors = [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                '#5a5c69', '#858796', '#e83e8c', '#fd7e14', '#20c9a6'
            ];
            
            // Destroy previous chart if exists
            if (browserOsChart) {
                browserOsChart.destroy();
            }
            
            // Create new chart
            browserOsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: type === 'browser' ? 'Browser' : (type === 'os' ? 'OS' : 'Device'),
                        data: chartData,
                        backgroundColor: backgroundColors,
                        borderColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `${context.parsed.x.toLocaleString()} visits`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        },
                        y: {
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        }
        
        function updateBrowserOsChart(type) {
            // In a real implementation, you would fetch new data based on the type
            // For now, we'll just update the chart with the same data but different labels
            const data = {
                browsers: [
                    { browser: 'Chrome', count: 1500 },
                    { browser: 'Firefox', count: 800 },
                    { browser: 'Safari', count: 600 },
                    { browser: 'Edge', count: 400 },
                    { browser: 'Brave', count: 200 },
                    { browser: 'Other', count: 200 }
                ],
                os: [
                    { os: 'Windows', count: 1800 },
                    { os: 'macOS', count: 1000 },
                    { os: 'iOS', count: 500 },
                    { os: 'Android', count: 300 },
                    { os: 'Linux', count: 200 },
                    { os: 'Other', count: 100 }
                ],
                devices: [
                    { device: 'Desktop', count: 2000 },
                    { device: 'Mobile', count: 1200 },
                    { device: 'Tablet', count: 300 },
                    { device: 'Laptop', count: 200 },
                    { device: 'Other', count: 100 }
                ]
            };
            
            renderBrowserOsChart(data, type);
        }
        
        function renderRecentVisitors(visitors) {
            const tbody = document.querySelector('#recentVisitorsTable tbody');
            tbody.innerHTML = '';
            
            if (!visitors || visitors.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="7" class="text-center py-4 text-muted">No recent visitors found</td>';
                tbody.appendChild(tr);
                return;
            }
            
            visitors.forEach(visitor => {
                const tr = document.createElement('tr');
                
                // Format last visit time
                const lastVisit = new Date(visitor.visit_time);
                const timeAgo = timeSince(lastVisit);
                
                // Get country flag (using a placeholder service)
                const countryCode = visitor.country ? visitor.country.toLowerCase() : 'xx';
                const flagUrl = `https://flagcdn.com/16x12/${countryCode}.png`;
                
                tr.innerHTML = `
                    <td>
                        <span class="badge bg-light text-dark">${visitor.ip_address}</span>
                    </td>
                    <td>
                        ${visitor.country ? 
                            `<img src="${flagUrl}" alt="${visitor.country}" class="me-1" style="width: 16px; height: 12px;">
                             ${visitor.country}` : 
                            '<span class="text-muted">Unknown</span>'}
                    </td>
                    <td>${visitor.browser || 'Unknown'}</td>
                    <td>${visitor.os || 'Unknown'}</td>
                    <td>${visitor.device_type || 'Unknown'}</td>
                    <td title="${lastVisit.toLocaleString()}">${timeAgo} ago</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" title="View details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            
            // Update pagination info
            document.getElementById('visitorTotal').textContent = visitors.length;
            document.getElementById('visitorStart').textContent = 1;
            document.getElementById('visitorEnd').textContent = Math.min(10, visitors.length);
        }
        
        function timeSince(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            
            let interval = Math.floor(seconds / 31536000);
            if (interval >= 1) return interval + ' year' + (interval === 1 ? '' : 's');
            
            interval = Math.floor(seconds / 2592000);
            if (interval >= 1) return interval + ' month' + (interval === 1 ? '' : 's');
            
            interval = Math.floor(seconds / 86400);
            if (interval >= 1) return interval + ' day' + (interval === 1 ? '' : 's');
            
            interval = Math.floor(seconds / 3600);
            if (interval >= 1) return interval + ' hour' + (interval === 1 ? '' : 's');
            
            interval = Math.floor(seconds / 60);
            if (interval >= 1) return interval + ' minute' + (interval === 1 ? '' : 's');
            
            return Math.floor(seconds) + ' second' + (seconds === 1 ? '' : 's');
        }
        
        function showError(message) {
            // In a real implementation, you would show a nice error message to the user
            console.error(message);
            alert(message);
        }
    </script>
</body>
</html>
