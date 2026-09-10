document.addEventListener("DOMContentLoaded", function () {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.style.display = 'none';
    }

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.querySelector('.sidebar-close');

    function setSidebarAria(isOpen) {
        if (sidebarToggle) {
            sidebarToggle.setAttribute('aria-expanded', String(isOpen));
        }
        if (sidebar) {
            sidebar.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        }
    }

    function toggleSidebar() {
        if (!sidebar || !sidebarOverlay || !sidebarToggle) {
            return;
        }
        sidebar.classList.toggle('show');
        sidebarOverlay.classList.toggle('show');

        const icon = sidebarToggle.querySelector('i');
        if (sidebar.classList.contains('show')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
            document.body.style.overflow = 'hidden';
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
            document.body.style.overflow = '';
        }

        setSidebarAria(sidebar.classList.contains('show'));
    }

    function closeSidebar() {
        if (!sidebar || !sidebarOverlay || !sidebarToggle) {
            return;
        }
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        document.body.style.overflow = '';

        const icon = sidebarToggle.querySelector('i');
        setSidebarAria(false);

        if (icon) {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    }

    setSidebarAria(false);

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    if (sidebarClose) {
        sidebarClose.addEventListener('click', function (e) {
            e.stopPropagation();
            closeSidebar();
        });
    }

    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 991) {
            if (sidebar.classList.contains('show') &&
                !sidebar.contains(event.target) &&
                !sidebarToggle.contains(event.target)) {
                closeSidebar();
            }
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth > 991) {
            closeSidebar();
        }
    });

    if (window.innerWidth <= 991) {
        const menuLinks = document.querySelectorAll('.submenu-link, .menu-link[href]');
        menuLinks.forEach(link => {
            link.addEventListener('click', () => {
                setTimeout(closeSidebar, 300);
            });
        });
    }

    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.submenu-link, .menu-link[href]');

    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (!href) return;

        let linkPath;
        try {
            const linkUrl = new URL(href, window.location.origin);
            linkPath = linkUrl.pathname;
        } catch (e) {
            linkPath = href.split(/[?#]/)[0];
        }

        if (linkPath === currentPath) {
            link.classList.add('active');
            activateParentMenu(link);
        } else if (currentPath.startsWith(linkPath + '/') && linkPath !== '/' && linkPath !== currentPath) {
            const isUnderPhotos = currentPath.startsWith('/admin/products/photos') ||
                                  /^\/admin\/products\/\d+\/photos-upload\/?$/.test(currentPath);

            if (linkPath === '/admin/products' && isUnderPhotos) {
                return;
            }

            link.classList.add('active');
            activateParentMenu(link);
        }
    });

    function activateParentMenu(link) {
        const parentCollapse = link.closest('.collapse');
        if (parentCollapse) {
            parentCollapse.classList.add('show');
            const parentToggle = parentCollapse.previousElementSibling;
            if (parentToggle && parentToggle.classList.contains('menu-toggle')) {
                parentToggle.classList.remove('collapsed');
                parentToggle.setAttribute('aria-expanded', 'true');
            }
        }
    }

    const collapses = document.querySelectorAll('#sidebarMenu .collapse');
    collapses.forEach(collapse => {
        const toggle = collapse.previousElementSibling;
        if (!toggle) return;

        collapse.addEventListener('shown.bs.collapse', () => toggle.classList.remove('collapsed'));
        collapse.addEventListener('hidden.bs.collapse', () => toggle.classList.add('collapsed'));
    });
});
