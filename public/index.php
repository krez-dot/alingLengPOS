<?php

require __DIR__ . '/../bootstrap.php';

use App\Models\Product;
use App\Models\Sale;

$productModel = new Product();
$saleModel = new Sale();

$totalProducts = count($productModel->all());
$lowStockItems = $productModel->lowStock();
$todayTotal = $saleModel->todayTotal();
$todayCount = $saleModel->todayCount();
$recentSales = $saleModel->history('', '', 5, 0);

$pageTitle = 'Dashboard - Sari-Sari POS';
require __DIR__ . '/includes/header.php';
?>

<h1 class="mb-4">Dashboard</h1>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body">
                <h6 class="card-title">Total Products</h6>
                <p class="fs-3 mb-0"><?= $totalProducts ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h6 class="card-title">Low Stock Items</h6>
                <p class="fs-3 mb-0"><?= count($lowStockItems) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body">
                <h6 class="card-title">Today's Sales</h6>
                <p class="fs-3 mb-0">&#8369;<?= number_format($todayTotal, 2) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info">
            <div class="card-body">
                <h6 class="card-title">Transactions Today</h6>
                <p class="fs-3 mb-0"><?= $todayCount ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Low Stock Alerts</div>
            <ul class="list-group list-group-flush">
                <?php if (empty($lowStockItems)): ?>
                    <li class="list-group-item text-muted">All products are sufficiently stocked.</li>
                <?php endif; ?>
                <?php foreach ($lowStockItems as $item): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= htmlspecialchars($item['name']) ?></span>
                        <span class="badge bg-danger"><?= $item['stock_quantity'] ?> left</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Recent Sales</div>
            <ul class="list-group list-group-flush">
                <?php if (empty($recentSales)): ?>
                    <li class="list-group-item text-muted">No sales recorded yet.</li>
                <?php endif; ?>
                <?php foreach ($recentSales as $sale): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= htmlspecialchars($sale['reference_no']) ?> &mdash; <?= htmlspecialchars($sale['created_at']) ?></span>
                        <span>&#8369;<?= number_format((float) $sale['total_amount'], 2) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
