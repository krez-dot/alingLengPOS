<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Category;
use App\Models\Product;

$productModel = new Product();
$categoryModel = new Category();

$keyword = trim($_GET['q'] ?? '');
$categoryId = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int) $_GET['category_id'] : null;
$sortBy = $_GET['sort'] ?? 'name';
$direction = $_GET['dir'] ?? 'ASC';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$products = $productModel->search($keyword, $categoryId, $sortBy, $direction, $perPage, $offset);
$totalProducts = $productModel->countSearch($keyword, $categoryId);
$totalPages = (int) ceil($totalProducts / $perPage);
$categories = $categoryModel->all('name');

$activeNav = 'products';
$pageTitle = "Products - Aling Leng's Sari-Sari Store";
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header page-header-row">
    <div>
        <h1>Products</h1>
        <p>Manage your catalog &mdash; add, edit, or remove products.</p>
    </div>
    <a href="<?= BASE_URL ?>/products/create.php" class="btn btn-primary">+ Add Product</a>
</div>

<form method="get" class="toolbar">
    <div class="search-input-wrap">
        <span class="search-icon"><?= navIcon('search') ?></span>
        <input type="text" name="q" class="search-input" placeholder="Search name or SKU..." value="<?= htmlspecialchars($keyword) ?>">
    </div>
    <div class="pill-group">
        <button type="submit" name="category_id" value="" class="pill <?= $categoryId === null ? 'active' : '' ?>">All</button>
        <?php foreach ($categories as $cat): ?>
            <button type="submit" name="category_id" value="<?= $cat['id'] ?>" class="pill <?= $categoryId === (int) $cat['id'] ? 'active' : '' ?>"><?= htmlspecialchars($cat['name']) ?></button>
        <?php endforeach; ?>
    </div>
    <select name="sort" class="sort-select" onchange="this.form.submit()">
        <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Sort: Name</option>
        <option value="selling_price" <?= $sortBy === 'selling_price' ? 'selected' : '' ?>>Sort: Price</option>
        <option value="stock_quantity" <?= $sortBy === 'stock_quantity' ? 'selected' : '' ?>>Sort: Stock</option>
    </select>
    <button type="submit" name="dir" value="<?= $direction === 'ASC' ? 'DESC' : 'ASC' ?>" class="btn btn-outline btn-sm" title="Toggle sort direction">
        <?= $direction === 'ASC' ? '&uarr; Asc' : '&darr; Desc' ?>
    </button>
</form>

<?= categoryLegend($categories) ?>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr><th>SKU</th><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr class="empty-row"><td colspan="6">No products found.</td></tr>
            <?php endif; ?>
            <?php foreach ($products as $product): $isLow = (int) $product['stock_quantity'] <= (int) $product['reorder_level']; ?>
                <tr>
                    <td class="muted"><?= htmlspecialchars($product['sku']) ?></td>
                    <td class="strong"><?= htmlspecialchars($product['name']) ?></td>
                    <td><span class="badge-pill <?= categoryBadgeClass($product['category_name'] ?? null, $product['category_color'] ?? null) ?>"><?= htmlspecialchars($product['category_name'] ?? '-') ?></span></td>
                    <td class="price-cell">&#8369;<?= number_format((float) $product['selling_price'], 2) ?></td>
                    <td>
                        <?= (int) $product['stock_quantity'] ?> units
                        <span class="badge-pill <?= $isLow ? 'badge-low' : 'badge-ok' ?>"><?= $isLow ? 'Low' : 'OK' ?></span>
                    </td>
                    <td class="actions-cell">
                        <button type="button" class="btn btn-outline btn-sm restock-btn" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>">Restock</button>
                        <a href="<?= BASE_URL ?>/products/edit.php?id=<?= $product['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                        <form action="<?= BASE_URL ?>/products/delete.php" method="post" class="inline-form" onsubmit="return confirm('Delete this product?');">
                            <input type="hidden" name="id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="<?= $i === $page ? 'active' : '' ?>">
                <a href="?q=<?= urlencode($keyword) ?>&category_id=<?= (int) $categoryId ?>&sort=<?= htmlspecialchars($sortBy) ?>&dir=<?= htmlspecialchars($direction) ?>&page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
<?php endif; ?>

<div class="modal-overlay" id="restockModal">
    <div class="modal-box">
        <h3>Restock Product</h3>
        <p class="modal-sub" id="restockProductName">Add stock for this product.</p>
        <form method="post" action="<?= BASE_URL ?>/products/restock.php">
            <input type="hidden" name="id" id="restockProductId">
            <div class="field">
                <label>Quantity to Add</label>
                <input type="number" name="quantity" id="restockQuantity" min="1" value="10" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline btn-block" id="restockCancel">Cancel</button>
                <button type="submit" class="btn btn-primary btn-block">Add Stock</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('restockModal');
    var nameLabel = document.getElementById('restockProductName');
    var idInput = document.getElementById('restockProductId');
    var qtyInput = document.getElementById('restockQuantity');

    document.querySelectorAll('.restock-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            idInput.value = btn.dataset.id;
            nameLabel.textContent = 'Add stock for "' + btn.dataset.name + '".';
            qtyInput.value = 10;
            modal.classList.add('open');
            qtyInput.focus();
        });
    });

    document.getElementById('restockCancel').addEventListener('click', function () {
        modal.classList.remove('open');
    });
})();
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
