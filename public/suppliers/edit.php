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

$pageTitle = 'Edit Supplier - Sari-Sari POS';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Edit Supplier</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" class="row g-3 bg-white p-4 rounded shadow-sm" style="max-width: 600px;">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="col-12">
        <label class="form-label">Supplier Name</label>
        <input type="text" name="name" class="form-control" value="<?= old($input, 'name') ?>" required>
    </div>
    <div class="col-12">
        <label class="form-label">Contact Person</label>
        <input type="text" name="contact_person" class="form-control" value="<?= old($input, 'contact_person') ?>">
    </div>
    <div class="col-12">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="<?= old($input, 'phone') ?>">
    </div>
    <div class="col-12">
        <label class="form-label">Address</label>
        <input type="text" name="address" class="form-control" value="<?= old($input, 'address') ?>">
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="/suppliers/index.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>
