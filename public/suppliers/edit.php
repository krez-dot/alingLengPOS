<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Supplier;

$supplierModel = new Supplier();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$supplier = $supplierModel->find($id);

if (!$supplier) {
    flash('danger', 'Supplier not found.');
    redirect('/suppliers/index.php');
}

$errors = [];
$input = $supplier;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;
    if (trim($input['name'] ?? '') === '') {
        $errors[] = 'Supplier name is required.';
    }

    if (empty($errors)) {
        $supplierModel->update($id, [
            'name' => trim($input['name']),
            'contact_person' => trim($input['contact_person'] ?? ''),
            'phone' => trim($input['phone'] ?? ''),
            'address' => trim($input['address'] ?? ''),
        ]);
        flash('success', 'Supplier updated.');
        redirect('/suppliers/index.php');
    }
}

$activeNav = 'suppliers';
$pageTitle = 'Edit Supplier - QuickTally';
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Edit Supplier</h1>
    <p>Keep supplier contact details handy for reordering stock.</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card form-card">
    <form method="post">
        <input type="hidden" name="id" value="<?= $id ?>">
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
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= BASE_URL ?>/suppliers/index.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
