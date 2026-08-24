<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;

$errors = [];
$input = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;

    if (trim($input['sku'] ?? '') === '') {
        $errors[] = 'SKU is required.';
    }
    if (trim($input['name'] ?? '') === '') {
        $errors[] = 'Product name is required.';
    }
    if (empty($input['category_id'])) {
        $errors[] = 'Please select a category.';
    }
    if (!is_numeric($input['selling_price'] ?? '') || (float) $input['selling_price'] < 0) {
        $errors[] = 'Selling price must be a valid non-negative number.';
    }
    if (!is_numeric($input['cost_price'] ?? '') || (float) $input['cost_price'] < 0) {
        $errors[] = 'Cost price must be a valid non-negative number.';
    }
    if (!ctype_digit((string) ($input['stock_quantity'] ?? ''))) {
        $errors[] = 'Stock quantity must be a whole number.';
    }

    if (empty($errors)) {
        try {
            $productModel = new Product();
            $productModel->create([
                'sku' => trim($input['sku']),
                'name' => trim($input['name']),
                'category_id' => (int) $input['category_id'],
                'supplier_id' => $input['supplier_id'] !== '' ? (int) $input['supplier_id'] : null,
                'cost_price' => (float) $input['cost_price'],
                'selling_price' => (float) $input['selling_price'],
                'stock_quantity' => (int) $input['stock_quantity'],
                'reorder_level' => (int) ($input['reorder_level'] ?? 10),
            ]);
            flash('success', 'Product added successfully.');
            redirect('/products/index.php');
        } catch (\PDOException $e) {
            $errors[] = str_contains($e->getMessage(), 'Duplicate')
                ? 'A product with that SKU already exists.'
                : 'Failed to save product. Please try again.';
        }
    }
}

$categories = (new Category())->all('name');
$suppliers = (new Supplier())->all('name');

$pageTitle = 'Add Product - Sari-Sari POS';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Add Product</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" class="row g-3 bg-white p-4 rounded shadow-sm">
    <div class="col-md-6">
        <label class="form-label">SKU</label>
        <input type="text" name="sku" class="form-control" value="<?= old($input, 'sku') ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" value="<?= old($input, 'name') ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (($input['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Supplier (optional)</label>
        <select name="supplier_id" class="form-select">
            <option value="">-- None --</option>
            <?php foreach ($suppliers as $sup): ?>
                <option value="<?= $sup['id'] ?>" <?= (($input['supplier_id'] ?? '') == $sup['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sup['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Cost Price (&#8369;)</label>
        <input type="number" step="0.01" min="0" name="cost_price" class="form-control" value="<?= old($input, 'cost_price', '0') ?>" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Selling Price (&#8369;)</label>
        <input type="number" step="0.01" min="0" name="selling_price" class="form-control" value="<?= old($input, 'selling_price', '0') ?>" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Initial Stock</label>
        <input type="number" min="0" name="stock_quantity" class="form-control" value="<?= old($input, 'stock_quantity', '0') ?>" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Reorder Level</label>
        <input type="number" min="0" name="reorder_level" class="form-control" value="<?= old($input, 'reorder_level', '10') ?>" required>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Save Product</button>
        <a href="<?= BASE_URL ?>/products/index.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>
