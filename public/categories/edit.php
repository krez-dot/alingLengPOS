<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Category;

$categoryModel = new Category();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$category = $categoryModel->find($id);

if (!$category) {
    flash('danger', 'Category not found.');
    redirect('/categories/index.php');
}

$errors = [];
$input = $category;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;
    if (trim($input['name'] ?? '') === '') {
        $errors[] = 'Category name is required.';
    }

    if (empty($errors)) {
        try {
            $categoryModel->update($id, ['name' => trim($input['name'])]);
            flash('success', 'Category updated.');
            redirect('/categories/index.php');
        } catch (\PDOException $e) {
            $errors[] = 'A category with that name already exists.';
        }
    }
}

$pageTitle = 'Edit Category - Sari-Sari POS';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Edit Category</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" class="row g-3 bg-white p-4 rounded shadow-sm" style="max-width: 500px;">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="col-12">
        <label class="form-label">Category Name</label>
        <input type="text" name="name" class="form-control" value="<?= old($input, 'name') ?>" required>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="<?= BASE_URL ?>/categories/index.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>
