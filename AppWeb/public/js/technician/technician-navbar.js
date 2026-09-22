// ==========================================================================
// Lógica de Navegación para el Rol Técnico (CelIx)
// ==========================================================================

document.addEventListener('DOMContentLoaded', function () {
    const navbarCollapse = document.getElementById('technicianNavbarCollapse');
    if (navbarCollapse) {
        const navLinks = navbarCollapse.querySelectorAll('.nav-link:not(.dropdown-toggle)');
        navLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992 && navbarCollapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
            });
        });
    }
});
