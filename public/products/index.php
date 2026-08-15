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

$pageTitle = 'Products - Sari-Sari POS';
require __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Products</h1>
    <a href="/products/create.php" class="btn btn-primary">+ Add Product</a>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Search by name..." value="<?= htmlspecialchars($keyword) ?>">
    </div>
    <div class="col-md-3">
        <select name="category_id" class="form-select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $categoryId === (int) $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="sort" class="form-select">
            <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Sort: Name</option>
            <option value="selling_price" <?= $sortBy === 'selling_price' ? 'selected' : '' ?>>Sort: Price</option>
            <option value="stock_quantity" <?= $sortBy === 'stock_quantity' ? 'selected' : '' ?>>Sort: Stock</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100" type="submit">Filter</button>
    </div>
</form>

<table class="table table-striped table-bordered bg-white">
    <thead>
        <tr>
            <th>SKU</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($products)): ?>
            <tr><td colspan="6" class="text-center text-muted">No products found.</td></tr>
        <?php endif; ?>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['sku']) ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['category_name'] ?? '-') ?></td>
                <td>&#8369;<?= number_format((float) $product['selling_price'], 2) ?></td>
                <td>
                    <?= (int) $product['stock_quantity'] ?>
                    <?php if ((int) $product['stock_quantity'] <= (int) $product['reorder_level']): ?>
                        <span class="badge bg-danger">Low</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/products/edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="/products/delete.php" method="post" class="d-inline" onsubmit="return confirm('Delete this product?');">
                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($totalPages > 1): ?>
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?q=<?= urlencode($keyword) ?>&category_id=<?= (int) $categoryId ?>&sort=<?= htmlspecialchars($sortBy) ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
