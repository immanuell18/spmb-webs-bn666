// Dashboard Charts JavaScript Library
class DashboardCharts {
    constructor() {
        this.charts = {};
        this.refreshInterval = 30000; // 30 seconds
        this.init();
    }

    init() {
        this.loadChartLibrary();
        this.setupAutoRefresh();
    }

    loadChartLibrary() {
        if (typeof Chart === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = () => this.initializeCharts();
            document.head.appendChild(script);
        } else {
            this.initializeCharts();
        }
    }

    initializeCharts() {
        this.createKpiCards();
        this.createRegistrationTrendChart();
        this.createJurusanDistributionChart();
        this.createPaymentAnalyticsChart();
        this.createRevenueChart();
        this.createGeographicChart();
        this.createPerformanceMetrics();
    }

    createKpiCards() {
        fetch('/api/dashboard/kpi')
            .then(response => response.json())
            .then(data => {
                this.updateKpiCard('total-pendaftar', data.total_pendaftar, 'orang');
                this.updateKpiCard('sudah-verifikasi', data.sudah_verifikasi, 'orang');
                this.updateKpiCard('sudah-bayar', data.sudah_bayar, 'orang');
                this.updateKpiCard('conversion-rate', data.conversion_rate, '%');
                this.updateKpiCard('total-revenue', this.formatCurrency(data.total_revenue), '');
                
                // Update progress bar
                const progressBar = document.getElementById('kuota-progress');
                if (progressBar) {
                    progressBar.style.width = data.progress_percentage + '%';
                    progressBar.textContent = data.progress_percentage + '%';
                }
            })
            .catch(error => console.error('Error loading KPI data:', error));
    }

    createRegistrationTrendChart() {
        const ctx = document.getElementById('registrationTrendChart');
        if (!ctx) return;

        fetch('/api/dashboard/registration-trend?days=30')
            .then(response => response.json())
            .then(data => {
                if (this.charts.registrationTrend) {
                    this.charts.registrationTrend.destroy();
                }

                this.charts.registrationTrend = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Pendaftar Harian',
                            data: data.data,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            });
    }

    createJurusanDistributionChart() {
        const ctx = document.getElementById('jurusanDistributionChart');
        if (!ctx) return;

        fetch('/api/dashboard/jurusan-distribution')
            .then(response => response.json())
            .then(data => {
                if (this.charts.jurusanDistribution) {
                    this.charts.jurusanDistribution.destroy();
                }

                this.charts.jurusanDistribution = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: data.colors,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                this.drillDownJurusan(index);
                            }
                        }
                    }
                });
            });
    }

    createPaymentAnalyticsChart() {
        const ctx = document.getElementById('paymentAnalyticsChart');
        if (!ctx) return;

        fetch('/api/dashboard/payment-analytics')
            .then(response => response.json())
            .then(data => {
                if (this.charts.paymentAnalytics) {
                    this.charts.paymentAnalytics.destroy();
                }

                this.charts.paymentAnalytics = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Paid', 'Pending', 'Failed'],
                        datasets: [{
                            label: 'Transactions',
                            data: [
                                data.paid_transactions,
                                data.pending_transactions,
                                data.failed_transactions
                            ],
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
    }

    createRevenueChart() {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        fetch('/api/dashboard/payment-analytics')
            .then(response => response.json())
            .then(data => {
                if (this.charts.revenue) {
                    this.charts.revenue.destroy();
                }

                this.charts.revenue = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.revenue_trend.labels,
                        datasets: [{
                            label: 'Revenue',
                            data: data.revenue_trend.data,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            });
    }

    createGeographicChart() {
        const ctx = document.getElementById('geographicChart');
        if (!ctx) return;

        fetch('/api/dashboard/geographic-data')
            .then(response => response.json())
            .then(data => {
                if (this.charts.geographic) {
                    this.charts.geographic.destroy();
                }

                this.charts.geographic = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Pendaftar per Wilayah',
                            data: data.data,
                            backgroundColor: '#17a2b8',
                            borderColor: '#138496',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
    }

    createPerformanceMetrics() {
        fetch('/api/dashboard/performance-metrics')
            .then(response => response.json())
            .then(data => {
                this.updateMetricCard('avg-processing-time', data.avg_processing_time);
                this.updateMetricCard('active-users', data.active_users_today);
                this.updateMetricCard('error-rate', data.error_rate);
                this.updateMetricCard('database-size', data.database_size);
                
                // Update system usage
                if (data.system_usage) {
                    this.updateProgressBar('cpu-usage', data.system_usage.cpu_usage);
                    this.updateProgressBar('memory-usage', data.system_usage.memory_usage);
                    this.updateProgressBar('disk-usage', data.system_usage.disk_usage);
                }
            });
    }

    updateKpiCard(elementId, value, suffix) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = value + (suffix ? ' ' + suffix : '');
            element.classList.add('animate-update');
            setTimeout(() => element.classList.remove('animate-update'), 1000);
        }
    }

    updateMetricCard(elementId, value) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = value;
        }
    }

    updateProgressBar(elementId, value) {
        const element = document.getElementById(elementId);
        if (element) {
            const numericValue = parseInt(value);
            element.style.width = numericValue + '%';
            element.textContent = value;
            
            // Color coding
            if (numericValue > 80) {
                element.className = 'progress-bar bg-danger';
            } else if (numericValue > 60) {
                element.className = 'progress-bar bg-warning';
            } else {
                element.className = 'progress-bar bg-success';
            }
        }
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    drillDownJurusan(index) {
        // Implementation for drill-down functionality
        console.log('Drill down for jurusan index:', index);
    }

    setupAutoRefresh() {
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                this.refreshData();
            }
        }, this.refreshInterval);
    }

    refreshData() {
        this.createKpiCards();
        this.createPerformanceMetrics();
        
        // Show refresh indicator
        this.showRefreshIndicator();
    }

    showRefreshIndicator() {
        const indicator = document.getElementById('refresh-indicator');
        if (indicator) {
            indicator.style.display = 'block';
            setTimeout(() => {
                indicator.style.display = 'none';
            }, 2000);
        }
    }

    exportChart(chartName, filename) {
        const chart = this.charts[chartName];
        if (chart) {
            const url = chart.toBase64Image();
            const link = document.createElement('a');
            link.download = filename || chartName + '.png';
            link.href = url;
            link.click();
        }
    }

    refreshAllCharts() {
        this.initializeCharts();
        this.showRefreshIndicator();
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.dashboardCharts = new DashboardCharts();
});

// Export functions for global access
window.exportChart = function(chartName, filename) {
    if (window.dashboardCharts) {
        window.dashboardCharts.exportChart(chartName, filename);
    }
};

window.refreshDashboard = function() {
    if (window.dashboardCharts) {
        window.dashboardCharts.refreshAllCharts();
    }
};