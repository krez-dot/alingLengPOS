<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Supplier;

$suppliers = (new Supplier())->all('name');

$pageTitle = 'Suppliers - Sari-Sari POS';
require __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Suppliers</h1>
    <a href="<?= BASE_URL ?>/suppliers/create.php" class="btn btn-primary">+ Add Supplier</a>
</div>

<table class="table table-striped bg-white">
    <thead><tr><th>Name</th><th>Contact Person</th><th>Phone</th><th>Address</th><th>Actions</th></tr></thead>
    <tbody>
        <?php if (empty($suppliers)): ?>
            <tr><td colspan="5" class="text-center text-muted">No suppliers found.</td></tr>
        <?php endif; ?>
        <?php foreach ($suppliers as $sup): ?>
            <tr>
                <td><?= htmlspecialchars($sup['name']) ?></td>
                <td><?= htmlspecialchars($sup['contact_person'] ?? '-') ?></td>
                <td><?= htmlspecialchars($sup['phone'] ?? '-') ?></td>
                <td><?= htmlspecialchars($sup['address'] ?? '-') ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/suppliers/edit.php?id=<?= $sup['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="<?= BASE_URL ?>/suppliers/delete.php" method="post" class="d-inline" onsubmit="return confirm('Delete this supplier?');">
                        <input type="hidden" name="id" value="<?= $sup['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../includes/footer.php'; ?>
