document.addEventListener("DOMContentLoaded", function () {

    const ticketSelect = document.getElementById("MaVe");
    const quantityInput = document.getElementById("SoLuong");
    const totalPrice = document.getElementById("totalPrice");
    const bookingForm = document.getElementById("bookingForm");

    // Định dạng tiền Việt Nam
    function formatMoney(amount) {
        return amount.toLocaleString("vi-VN") + " VNĐ";
    }

    // Tính tổng tiền
    function calculateTotal() {

        const selectedOption =
            ticketSelect.options[ticketSelect.selectedIndex];

        const price = Number(selectedOption.dataset.price) || 0;

        const quantity = Number(quantityInput.value) || 1;

        const total = price * quantity;

        totalPrice.textContent = formatMoney(total);
    }

    // Khi thay đổi loại vé
    ticketSelect.addEventListener("change", calculateTotal);

    // Khi thay đổi số lượng
    quantityInput.addEventListener("input", calculateTotal);

    // Tính tiền ban đầu
    calculateTotal();

    // Kiểm tra form demo
    bookingForm.addEventListener("submit", function (event) {

        event.preventDefault();

        alert(
            "Giao diện đăng ký đã nhận thông tin. " +
            "Chức năng lưu đăng ký vào MySQL cần được tích hợp tiếp."
        );

    });

});