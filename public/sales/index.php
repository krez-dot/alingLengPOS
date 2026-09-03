<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Sale;

$saleModel = new Sale();
$keyword = trim($_GET['q'] ?? '');
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$order = strtolower($_GET['order'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$sales = $saleModel->history($keyword, $dateFrom, $dateTo, $order, $perPage, $offset);

$activeNav = 'sales';
$pageTitle = 'Sales History - QuickTally';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Sales History</h1>
    <p>Every completed transaction. Click a row to view its receipt.</p>
</div>

<form method="get" class="filters-bar">
    <div class="field">
        <label>Search</label>
        <input type="text" name="q" placeholder="Reference No." value="<?= htmlspecialchars($keyword) ?>">
    </div>
    <div class="field">
        <label>From</label>
        <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>">
    </div>
    <div class="field">
        <label>To</label>
        <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>">
    </div>
    <div class="field">
        <label>Order</label>
        <select name="order">
            <option value="desc" <?= $order === 'DESC' ? 'selected' : '' ?>>Newest first</option>
            <option value="asc" <?= $order === 'ASC' ? 'selected' : '' ?>>Oldest first</option>
        </select>
    </div>
    <div class="filters-actions">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="<?= BASE_URL ?>/sales/index.php" class="btn btn-outline">Reset</a>
    </div>
</form>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr><th>Reference No.</th><th>Date</th><th>Customer</th><th>Cashier</th><th>Total</th><th>Paid</th><th>Change</th></tr>
        </thead>
        <tbody>
            <?php if (empty($sales)): ?>
                <tr class="empty-row"><td colspan="7">No sales transactions found.</td></tr>
            <?php endif; ?>
            <?php foreach ($sales as $sale): ?>
                <tr class="clickable-row" onclick="window.location='<?= BASE_URL ?>/sales/view.php?id=<?= $sale['id'] ?>'">
                    <td class="strong"><?= htmlspecialchars($sale['reference_no']) ?></td>
                    <td class="muted"><?= htmlspecialchars($sale['created_at']) ?></td>
                    <td>Walk-in</td>
                    <td>Admin</td>
                    <td class="price-cell">&#8369;<?= number_format((float) $sale['total_amount'], 2) ?></td>
                    <td>&#8369;<?= number_format((float) $sale['amount_paid'], 2) ?></td>
                    <td>&#8369;<?= number_format((float) $sale['change_due'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
