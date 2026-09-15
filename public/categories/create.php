<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Category;

$errors = [];
$input = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;
    if (trim($input['name'] ?? '') === '') {
        $errors[] = 'Category name is required.';
    }

    if (empty($errors)) {
        try {
            (new Category())->create(['name' => trim($input['name'])]);
            flash('success', 'Category added.');
            redirect('/categories/index.php');
        } catch (\PDOException $e) {
            $errors[] = 'A category with that name already exists.';
        }
    }
}

$activeNav = 'categories';
$pageTitle = "Add Category - Aling Leng's Sari-Sari Store";
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Add Category</h1>
    <p>Categories help organize the product catalog and checkout filters.</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card form-card">
    <form method="post">
        <div class="field">
            <label>Category Name</label>
            <input type="text" name="name" value="<?= old($input, 'name') ?>" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="<?= BASE_URL ?>/categories/index.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
