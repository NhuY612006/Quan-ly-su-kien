
document.addEventListener("DOMContentLoaded", function () {

    // Đường dẫn từ thư mục pages/*/
    const root = "../../";

    // TOP + HEADER
    const headerHTML = `
        <div class="top_container">
            <div class="top_left">
                <i class="fa-solid fa-location-dot"></i>
                208/5 3/2, Phường Ninh Kiều, TP Cần Thơ

                <span>|</span>

                <i class="fa-solid fa-phone"></i>
                0772474933

                <span>|</span>

                <i class="fa-solid fa-clock"></i>
                8:00 - 22:00
            </div>

            <div class="top_right">
                <a href="${root}pages/taikhoan/taikhoan.php">
                    <i class="fa-solid fa-user"></i>
                    Tài khoản
                </a>
            </div>
        </div>

        <header class="page-header">

            <a href="${root}index.php"
               class="page-header__logo-link">

                <img src="${root}assets/images/Music for life.png"
                     alt="Music for life">
            </a>

            <nav class="page-header__navbar">

                <a class="page-header__link"
                   href="${root}pages/sukien/sukien.php">
                    Sự kiện
                </a>

                <a class="page-header__link"
                   href="${root}pages/muave/muave.php">
                    Mua vé
                </a>

                <a class="page-header__link"
                   href="${root}pages/taikhoan/taikhoan.php">
                    Tài khoản
                </a>

            </nav>
        </header>
    `;

    // FOOTER
    const footerHTML = `
        <footer class="page-footer">

            <a class="page-footer__logo"
               href="${root}index.php">

                <img src="${root}assets/images/Music for life.png"
                     alt="Music For Life">
            </a>

            <div class="page-footer__columns">

                <div class="page-footer__col">
                    <h3>LIÊN HỆ</h3>

                    <p>
                        <i class="fa-solid fa-location-dot"></i>
                        208/5 3/2, Ninh Kiều, Cần Thơ
                    </p>

                    <p>
                        <i class="fa-solid fa-phone"></i>
                        0772474933
                    </p>

                    <p>
                        <i class="fa-solid fa-envelope"></i>
                        musicforlife@gmail.com
                    </p>
                </div>

                <div class="page-footer__col">
                    <h3>LIÊN KẾT NHANH</h3>

                    <ul>
                        <li>
                            <a href="${root}pages/sukien/sukien.php">
                                Sự kiện
                            </a>
                        </li>

                        <li>
                            <a href="${root}pages/muave/muave.php">
                                Mua vé
                            </a>
                        </li>

                        <li>
                            <a href="${root}pages/taikhoan/taikhoan.php">
                                Tài khoản
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="page-footer__col">
                    <h3>KẾT NỐI</h3>

                    <div class="page-footer__social-icons">
                        <a href="https://www.facebook.com">
                            <i class="fa-brands fa-facebook"></i>
                        </a>

                        <a href="https://www.instagram.com">
                            <i class="fa-brands fa-instagram"></i>
                        </a>

                        <a href="https://www.youtube.com">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                    </div>
                </div>

            </div>

            <div class="page-footer__legal">
                <p>© 2026 Music For Life.</p>
            </div>

        </footer>
    `;

    // Hiển thị header
    const headerContainer = document.getElementById("site-header");

    if (headerContainer) {
        headerContainer.innerHTML = headerHTML;
    }

    // Hiển thị footer
    const footerContainer = document.getElementById("site-footer");

    if (footerContainer) {
        footerContainer.innerHTML = footerHTML;
    }

});
