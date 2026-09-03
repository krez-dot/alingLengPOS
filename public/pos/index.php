<?php

require __DIR__ . '/../../bootstrap.php';

use App\Models\Category;
use App\Models\Product;

$products = (new Product())->search('', null, 'name', 'ASC', 200, 0);
$categories = (new Category())->all('name');

$activeNav = 'pos';
$pageTitle = 'Checkout - QuickTally';
require __DIR__ . '/../includes/header.php';
?>

<div class="pos-layout">
    <div class="pos-main">
        <div class="search-input-wrap" style="margin-bottom:16px;">
            <span class="search-icon"><?= navIcon('search') ?></span>
            <input type="text" id="posSearch" class="search-input" placeholder="Search products or SKU...">
        </div>
        <div class="pill-group" id="posCategoryPills" style="margin-bottom:20px;">
            <button type="button" class="pill active" data-category="all">All</button>
            <?php foreach ($categories as $cat): ?>
                <button type="button" class="pill" data-category="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></button>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <p class="empty-text">No products available. Add products first.</p>
        <?php else: ?>
            <div class="product-grid" id="posProductGrid">
                <?php foreach ($products as $p): ?>
                    <button type="button" class="product-card"
                        data-id="<?= $p['id'] ?>"
                        data-name="<?= htmlspecialchars($p['name']) ?>"
                        data-price="<?= (float) $p['selling_price'] ?>"
                        data-stock="<?= (int) $p['stock_quantity'] ?>"
                        data-category="<?= htmlspecialchars($p['category_name'] ?? '') ?>"
                        data-search="<?= htmlspecialchars(strtolower($p['name'] . ' ' . $p['sku'])) ?>"
                        <?= (int) $p['stock_quantity'] < 1 ? 'disabled' : '' ?>>
                        <span class="badge-pill <?= categoryBadgeClass($p['category_name'] ?? null) ?>"><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></span>
                        <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="product-price">&#8369;<?= number_format((float) $p['selling_price'], 2) ?></div>
                        <div class="product-stock"><?= (int) $p['stock_quantity'] ?> in stock</div>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <aside class="order-panel">
        <div class="order-title">Order</div>
        <div class="order-empty" id="orderEmpty">
            <?= navIcon('cart') ?>
            <strong>Cart is empty</strong>
            <span>Tap a product to add it</span>
        </div>
        <div class="order-items" id="cartBody"></div>

        <form method="post" action="<?= BASE_URL ?>/pos/checkout.php" id="checkoutForm">
            <div id="hiddenItems"></div>
            <div class="order-summary">
                <div class="order-total-row"><span>Subtotal</span><span>&#8369;<span id="cartTotal">0.00</span></span></div>
                <div class="field">
                    <label>Cash Tendered</label>
                    <input type="number" step="0.01" min="0" name="amount_paid" id="amountPaid" placeholder="0.00">
                </div>
                <div class="order-row"><span>Change</span><span>&#8369;<span id="changeDue">0.00</span></span></div>
                <button type="submit" class="btn btn-primary btn-block" id="checkoutBtn" disabled>Add items to checkout</button>
            </div>
        </form>
    </aside>
</div>

<script>
var cart = {};
var grid = document.getElementById('posProductGrid');
var searchInput = document.getElementById('posSearch');
var pills = document.querySelectorAll('#posCategoryPills .pill');
var activeCategory = 'all';

function applyFilters() {
    if (!grid) return;
    var term = searchInput.value.trim().toLowerCase();
    grid.querySelectorAll('.product-card').forEach(function (card) {
        var matchesCategory = activeCategory === 'all' || card.dataset.category === activeCategory;
        var matchesSearch = term === '' || card.dataset.search.indexOf(term) !== -1;
        card.style.display = (matchesCategory && matchesSearch) ? '' : 'none';
    });
}

