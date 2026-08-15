<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Supplier;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    redirect('/suppliers/index.php');
}

try {
    (new Supplier())->delete((int) $_POST['id']);
    flash('success', 'Supplier deleted.');
} catch (\PDOException $e) {
    flash('danger', 'Cannot delete supplier: products are still assigned to it.');
}

redirect('/suppliers/index.php');
