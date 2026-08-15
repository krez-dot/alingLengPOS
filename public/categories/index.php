<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Category;

$categories = (new Category())->all('name');

$pageTitle = 'Categories - Sari-Sari POS';
require __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Categories</h1>
    <a href="/categories/create.php" class="btn btn-primary">+ Add Category</a>
</div>

<table class="table table-striped bg-white">
    <thead><tr><th>Name</th><th>Created</th><th>Actions</th></tr></thead>
    <tbody>
        <?php if (empty($categories)): ?>
            <tr><td colspan="3" class="text-center text-muted">No categories found.</td></tr>
        <?php endif; ?>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= htmlspecialchars($cat['name']) ?></td>
                <td><?= htmlspecialchars($cat['created_at']) ?></td>
                <td>
                    <a href="/categories/edit.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="/categories/delete.php" method="post" class="d-inline" onsubmit="return confirm('Delete this category?');">
                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../includes/footer.php'; ?>
