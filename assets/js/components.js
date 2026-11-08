
document.addEventListener('DOMContentLoaded', function () {
    const userMenu = document.getElementById('userMenu');
    const toggle = document.getElementById('userMenuToggle');
    const dropdown = document.getElementById('userDropdown');

    if (!userMenu || !toggle || !dropdown) return;

    toggle.addEventListener('click', function (event) {
        event.stopPropagation();

        const isOpen = userMenu.classList.toggle('open');

        dropdown.hidden = !isOpen;
        toggle.setAttribute('aria-expanded', isOpen);
    });

    // Nhấn bên ngoài để đóng
    document.addEventListener('click', function (event) {
        if (!userMenu.contains(event.target)) {
            userMenu.classList.remove('open');
            dropdown.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
        }
    });

    // Nhấn ESC để đóng
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            userMenu.classList.remove('open');
            dropdown.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
});
