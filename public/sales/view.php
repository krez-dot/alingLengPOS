<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Sale;

$saleModel = new Sale();
$saleId = (int) ($_GET['id'] ?? 0);
$sale = $saleModel->find($saleId);

if (!$sale) {
    flash('danger', 'Sale not found.');
    redirect('/sales/index.php');
}

$items = $saleModel->itemsFor($saleId);

$pageTitle = 'Sale Receipt - Sari-Sari POS';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Receipt: <?= htmlspecialchars($sale['reference_no']) ?></h1>

<div class="card" style="max-width: 500px;">
    <div class="card-body">
        <p><strong>Date:</strong> <?= htmlspecialchars($sale['created_at']) ?></p>
        <table class="table table-sm">
            <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= (int) $item['quantity'] ?></td>
                        <td>&#8369;<?= number_format((float) $item['unit_price'], 2) ?></td>
                        <td>&#8369;<?= number_format((float) $item['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="fs-5"><strong>Total:</strong> &#8369;<?= number_format((float) $sale['total_amount'], 2) ?></p>
        <p><strong>Amount Paid:</strong> &#8369;<?= number_format((float) $sale['amount_paid'], 2) ?></p>
        <p><strong>Change:</strong> &#8369;<?= number_format((float) $sale['change_due'], 2) ?></p>
    </div>
</div>

<a href="/sales/index.php" class="btn btn-outline-secondary mt-3">Back to Sales History</a>

<?php require __DIR__ . '/../includes/footer.php'; ?>
