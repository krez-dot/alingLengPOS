<?php
$navItems = [
    ['key' => 'dashboard', 'label' => 'Overview', 'href' => '/index.php', 'icon' => 'grid'],
    ['key' => 'pos', 'label' => 'Checkout', 'href' => '/pos/index.php', 'icon' => 'bag', 'badge' => 'POS'],
    ['key' => 'products', 'label' => 'Products', 'href' => '/products/index.php', 'icon' => 'hexagon'],
    ['key' => 'categories', 'label' => 'Categories', 'href' => '/categories/index.php', 'icon' => 'tag'],
    ['key' => 'suppliers', 'label' => 'Suppliers', 'href' => '/suppliers/index.php', 'icon' => 'truck'],
    ['key' => 'sales', 'label' => 'History', 'href' => '/sales/index.php', 'icon' => 'file'],
];
$active = $activeNav ?? '';
?>
<aside class="sidebar" id="appSidebar">
    <a href="<?= BASE_URL ?>/index.php" class="brand">
        <span class="brand-icon"><?= navIcon('bag') ?></span>
        <span class="brand-text">
            <span class="brand-name">QuickTally</span>
            <span class="brand-sub">Grocery POS</span>
        </span>
    </a>
    <nav>
        <ul class="nav-list">
            <?php foreach ($navItems as $item): ?>
                <li>
                    <a href="<?= BASE_URL . $item['href'] ?>" class="nav-link <?= $active === $item['key'] ? 'active' : '' ?>">
                        <?= navIcon($item['icon']) ?>
                        <span><?= htmlspecialchars($item['label']) ?></span>
                        <?php if (!empty($item['badge'])): ?><span class="nav-badge"><?= htmlspecialchars($item['badge']) ?></span><?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <div class="user-card">
            <span class="user-avatar">AU</span>
            <span class="user-info">
                <span class="user-name">Admin User</span>
                <span class="user-role">Admin</span>
            </span>
            <a href="#" class="user-logout" title="Login/logout arrives with Final Term auth">Log out</a>
        </div>
    </div>
</aside>
