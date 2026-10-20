
import Chart from 'chart.js/auto';

function initializeDashboardCharts() {
    const dataElement = document.getElementById(
        'admin-dashboard-chart-data'
    );

    if (!dataElement) return;

    const dashboardData = JSON.parse(dataElement.textContent);

    const salesCanvas = document.getElementById('salesTrendChart');
    const stockCanvas = document.getElementById('stockMovementChart');
    const inventoryCanvas = document.getElementById('inventoryHealthChart');

    if (!salesCanvas || !stockCanvas || !inventoryCanvas) return;

    const colors = {
        green: '#0f766e',
        greenLight: 'rgba(15, 118, 110, 0.12)',
        blue: '#3b82f6',
        orange: '#f59e0b',
        red: '#ef4444',
        grid: '#e2e8f0',
        text: '#64748b',
    };

    const peso = (value) => {
        return '₱' + Number(value).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    };

    Chart.defaults.font.family = 'Arial, sans-serif';
    Chart.defaults.color = colors.text;

    let selectedPeriod = 'monthly';

    // SALES PERFORMANCE CHART
    const salesChart = new Chart(salesCanvas, {
        type: 'line',
        data: {
            labels: dashboardData.monthly.labels,
            datasets: [
                {
                    label: 'Completed Sales',
                    data: dashboardData.monthly.sales,
                    borderColor: colors.green,
                    backgroundColor: colors.greenLight,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: colors.green,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            return 'Sales: ' + peso(context.parsed.y);
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: colors.grid,
                    },
                    ticks: {
                        callback: (value) => {
                            return '₱' + Number(value).toLocaleString('en-PH');
                        },
                    },
                },
            },
        },
    });

    // STOCK MOVEMENT CHART
    const stockChart = new Chart(stockCanvas, {
        type: 'bar',
        data: {
            labels: dashboardData.monthly.labels,
            datasets: [
                {
                    label: 'Stock In',
                    data: dashboardData.monthly.stockIn,
                    backgroundColor: colors.green,
                    borderRadius: 5,
                    maxBarThickness: 30,
                },
                {
                    label: 'Stock Out',
                    data: dashboardData.monthly.stockOut,
                    backgroundColor: colors.orange,
                    borderRadius: 5,
                    maxBarThickness: 30,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        padding: 18,
                    },
                },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            return context.dataset.label + ': ' +
                                context.parsed.y.toLocaleString('en-PH') +
                                ' units';
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: colors.grid,
                    },
                    ticks: {
                        precision: 0,
                    },
                },
            },
        },
    });

    // INVENTORY HEALTH CHART
    const inventory = dashboardData.inventory;

    new Chart(inventoryCanvas, {
        type: 'doughnut',
        data: {
            labels: [
                'In Stock',
                'Low Stock',
                'Out of Stock',
            ],
            datasets: [
                {
                    data: [
                        inventory.inStock,
                        inventory.lowStock,
                        inventory.outOfStock,
                    ],
                    backgroundColor: [
                        colors.green,
                        colors.orange,
                        colors.red,
                    ],
                    borderWidth: 0,
                    hoverOffset: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        padding: 15,
                    },
                },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            return context.label + ': ' +
                                context.parsed + ' products';
                        },
                    },
                },
            },
        },
    });

    // UPDATE DAILY, WEEKLY OR MONTHLY CHARTS
    function updatePeriod(period) {
        if (!dashboardData[period]) return;

        selectedPeriod = period;

        const periodData = dashboardData[selectedPeriod];

        salesChart.data.labels = periodData.labels;
        salesChart.data.datasets[0].data = periodData.sales;

        stockChart.data.labels = periodData.labels;
        stockChart.data.datasets[0].data = periodData.stockIn;
        stockChart.data.datasets[1].data = periodData.stockOut;

        salesChart.update();
        stockChart.update();

        document.querySelectorAll('[data-dashboard-period]')
            .forEach((button) => {
                const isActive = button.dataset.dashboardPeriod === period;

                button.classList.toggle('active', isActive);
                button.setAttribute('aria-pressed', String(isActive));
            });
    }

    document.querySelectorAll('[data-dashboard-period]')
        .forEach((button) => {
            button.addEventListener('click', () => {
                updatePeriod(button.dataset.dashboardPeriod);
            });
        });
}

if (document.readyState === 'loading') {
    document.addEventListener(
        'DOMContentLoaded',
        initializeDashboardCharts
    );
} else {
    initializeDashboardCharts();
}
