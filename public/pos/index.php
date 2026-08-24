<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Product;

$products = (new Product())->search('', null, 'name', 'ASC', 200, 0);

$pageTitle = 'New Sale - Sari-Sari POS';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">New Sale</h1>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">Available Products</div>
            <div class="card-body" style="max-height: 520px; overflow-y: auto;">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Name</th><th>Price</th><th>Stock</th><th>Qty</th><th></th></tr></thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="5" class="text-center text-muted">No products available. Add products first.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td>&#8369;<?= number_format((float) $p['selling_price'], 2) ?></td>
                                <td><?= (int) $p['stock_quantity'] ?></td>
                                <td style="width:80px">
                                    <input type="number" min="1" max="<?= (int) $p['stock_quantity'] ?>" value="1"
                                           class="form-control form-control-sm" id="qty-<?= $p['id'] ?>"
                                           <?= (int) $p['stock_quantity'] < 1 ? 'disabled' : '' ?>>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary"
                                        <?= (int) $p['stock_quantity'] < 1 ? 'disabled' : '' ?>
                                        onclick="addToCart(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>', <?= (float) $p['selling_price'] ?>, <?= (int) $p['stock_quantity'] ?>)">
                                        Add
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Cart</div>
            <div class="card-body">
                <table class="table table-sm" id="cartTable">
                    <thead><tr><th>Item</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
                    <tbody id="cartBody"></tbody>
                </table>
                <h5 class="text-end">Total: &#8369;<span id="cartTotal">0.00</span></h5>

                <form method="post" action="<?= BASE_URL ?>/pos/checkout.php" id="checkoutForm">
                    <div id="hiddenItems"></div>
                    <div class="mb-2">
                        <label class="form-label">Amount Paid (&#8369;)</label>
                        <input type="number" step="0.01" min="0" name="amount_paid" id="amountPaid" class="form-control" required>
                    </div>
                    <p>Change: &#8369;<span id="changeDue">0.00</span></p>
                    <button type="submit" class="btn btn-success w-100" id="checkoutBtn" disabled>Complete Sale</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const cart = {};

function addToCart(id, name, price, stock) {
    const qtyInput = document.getElementById('qty-' + id);
    const qty = parseInt(qtyInput.value, 10) || 1;

    if (qty < 1 || qty > stock) {
        alert('Invalid quantity. Available stock: ' + stock);
        return;
    }

    const existingQty = cart[id] ? cart[id].qty : 0;
    if (existingQty + qty > stock) {
        alert('Not enough stock. Available: ' + stock + ', already in cart: ' + existingQty);
        return;
    }

    cart[id] = { name: name, price: price, qty: existingQty + qty, stock: stock };
    renderCart();
}

function removeFromCart(id) {
    delete cart[id];
    renderCart();
}

function renderCart() {
    const body = document.getElementById('cartBody');
    const hiddenItems = document.getElementById('hiddenItems');
    body.innerHTML = '';
    hiddenItems.innerHTML = '';
    let total = 0;
    let index = 0;

    for (const id in cart) {
        const item = cart[id];
        const subtotal = item.price * item.qty;
        total += subtotal;

        const row = document.createElement('tr');
        row.innerHTML = '<td></td><td></td><td></td><td><button type="button" class="btn btn-sm btn-outline-danger">x</button></td>';
        row.children[0].textContent = item.name;
        row.children[1].textContent = item.qty;
        row.children[2].textContent = '₱' + subtotal.toFixed(2);
        row.children[3].querySelector('button').addEventListener('click', () => removeFromCart(id));
        body.appendChild(row);

        hiddenItems.innerHTML += `
            <input type="hidden" name="items[${index}][product_id]" value="${id}">
            <input type="hidden" name="items[${index}][quantity]" value="${item.qty}">
            <input type="hidden" name="items[${index}][unit_price]" value="${item.price}">
        `;
        index++;
    }

    document.getElementById('cartTotal').textContent = total.toFixed(2);
    document.getElementById('checkoutBtn').disabled = (index === 0);
    updateChange();
}

function updateChange() {
    const total = parseFloat(document.getElementById('cartTotal').textContent) || 0;
    const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const change = paid - total;
    document.getElementById('changeDue').textContent = change >= 0 ? change.toFixed(2) : '0.00';
}

document.getElementById('amountPaid').addEventListener('input', updateChange);

document.getElementById('checkoutForm').addEventListener('submit', function (e) {
    const total = parseFloat(document.getElementById('cartTotal').textContent) || 0;
    const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    if (paid < total) {
        e.preventDefault();
        alert('Amount paid is less than the total.');
    }
});
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