pills.forEach(function (pill) {
    pill.addEventListener('click', function () {
        pills.forEach(function (p) { p.classList.remove('active'); });
        pill.classList.add('active');
        activeCategory = pill.dataset.category;
        applyFilters();
    });
});
searchInput.addEventListener('input', applyFilters);

if (grid) {
    grid.querySelectorAll('.product-card').forEach(function (card) {
        card.addEventListener('click', function () {
            var id = card.dataset.id;
            var stock = parseInt(card.dataset.stock, 10);
            var existing = cart[id] ? cart[id].qty : 0;
            if (existing + 1 > stock) {
                alert('Not enough stock. Available: ' + stock);
                return;
            }
            cart[id] = { name: card.dataset.name, price: parseFloat(card.dataset.price), qty: existing + 1, stock: stock };
            renderCart();
        });
    });
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    var next = cart[id].qty + delta;
    if (next < 1) {
        delete cart[id];
    } else if (next > cart[id].stock) {
        alert('Not enough stock. Available: ' + cart[id].stock);
        return;
    } else {
        cart[id].qty = next;
    }
    renderCart();
}

function renderCart() {
    var body = document.getElementById('cartBody');
    var empty = document.getElementById('orderEmpty');
    var hiddenItems = document.getElementById('hiddenItems');
    var ids = Object.keys(cart);
    body.innerHTML = '';
    hiddenItems.innerHTML = '';
    var total = 0;

    body.style.display = ids.length === 0 ? 'none' : 'flex';
    empty.style.display = ids.length === 0 ? 'flex' : 'none';

    ids.forEach(function (id, index) {
        var item = cart[id];
        var subtotal = item.price * item.qty;
        total += subtotal;

        var row = document.createElement('div');
        row.className = 'order-item';
        row.innerHTML =
            '<div>' +
                '<div class="order-item-name"></div>' +
                '<div class="order-item-meta"></div>' +
            '</div>' +
            '<div class="order-item-actions">' +
                '<button type="button" class="qty-btn" data-action="dec">&minus;</button>' +
                '<button type="button" class="qty-btn" data-action="inc">+</button>' +
                '<button type="button" class="order-item-remove" data-action="remove">&#10005;</button>' +
            '</div>';
        row.querySelector('.order-item-name').textContent = item.name;
        row.querySelector('.order-item-meta').textContent = item.qty + ' × ₱' + item.price.toFixed(2) + ' = ₱' + subtotal.toFixed(2);
        row.querySelector('[data-action="dec"]').addEventListener('click', function (e) { e.stopPropagation(); changeQty(id, -1); });
        row.querySelector('[data-action="inc"]').addEventListener('click', function (e) { e.stopPropagation(); changeQty(id, 1); });
        row.querySelector('[data-action="remove"]').addEventListener('click', function (e) { e.stopPropagation(); delete cart[id]; renderCart(); });
        body.appendChild(row);

        hiddenItems.innerHTML +=
            '<input type="hidden" name="items[' + index + '][product_id]" value="' + id + '">' +
            '<input type="hidden" name="items[' + index + '][quantity]" value="' + item.qty + '">' +
            '<input type="hidden" name="items[' + index + '][unit_price]" value="' + item.price + '">';
    });

    document.getElementById('cartTotal').textContent = total.toFixed(2);
    document.getElementById('checkoutBtn').disabled = (ids.length === 0);
    updateChange();
}

function updateChange() {
    var total = parseFloat(document.getElementById('cartTotal').textContent) || 0;
    var paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    var change = paid - total;
    document.getElementById('changeDue').textContent = change >= 0 ? change.toFixed(2) : '0.00';
}

document.getElementById('amountPaid').addEventListener('input', updateChange);

document.getElementById('checkoutForm').addEventListener('submit', function (e) {
    var total = parseFloat(document.getElementById('cartTotal').textContent) || 0;
    var paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    if (paid < total) {
        e.preventDefault();
        alert('Cash tendered is less than the total.');
    }
});

renderCart();
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
