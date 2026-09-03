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
</script>
</body>
</html>
