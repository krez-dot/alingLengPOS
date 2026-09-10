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

$activeNav = 'sales';
$pageTitle = 'Sale Receipt - QuickTally';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Receipt: <?= htmlspecialchars($sale['reference_no']) ?></h1>
    <p><?= htmlspecialchars($sale['created_at']) ?></p>
</div>

<div class="card" style="max-width:520px;">
    <table class="data-table">
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= (int) $item['quantity'] ?></td>
                    <td>&#8369;<?= number_format((float) $item['unit_price'], 2) ?></td>
                    <td class="price-cell">&#8369;<?= number_format((float) $item['subtotal'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="receipt-summary">
        <div class="order-row"><span>Subtotal</span><span>&#8369;<?= number_format((float) $sale['subtotal_amount'], 2) ?></span></div>
        <?php if ($sale['discount_type'] !== 'none'): ?>
            <div class="order-row" style="color:var(--red-600);">
                <span><?= $sale['discount_type'] === 'pwd' ? 'PWD' : 'Senior Citizen' ?> Discount (&minus;20%) &mdash; ID presented</span>
                <span>&minus;&#8369;<?= number_format((float) $sale['discount_amount'], 2) ?></span>
            </div>
        <?php endif; ?>
        <div class="order-row"><span>Total</span><strong>&#8369;<?= number_format((float) $sale['total_amount'], 2) ?></strong></div>
        <div class="order-row"><span>Amount Paid</span><span>&#8369;<?= number_format((float) $sale['amount_paid'], 2) ?></span></div>
        <div class="order-row"><span>Change</span><span>&#8369;<?= number_format((float) $sale['change_due'], 2) ?></span></div>
    </div>
</div>

<a href="<?= BASE_URL ?>/sales/index.php" class="btn btn-outline" style="margin-top:16px;">&larr; Back to Sales History</a>

<?php require __DIR__ . '/../includes/footer.php'; ?>
