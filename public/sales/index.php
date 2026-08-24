<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Sale;

$saleModel = new Sale();
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$sales = $saleModel->history($dateFrom, $dateTo, $perPage, $offset);

$pageTitle = 'Sales History - Sari-Sari POS';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Sales History</h1>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-3">
        <label class="form-label">From</label>
        <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">To</label>
        <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
    </div>
    <div class="col-md-2 align-self-end">
        <button class="btn btn-outline-secondary w-100" type="submit">Filter</button>
    </div>
</form>

<table class="table table-striped bg-white">
    <thead><tr><th>Reference No.</th><th>Date</th><th>Total</th><th>Paid</th><th>Change</th><th></th></tr></thead>
    <tbody>
        <?php if (empty($sales)): ?>
            <tr><td colspan="6" class="text-center text-muted">No sales found.</td></tr>
        <?php endif; ?>
        <?php foreach ($sales as $sale): ?>
            <tr>
                <td><?= htmlspecialchars($sale['reference_no']) ?></td>
                <td><?= htmlspecialchars($sale['created_at']) ?></td>
                <td>&#8369;<?= number_format((float) $sale['total_amount'], 2) ?></td>
                <td>&#8369;<?= number_format((float) $sale['amount_paid'], 2) ?></td>
                <td>&#8369;<?= number_format((float) $sale['change_due'], 2) ?></td>
                <td><a href="<?= BASE_URL ?>/sales/view.php?id=<?= $sale['id'] ?>" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../includes/footer.php'; ?>
