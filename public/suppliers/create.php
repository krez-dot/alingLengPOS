<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Supplier;

$errors = [];
$input = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;
    if (trim($input['name'] ?? '') === '') {
        $errors[] = 'Supplier name is required.';
    }

    if (empty($errors)) {
        (new Supplier())->create([
            'name' => trim($input['name']),
            'contact_person' => trim($input['contact_person'] ?? ''),
            'phone' => trim($input['phone'] ?? ''),
            'address' => trim($input['address'] ?? ''),
        ]);
        flash('success', 'Supplier added.');
        redirect('/suppliers/index.php');
    }
}

$activeNav = 'suppliers';
$pageTitle = "Add Supplier - Aling Leng's Sari-Sari Store";
require __DIR__ . '/../includes/header.php';
?>

<div class="page-centered">
<div class="page-header">
    <h1>Add Supplier</h1>
    <p>Keep supplier contact details handy for reordering stock.</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card form-card">
    <form method="post">
        <div class="field">
            <label>Supplier Name</label>
            <input type="text" name="name" value="<?= old($input, 'name') ?>" required>
        </div>
        <div class="field">
            <label>Contact Person</label>
            <input type="text" name="contact_person" value="<?= old($input, 'contact_person') ?>">
        </div>
        <div class="field">
            <label>Phone</label>
            <input type="text" name="phone" value="<?= old($input, 'phone') ?>">
        </div>
        <div class="field">
            <label>Address</label>
            <input type="text" name="address" value="<?= old($input, 'address') ?>">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="<?= BASE_URL ?>/suppliers/index.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
