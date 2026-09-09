// ============================================================
// ADMIN.JS - JAVASCRIPT DE L'ADMINISTRATION (APIS RAPIDO)
// ============================================================

// Confirmation avant changement de statut d'une commande
function initStatusFormConfirm() {
    document.querySelectorAll('.admin-status-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const select = this.querySelector('select[name="statut"]');
            if (select && !confirm('Confirmer le changement de statut vers "' + select.options[select.selectedIndex].text + '" ?')) {
                e.preventDefault();
            }
        });
    });
}

// Repli / dépli de la barre latérale sur mobile
function initAdminSidebarToggle() {
    const sidebar = document.querySelector('.admin-sidebar');
    if (!sidebar) return;

    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'admin-sidebar-toggle';
    toggle.setAttribute('aria-label', 'Ouvrir le menu administration');
    toggle.innerHTML = '<ion-icon name="menu-outline"></ion-icon>';

    // Fond semi-transparent affiché derrière le menu quand il est ouvert (mobile)
    const backdrop = document.createElement('div');
    backdrop.className = 'admin-sidebar-backdrop';
    document.body.appendChild(backdrop);

    function closeSidebar() {
        sidebar.classList.remove('admin-sidebar-open');
        backdrop.classList.remove('admin-sidebar-backdrop-visible');
    }

    const topbar = document.querySelector('.admin-topbar');
    if (topbar) {
        topbar.prepend(toggle);
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('admin-sidebar-open');
            backdrop.classList.toggle('admin-sidebar-backdrop-visible');
        });
        backdrop.addEventListener('click', closeSidebar);
        // Fermer automatiquement après avoir choisi une section du menu
        sidebar.querySelectorAll('a').forEach(link => link.addEventListener('click', closeSidebar));
    }
}

document.addEventListener('DOMContentLoaded', function () {
    initStatusFormConfirm();
    initAdminSidebarToggle();
});
