<?php

require __DIR__ . '/../../bootstrap.php';

use App\Exceptions\InsufficientStockException;
use App\Models\Sale;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['items'])) {
    flash('danger', 'No items in cart.');
    redirect('/pos/index.php');
}

$items = [];
foreach ($_POST['items'] as $item) {
    if (empty($item['product_id']) || empty($item['quantity'])) {
        continue;
    }
    $items[] = [
        'product_id' => (int) $item['product_id'],
        'quantity' => (int) $item['quantity'],
        'unit_price' => (float) $item['unit_price'],
    ];
}

$amountPaid = (float) ($_POST['amount_paid'] ?? 0);
$discountType = in_array($_POST['discount_type'] ?? 'none', ['senior', 'pwd'], true) ? $_POST['discount_type'] : 'none';
$discountConfirmed = ($_POST['discount_confirmed'] ?? '') === '1';

if (empty($items)) {
    flash('danger', 'No valid items in cart.');
    redirect('/pos/index.php');
}

if ($discountType !== 'none' && !$discountConfirmed) {
    flash('danger', 'Please confirm the Senior Citizen / PWD ID was presented to apply the discount.');
    redirect('/pos/index.php');
}

$subtotal = array_reduce($items, fn ($carry, $item) => $carry + $item['quantity'] * $item['unit_price'], 0.0);
$discountAmount = $discountType !== 'none' ? round($subtotal * Sale::DISCOUNT_RATE, 2) : 0.0;
$total = $subtotal - $discountAmount;

if ($amountPaid < $total) {
    flash('danger', 'Amount paid is less than the total due.');
    redirect('/pos/index.php');
}

try {
    $saleId = (new Sale())->create([
        'reference_no' => 'TXN-' . date('YmdHis') . '-' . random_int(100, 999),
        'items' => $items,
        'amount_paid' => $amountPaid,
        'discount_type' => $discountType,
    ]);

    flash('success', 'Sale completed successfully.');
    redirect('/sales/view.php?id=' . $saleId);
} catch (InsufficientStockException $e) {
    flash('danger', 'Sale failed: ' . $e->getMessage());
    redirect('/pos/index.php');
} catch (\Throwable $e) {
    flash('danger', 'An unexpected error occurred. Please try again.');
    redirect('/pos/index.php');
}
