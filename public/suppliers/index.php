<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Supplier;

$keyword = trim($_GET['q'] ?? '');
$suppliers = (new Supplier())->search($keyword);

$activeNav = 'suppliers';
$pageTitle = "Suppliers - Aling Leng's Sari-Sari Store";
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header page-header-row">
    <div>
        <h1>Suppliers</h1>
        <p>Track who supplies your stock and how to reach them.</p>
    </div>
    <a href="<?= BASE_URL ?>/suppliers/create.php" class="btn btn-primary">+ Add Supplier</a>
</div>

<form method="get" class="toolbar">
    <div class="search-input-wrap">
        <span class="search-icon"><?= navIcon('search') ?></span>
        <input type="text" name="q" class="search-input" placeholder="Search name or contact person..." value="<?= htmlspecialchars($keyword) ?>">
    </div>
</form>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Contact Person</th><th>Phone</th><th>Address</th><th>Actions</th></tr></thead>
        <tbody>
            <?php if (empty($suppliers)): ?>
                <tr class="empty-row"><td colspan="5">No suppliers found.</td></tr>
            <?php endif; ?>
            <?php foreach ($suppliers as $sup): ?>
                <tr>
                    <td class="strong"><?= htmlspecialchars($sup['name']) ?></td>
                    <td><?= htmlspecialchars($sup['contact_person'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($sup['phone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($sup['address'] ?? '-') ?></td>
                    <td class="actions-cell">
                        <a href="<?= BASE_URL ?>/suppliers/edit.php?id=<?= $sup['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                        <form action="<?= BASE_URL ?>/suppliers/delete.php" method="post" class="inline-form" onsubmit="return confirm('Delete this supplier?');">
                            <input type="hidden" name="id" value="<?= $sup['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
