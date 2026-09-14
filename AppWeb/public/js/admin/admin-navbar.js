// Comportamiento mejorado de la barra de navegación con Bootstrap 5 Collapse
document.addEventListener('DOMContentLoaded', function () {
    const navbarCollapseEl = document.getElementById('navbarCollapseMenu');
    if (navbarCollapseEl && window.bootstrap) {
        // Cerrar el menú colapsable automáticamente al hacer clic en un enlace en móviles
        navbarCollapseEl.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function () {
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapseEl);
                if (bsCollapse && window.innerWidth < 992) {
                    bsCollapse.hide();
                }
            });
        });
    }
});
