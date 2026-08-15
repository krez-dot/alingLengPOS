<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Product;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    redirect('/products/index.php');
}

try {
    (new Product())->delete((int) $_POST['id']);
    flash('success', 'Product deleted.');
} catch (\PDOException $e) {
    flash('danger', 'Cannot delete product: it is referenced by existing sales records.');
}

redirect('/products/index.php');
