<<<<<<< HEAD
/* =========================================================
   sukien.js — Trang Sự kiện
   1. Đăng ký / hủy đăng ký không tải lại trang (AJAX)
   2. Thông báo tự ẩn sau vài giây
   Không cần sửa PHP: script gửi form như bình thường rồi
   thay phần <main class="sk"> bằng bản mới từ server.
   Nếu có lỗi, tự quay về cách gửi form thông thường.
   ========================================================= */
(function () {
    'use strict';

    var FLASH_MS = 5000;

    /* ---------- Thông báo tự ẩn ---------- */
    function armFlash() {
        var flash = document.querySelector('.sk-flash');
        if (!flash) return;
        setTimeout(function () {
            flash.classList.add('is-hiding');
            setTimeout(function () { flash.remove(); }, 400);
        }, FLASH_MS);
    }

    /* ---------- Gửi form đăng ký / hủy bằng AJAX ---------- */
    function isEventForm(form) {
        return form.closest('.sk') &&
               form.method.toLowerCase() === 'post' &&
               form.querySelector('input[name="action"]');
    }

    function swapMain(html) {
        var doc = new DOMParser().parseFromString(html, 'text/html');
        var fresh = doc.querySelector('main.sk');
        var current = document.querySelector('main.sk');
        if (!fresh || !current) return false;
        current.innerHTML = fresh.innerHTML;
        return true;
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!isEventForm(form)) return;
        if (e.defaultPrevented) return;      // người dùng bấm "Hủy" ở hộp xác nhận

        e.preventDefault();

        var btn = form.querySelector('button[type="submit"]');
        var oldText = btn ? btn.textContent : '';
        if (btn) { btn.disabled = true; btn.textContent = 'Đang xử lý...'; }

        fetch(form.action || window.location.href, {
            method: 'POST',
            body: new FormData(form),
            credentials: 'same-origin'
        })
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.text().then(function (html) { return { res: res, html: html }; });
        })
        .then(function (r) {
            if (!swapMain(r.html)) {
                // ví dụ phiên đăng nhập hết hạn, server chuyển sang trang khác
                window.location.href = r.res.url;
                return;
            }
            armFlash();
        })
        .catch(function () {
            if (btn) { btn.disabled = false; btn.textContent = oldText; }
            form.submit();                    // dự phòng: gửi form kiểu thường
        });
    });

    armFlash();
=======
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
>>>>>>> 52ccd2d2a215aa70ae8c49d5474d8a9eb0f95459
})();