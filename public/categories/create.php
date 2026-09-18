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
            (new Category())->create([
                'name' => trim($input['name']),
                'color' => $input['color'] ?? '',
            ]);
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

<div class="form-with-preview">
    <div class="card form-card">
        <form method="post" id="categoryForm">
            <div class="field">
                <label>Category Name</label>
                <input type="text" name="name" id="categoryNameInput" value="<?= old($input, 'name') ?>" required>
            </div>
            <div class="field">
                <label>Badge Color</label>
                <?= colorSwatchPicker($input['color'] ?? '') ?>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="<?= BASE_URL ?>/categories/index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>

    <div class="card preview-card">
        <div class="card-header-row"><strong>Preview</strong></div>
        <div class="preview-body">
            <p class="empty-text">This is how it'll look on Products and Checkout.</p>
            <span class="badge-pill badge-default" id="colorPreviewBadge">Category Name</span>
            <p class="empty-text" id="colorPreviewNote">Auto-assigned once saved.</p>
        </div>
    </div>
</div>

<script>
(function () {
    var nameInput = document.getElementById('categoryNameInput');
    var colorRadios = document.querySelectorAll('input[name="color"]');
    var badge = document.getElementById('colorPreviewBadge');
    var note = document.getElementById('colorPreviewNote');

    function update() {
        badge.textContent = nameInput.value.trim() || 'Category Name';

        var checked = document.querySelector('input[name="color"]:checked');
        var color = checked ? checked.value : '';

        badge.className = 'badge-pill ' + (color ? 'badge-' + color : 'badge-default');
        note.style.display = color ? 'none' : 'block';
    }

    nameInput.addEventListener('input', update);
    colorRadios.forEach(function (radio) { radio.addEventListener('change', update); });
    update();
})();
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
