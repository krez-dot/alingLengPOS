<?php

require __DIR__ . '/../bootstrap.php';

use App\Models\Product;
use App\Models\Sale;

$productModel = new Product();
$saleModel = new Sale();

$allProducts = $productModel->all();
$totalProducts = count($allProducts);
$totalStock = array_sum(array_column($allProducts, 'stock_quantity'));
$lowStockItems = $productModel->lowStock();
$todayTotal = $saleModel->todayTotal();
$todayCount = $saleModel->todayCount();
$recentSales = $saleModel->history('', '', '', 'DESC', 5, 0);
$topSelling = $saleModel->topSelling(5);
$salesTrend = $saleModel->dailyTotals(7);

$activeNav = 'dashboard';
$pageTitle = "Dashboard - Aling Leng's Sari-Sari Store";
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1>Good day, Admin &#128075;</h1>
    <p><?= date('l, F j, Y') ?> &middot; Quick summary of today's sales and current inventory status.</p>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-label">Today's Sales</div>
        <div class="stat-value green">&#8369;<?= number_format($todayTotal, 2) ?></div>
        <div class="stat-sub"><?= $todayCount ?> transaction<?= $todayCount === 1 ? '' : 's' ?> today</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Transactions Today</div>
        <div class="stat-value"><?= $todayCount ?></div>
        <div class="stat-sub">Completed sales</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Stock on Hand</div>
        <div class="stat-value"><?= $totalStock ?></div>
        <div class="stat-sub">Units across <?= $totalProducts ?> product<?= $totalProducts === 1 ? '' : 's' ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Low-Stock Products</div>
        <div class="stat-value"><?= count($lowStockItems) ?></div>
        <div class="stat-sub">At or below reorder level</div>
    </div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-header-row"><strong>Sales &mdash; Last 7 Days</strong></div>
    <canvas id="salesTrendChart" height="90"></canvas>
</div>

<div class="dash-grid">
    <div class="card">
        <div class="card-header-row">
            <strong>Recent Transactions</strong>
            <a href="<?= BASE_URL ?>/sales/index.php" class="link-accent">View all</a>
        </div>
        <?php if (empty($recentSales)): ?>
            <p class="empty-text">No sales recorded yet.</p>
        <?php else: ?>
            <ul class="simple-list">
                <?php foreach ($recentSales as $sale): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/sales/view.php?id=<?= $sale['id'] ?>">
                            <span><?= htmlspecialchars($sale['reference_no']) ?></span>
                            <span class="price-cell">&#8369;<?= number_format((float) $sale['total_amount'], 2) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-header-row">
            <strong>&#9888;&#65039; Low Stock</strong>
            <span class="badge-pill <?= empty($lowStockItems) ? 'badge-ok' : 'badge-low' ?>"><?= count($lowStockItems) ?> item<?= count($lowStockItems) === 1 ? '' : 's' ?></span>
        </div>
        <?php if (empty($lowStockItems)): ?>
            <p class="empty-text">All products are above their reorder level.</p>
        <?php else: ?>
            <ul class="simple-list">
                <?php foreach ($lowStockItems as $item): ?>
                    <li>
                        <span><?= htmlspecialchars($item['name']) ?></span>
                        <span class="badge-pill badge-low"><?= (int) $item['stock_quantity'] ?> left</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header-row"><strong>&#127942; Top-Selling Products</strong></div>
    <?php if (empty($topSelling)): ?>
        <p class="empty-text">No sales recorded yet.</p>
    <?php else: ?>
        <ul class="simple-list">
            <?php foreach ($topSelling as $t): ?>
                <li>
                    <span><?= htmlspecialchars($t['name']) ?></span>
                    <span class="stat-sub"><?= (int) $t['total_qty'] ?> sold</span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
var salesTrend = <?= json_encode($salesTrend) ?>;

Chart.defaults.font.family = "'Poppins', sans-serif";

new Chart(document.getElementById('salesTrendChart'), {
    type: 'line',
    data: {
        labels: salesTrend.map(function (d) {
            var date = new Date(d.date + 'T00:00:00');
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }),
        datasets: [{
            label: 'Sales',
            data: salesTrend.map(function (d) { return d.total; }),
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22, 163, 74, 0.12)',
            fill: true,
            tension: 0.35,
            pointBackgroundColor: '#16a34a',
            pointRadius: 4,
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function (ctx) { return '₱' + ctx.parsed.y.toFixed(2); }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: function (v) { return '₱' + v; } },
                grid: { color: '#f1efe9' }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
