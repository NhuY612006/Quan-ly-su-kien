// Menu xo xuong cua nguoi dung.
// Dung event delegation tren document nen van chay khi top/header duoc nap sau bang loadComponent.
document.addEventListener('click', (e) => {
    const toggle = e.target.closest('.user-menu-toggle');

    document.querySelectorAll('.user-menu').forEach((menu) => {
        const btn = menu.querySelector('.user-menu-toggle');
        const dd  = menu.querySelector('.user-dropdown');
        if (!btn || !dd) return;

        const clickedThis = toggle && menu.contains(toggle);
        const open = clickedThis ? dd.hidden : false;   // bam vao nut: dao trang thai; bam cho khac: dong

        dd.hidden = !open;
        btn.setAttribute('aria-expanded', String(open));
    });
});

document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('.user-menu').forEach((menu) => {
        const btn = menu.querySelector('.user-menu-toggle');
        const dd  = menu.querySelector('.user-dropdown');
        if (dd && !dd.hidden) {
            dd.hidden = true;
            btn.setAttribute('aria-expanded', 'false');
            btn.focus();
        }
    });
});

// Nut ☰ tren dien thoai / iPad doc: mo-dong menu dieu huong
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.nav-toggle');
    if (!btn) return;
    const nav = btn.closest('.site-header')?.querySelector('.main-nav');
    if (!nav) return;
    const open = nav.classList.toggle('open');
    btn.setAttribute('aria-expanded', String(open));
});