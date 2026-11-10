(function () {
    const modal = document.getElementById('sk-modal');
    if (!modal) return;
    const form = modal.querySelector('form');
    const errBox = document.getElementById('sk-form-error');
    let opener = null;

    document.querySelectorAll('[data-register]').forEach(btn => {
        btn.addEventListener('click', () => {
            opener = btn;
            document.getElementById('sk-event-id').value = btn.dataset.id;
            document.getElementById('sk-event-title').textContent = btn.dataset.title;
            errBox.textContent = '';
            modal.showModal();
            form.elements.name.focus();
        });
    });

    modal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => modal.close()));
    modal.addEventListener('click', e => { if (e.target === modal) modal.close(); });
    modal.addEventListener('close', () => opener && opener.focus());

    form.addEventListener('submit', e => {
        const f = form.elements;
        let msg = '';
        if (f.name.value.trim().length < 2) msg = 'Hãy nhập họ và tên.';
        else if (!f.email.validity.valid || !f.email.value) msg = 'Email chưa đúng định dạng.';
        else if (!/^(0|\+84)\d{9}$/.test(f.phone.value.replace(/\s+/g, ''))) msg = 'Số điện thoại cần có 10 chữ số, ví dụ 0912345678.';
        if (msg) { e.preventDefault(); errBox.textContent = msg; }
    });

    const flash = document.querySelector('.sk-flash');
    if (flash) setTimeout(() => flash.classList.add('is-hidden'), 7000);
})();