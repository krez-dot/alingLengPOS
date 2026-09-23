<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Product;
use App\Models\StockMovement;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/products/index.php');
}

$id = (int) ($_POST['id'] ?? 0);
$quantity = (int) ($_POST['quantity'] ?? 0);

if ($id < 1 || $quantity < 1) {
    flash('danger', 'Enter a valid quantity to restock.');
    redirect('/products/index.php');
}

$productModel = new Product();
$product = $productModel->find($id);

if (!$product) {
    flash('danger', 'Product not found.');
    redirect('/products/index.php');
}

$productModel->restock($id, $quantity);
(new StockMovement())->log($id, 'in', $quantity, 'Manual restock');

flash('success', "Added {$quantity} units to \"{$product['name']}\".");
redirect('/products/index.php');
