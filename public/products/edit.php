<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;

$productModel = new Product();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$product = $productModel->find($id);

if (!$product) {
    flash('danger', 'Product not found.');
    redirect('/products/index.php');
}

$errors = [];
$input = $product;

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
        $errors[] = 'Unit price must be a valid non-negative number.';
    }
    if (!is_numeric($input['cost_price'] ?? '') || (float) $input['cost_price'] < 0) {
        $errors[] = 'Cost price must be a valid non-negative number.';
    }
    if (!ctype_digit((string) ($input['stock_quantity'] ?? ''))) {
        $errors[] = 'Stock quantity must be a whole number.';
    }

    if (empty($errors)) {
        try {
            $productModel->update($id, [
                'sku' => trim($input['sku']),
                'name' => trim($input['name']),
                'category_id' => (int) $input['category_id'],
                'supplier_id' => $input['supplier_id'] !== '' ? (int) $input['supplier_id'] : null,
                'cost_price' => (float) $input['cost_price'],
                'selling_price' => (float) $input['selling_price'],
                'stock_quantity' => (int) $input['stock_quantity'],
                'reorder_level' => (int) ($input['reorder_level'] ?? 10),
            ]);
            flash('success', 'Product updated successfully.');
            redirect('/products/index.php');
        } catch (\PDOException $e) {
            $errors[] = str_contains($e->getMessage(), 'Duplicate')
                ? 'A product with that SKU already exists.'
                : 'Failed to update product. Please try again.';
        }
    }
}

$categories = (new Category())->all('name');
$suppliers = (new Supplier())->all('name');

$activeNav = 'products';
$pageTitle = 'Edit Product - QuickTally';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Edit Product</h1>
    <p>Product details are visible to cashiers on the checkout screen.</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card form-card">
    <form method="post">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="field">
            <label>SKU</label>
            <input type="text" name="sku" value="<?= old($input, 'sku') ?>" required>
        </div>
        <div class="field">
            <label>Product Name</label>
            <input type="text" name="name" value="<?= old($input, 'name') ?>" required>
        </div>
        <div class="field-row">
            <div class="field">
                <label>Category</label>
                <select name="category_id" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (($input['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label>Supplier (optional)</label>
                <select name="supplier_id">
                    <option value="">-- None --</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['id'] ?>" <?= (($input['supplier_id'] ?? '') == $sup['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sup['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="field-row">
            <div class="field">
                <label>Cost Price (&#8369;)</label>
                <input type="number" step="0.01" min="0" name="cost_price" value="<?= old($input, 'cost_price', '0') ?>" required>
            </div>
            <div class="field">
                <label>Unit Price (&#8369;)</label>
                <input type="number" step="0.01" min="0" name="selling_price" value="<?= old($input, 'selling_price', '0') ?>" required>
            </div>
        </div>
        <div class="field-row">
            <div class="field">
                <label>Stock Quantity</label>
                <input type="number" min="0" name="stock_quantity" value="<?= old($input, 'stock_quantity', '0') ?>" required>
            </div>
            <div class="field">
                <label>Reorder Level</label>
                <input type="number" min="0" name="reorder_level" value="<?= old($input, 'reorder_level', '10') ?>" required>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="<?= BASE_URL ?>/products/index.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
