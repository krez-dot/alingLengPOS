<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('theme');
                if (saved === 'dark' || saved === 'light') {
                    document.documentElement.setAttribute('data-theme', saved);
                }
            } catch (e) {}
        })();
    </script>
    <title><?= htmlspecialchars($pageTitle ?? "Aling Leng's Sari-Sari Store") ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="app-shell">
    <?php require __DIR__ . '/nav.php'; ?>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    <div class="main-content">
        <div class="mobile-topbar">
            <button type="button" class="icon-btn" id="sidebarToggle" aria-label="Toggle menu"><?= navIcon('menu') ?></button>
            <span class="brand-name">Aling Leng's</span>
        </div>
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?>">
                <span><?= htmlspecialchars($_SESSION['flash']['message']) ?></span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>