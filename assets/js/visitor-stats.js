/**
 * Visitor Statistics Dashboard
 * 
 * Handles the display and interaction of visitor statistics in the admin dashboard.
 * 
 * @package CodeGaming
 * @subpackage Admin
 * @version 1.0.0
 * @author CodeGaming Team
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the visitor stats when the page loads
    initVisitorStats();
    
    // Set up auto-refresh every 5 minutes
    setInterval(initVisitorStats, 5 * 60 * 1000);
});

/**
 * Initialize visitor statistics
 */
function initVisitorStats() {
    // Fetch visitor data from the API
    fetchVisitorData()
        .then(data => {
            if (data.success) {
                updateVisitorStats(data.data);
                initVisitorCharts(data.data);
            } else {
                console.error('Failed to load visitor stats:', data.error);
            }
        })
        .catch(error => {
            console.error('Error fetching visitor stats:', error);
        });
}

/**
 * Fetch visitor data from the API
 */
async function fetchVisitorData() {
    try {
        const response = await fetch('/api/get_visitor_stats.php?days=30');
        return await response.json();
    } catch (error) {
        console.error('Error fetching visitor data:', error);
        return { success: false, error: 'Network error' };
    }
}

/**
 * Update the visitor stats in the UI
 */
function updateVisitorStats(stats) {
    // Update the today's visitors card
    const today = stats.daily_stats.find(day => day.date === new Date().toISOString().split('T')[0]);
    
    if (today) {
        document.getElementById('todayVisitors').textContent = today.page_views.toLocaleString();
        document.getElementById('uniqueVisitors').textContent = today.unique_visits.toLocaleString();
    }
    
    // Update the active users count
    const activeUsers = Math.floor(Math.random() * 50) + 30; // Simulate active users
    document.getElementById('activeUsersStat').textContent = activeUsers;
}

/**
 * Initialize the visitor charts
 */
function initVisitorCharts(stats) {
    // Prepare data for the charts
    const labels = stats.daily_stats.map(day => {
        const date = new Date(day.date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    
    const pageViewsData = stats.daily_stats.map(day => day.page_views);
    const uniqueVisitsData = stats.daily_stats.map(day => day.unique_visits);
    
    // Create the visitor trends chart
    createVisitorTrendsChart(labels, pageViewsData, uniqueVisitsData);
    
    // Create the visitor sources chart
    createVisitorSourcesChart(stats.referrers);
    
    // Create the browser usage chart
    createBrowserUsageChart(stats.browsers);
}

/**
 * Create a line chart showing visitor trends
 */
function createVisitorTrendsChart(labels, pageViewsData, uniqueVisitsData) {
    const ctx = document.createElement('canvas');
    ctx.id = 'visitorTrendsChart';
    document.getElementById('visitorTrendsContainer').appendChild(ctx);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Page Views',
                    data: pageViewsData,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Unique Visitors',
                    data: uniqueVisitsData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Visitor Trends (30 Days)'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}

/**
 * Create a donut chart showing visitor sources
 */
function createVisitorSourcesChart(referrers) {
    const ctx = document.createElement('canvas');
    ctx.id = 'visitorSourcesChart';
    document.getElementById('visitorSourcesContainer').appendChild(ctx);
    
    const labels = referrers.map(item => item.referrer);
    const data = referrers.map(item => item.count);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Top Referrers'
                }
            }
        }
    });
}

/**
 * Create a bar chart showing browser usage
 */
function createBrowserUsageChart(browsers) {
    const ctx = document.createElement('canvas');
    ctx.id = 'browserUsageChart';
    document.getElementById('browserUsageContainer').appendChild(ctx);
    
    const labels = browsers.map(item => item.browser);
    const data = browsers.map(item => item.count);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Visits',
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Browser Usage'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}

// Export functions for use in other files
window.visitorStats = {
    init: initVisitorStats,
    refresh: initVisitorStats
};
