<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Category;

$categories = (new Category())->all('name');

$activeNav = 'categories';
$pageTitle = "Categories - Aling Leng's Sari-Sari Store";
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header page-header-row">
    <div>
        <h1>Categories</h1>
        <p>Group products so cashiers can filter the checkout screen quickly.</p>
    </div>
    <a href="<?= BASE_URL ?>/categories/create.php" class="btn btn-primary">+ Add Category</a>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr class="empty-row"><td colspan="3">No categories found.</td></tr>
            <?php endif; ?>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><span class="badge-pill <?= categoryBadgeClass($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></span></td>
                    <td class="muted"><?= htmlspecialchars($cat['created_at']) ?></td>
                    <td class="actions-cell">
                        <a href="<?= BASE_URL ?>/categories/edit.php?id=<?= $cat['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                        <form action="<?= BASE_URL ?>/categories/delete.php" method="post" class="inline-form" onsubmit="return confirm('Delete this category?');">
                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
