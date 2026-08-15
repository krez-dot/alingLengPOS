<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Category;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    redirect('/categories/index.php');
}

try {
    (new Category())->delete((int) $_POST['id']);
    flash('success', 'Category deleted.');
} catch (\PDOException $e) {
    flash('danger', 'Cannot delete category: products are still assigned to it.');
}

redirect('/categories/index.php');
