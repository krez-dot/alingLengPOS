    </div>
</div>
<script>
(function () {
    var toggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('appSidebar');
    var backdrop = document.getElementById('sidebarBackdrop');

    function close() {
        sidebar.classList.remove('open');
        backdrop.classList.remove('open');
    }

    if (toggle) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            backdrop.classList.toggle('open');
        });
    }
    if (backdrop) {
        backdrop.addEventListener('click', close);
    }
})();

(function () {
    var toggle = document.getElementById('themeToggle');
    var icon = document.getElementById('themeIcon');
    var label = document.getElementById('themeLabel');
    if (!toggle) return;

    function isDark() {
        var current = document.documentElement.getAttribute('data-theme');
        if (current === 'dark') return true;
        if (current === 'light') return false;
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    function render() {
        var dark = isDark();
        icon.textContent = dark ? '☀️' : '🌙';
        label.textContent = dark ? 'Light Mode' : 'Dark Mode';
    }

    toggle.addEventListener('click', function () {
        var next = isDark() ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        try { localStorage.setItem('theme', next); } catch (e) {}
        render();
    });

    render();
})();
</script>
</body>
</html>
