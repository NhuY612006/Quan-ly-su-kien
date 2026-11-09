
<?php
require_once __DIR__ . '/../../includes/auth.php';

// Bắt buộc đăng nhập
requireLogin();

$db = getDB();
$id_nguoi_dung = (int) $_SESSION['user_id'];

$danh_sach_ve = [];
$loi = '';

try {
    // Lấy danh sách vé của người dùng đang đăng nhập
    $stmt = $db->prepare("
        SELECT
            d.ID,
            d.NgayDangKy,
            d.TrangThai,
            d.MaVe,
            d.TrangThaiCheckIn,
            d.ThoiGianCheckIn,
            s.TenSuKien,
            s.DiaDiem,
            s.ThoiGianBatDau,
            s.ThoiGianKetThuc
        FROM dangkysukien d
        JOIN sukien s ON d.ID_SuKien = s.ID
        WHERE d.ID_NguoiDung = ?
        ORDER BY d.NgayDangKy DESC
    ");

    $stmt->execute([$id_nguoi_dung]);
    $danh_sach_ve = $stmt->fetchAll();

} catch (PDOException $e) {
    $loi = 'Không thể tải danh sách vé. Vui lòng thử lại.';
}

$pageTitle = 'Vé của tôi - Music4Life';

require __DIR__ . '/../../components/top.php';
require __DIR__ . '/../../components/header.php';
?>

<style>
    
/* =====================================
   VE CUA TOI - CYBER OCEAN
===================================== */

.ticket-container {
    position: relative;
    isolation: isolate;
    max-width: 1100px;
    margin: 35px auto;
    padding: 35px 25px;
    border-radius: 22px;
    overflow: hidden;
    background:
        radial-gradient(ellipse at 5% 15%, rgba(0, 225, 255, .17), transparent 35%),
        radial-gradient(ellipse at 95% 85%, rgba(145, 90, 255, .18), transparent 38%),
        linear-gradient(135deg, #061b35, #0b3457 55%, #071a36);
    box-shadow: 0 12px 40px rgba(0, 15, 40, .2);
}

/* Tiêu đề */
.ticket-container h1 {
    position: relative;
    z-index: 1;
    text-align: center;
    color: #fff;
    font-family: 'Be Vietnam Pro', Arial, sans-serif;
    font-size: 32px;
    margin: 0 0 10px;
    text-shadow: 0 0 14px rgba(0, 230, 255, .45);
}

.ticket-container h1::after {
    content: "QUẢN LÝ VÉ SỰ KIỆN CỦA BẠN";
    display: block;
    margin-top: 9px;
    color: #8cefff;
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 2px;
    text-shadow: none;
}

/* Thẻ vé */
.ticket-card {
    position: relative;
    z-index: 1;
    border: 1px solid rgba(100, 225, 255, .45);
    border-left: 5px solid #36c9e8;
    border-radius: 15px;
    padding: 24px 28px;
    margin: 22px auto;
    max-width: 850px;
    background: rgba(255, 255, 255, .97);
    box-shadow:
        0 8px 25px rgba(0, 0, 0, .18),
        0 0 14px rgba(0, 220, 255, .12);
    transition: transform .25s ease, box-shadow .25s ease;
}

.ticket-card:hover {
    transform: translateY(-4px);
    box-shadow:
        0 13px 30px rgba(0, 0, 0, .22),
        0 0 20px rgba(0, 220, 255, .25);
}

.ticket-card h3 {
    margin: 0 0 18px;
    color: #174767;
    font-family: 'Be Vietnam Pro', Arial, sans-serif;
    font-size: 21px;
    padding-bottom: 13px;
    border-bottom: 1px dashed #c8e4ed;
}

.ticket-info {
    margin: 12px 0;
    color: #526979;
    font-size: 14px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.ticket-info strong {
    display: inline-block;
    min-width: 175px;
    color: #24485f;
}

/* Trạng thái vé */
.status {
    display: inline-block;
    font-size: 12px;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 20px;
}

.status-active {
    color: #087b58;
    background: #dcf8ed;
}

.status-cancelled {
    color: #c03939;
    background: #ffe5e5;
}

.status-checked {
    color: #1768a5;
    background: #dff2ff;
}

/* Nút hủy */
.cancel-button {
    padding: 10px 20px;
    border: 0;
    border-radius: 8px;
    background: linear-gradient(135deg, #ed6262, #c93650);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 15px;
    transition: .2s;
}

.cancel-button:hover {
    background: linear-gradient(135deg, #d94a4a, #a9233c);
    transform: translateY(-2px);
    box-shadow: 0 5px 14px rgba(200, 50, 70, .25);
}

/* Thông báo */
.message,
.empty-message {
    position: relative;
    z-index: 1;
    max-width: 850px;
    margin: 18px auto;
    padding: 16px 20px;
    border-radius: 10px;
    font-size: 14px;
}

.message {
    color: #155b78;
    background: #e4f8ff;
    border: 1px solid #9ce6f5;
}

.empty-message {
    padding: 35px 20px;
    color: #d8f8ff;
    background: rgba(255, 255, 255, .09);
    border: 1px dashed rgba(130, 235, 255, .65);
    text-align: center;
}

/* Cá hologram trang trí */
.ticket-container .holo-fish {
    position: absolute;
    z-index: 0;
    width: 66px;
    height: 32px;
    border: 1px solid rgba(100, 250, 255, .9);
    border-radius: 50%;
    background: linear-gradient(
        135deg,
        rgba(0, 255, 255, .08),
        rgba(0, 190, 255, .48),
        rgba(170, 100, 255, .2)
    );
    box-shadow:
        0 0 8px #00eaff,
        0 0 18px rgba(0, 225, 255, .7),
        inset 0 0 12px rgba(100, 250, 255, .6);
    opacity: .7;
    pointer-events: none;
}

.ticket-container .holo-fish::before {
    content: "";
    position: absolute;
    left: -17px;
    top: 5px;
    width: 20px;
    height: 22px;
    background: rgba(0, 230, 255, .35);
    border: 1px solid rgba(100, 250, 255, .9);
    clip-path: polygon(100% 50%, 0 0, 0 100%);
}

.ticket-container .holo-fish::after {
    content: "";
    position: absolute;
    right: 12px;
    top: 10px;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 0 7px 2px #00ffff;
}

.ticket-container .fish-one {
    top: 16%;
    left: 4%;
    animation: myFishOne 9s ease-in-out infinite;
}

.ticket-container .fish-two {
    top: 42%;
    right: 4%;
    animation: myFishTwo 11s ease-in-out infinite;
}

.ticket-container .fish-three {
    bottom: 12%;
    left: 7%;
    animation: myFishThree 12s ease-in-out infinite;
}

.ticket-container .fish-four {
    bottom: 20%;
    right: 8%;
    animation: myFishFour 10s ease-in-out infinite;
}

@keyframes myFishOne {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(30px, -18px); }
}

@keyframes myFishTwo {
    0%, 100% { transform: scaleX(-1) translate(0, 0); }
    50% { transform: scaleX(-1) translate(30px, 18px); }
}

@keyframes myFishThree {
    0%, 100% { transform: scale(.8) translate(0, 0); }
    50% { transform: scale(.8) translate(25px, -20px); }
}

@keyframes myFishFour {
    0%, 100% { transform: scaleX(-1) translate(0, 0); }
    50% { transform: scaleX(-1) translate(25px, -15px); }
}

/* Responsive */
@media (max-width: 600px) {
    .ticket-container {
        margin: 18px auto;
        padding: 25px 12px;
        border-radius: 14px;
    }

    .ticket-container h1 {
        font-size: 25px;
    }

    .ticket-card {
        padding: 20px 16px;
    }

    .ticket-card h3 {
        font-size: 18px;
    }

    .ticket-info {
        font-size: 13px;
    }

    .ticket-info strong {
        display: block;
        min-width: 0;
        margin-bottom: 3px;
    }

    .ticket-container .holo-fish {
        opacity: .25;
        transform: scale(.65);
    }
}

@media (prefers-reduced-motion: reduce) {
    .ticket-container .holo-fish {
        animation: none !important;
    }

    .ticket-card {
        transition: none;
    }
}

</style>

<div class="ticket-container">

    <!-- Cá hologram trang trí -->
    <div class="holo-fish fish-one" aria-hidden="true"></div>
    <div class="holo-fish fish-two" aria-hidden="true"></div>
    <div class="holo-fish fish-three" aria-hidden="true"></div>
    <div class="holo-fish fish-four" aria-hidden="true"></div>

    <h1>Vé của tôi</h1>

    <?php if (isset($_GET['registered'])): ?>
        <div class="message">
            Đăng ký sự kiện thành công!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['cancelled'])): ?>
        <div class="message">
            Hủy đăng ký thành công!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="message">
            <?= e($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <?php if ($loi !== ''): ?>

        <div class="message">
            <?= e($loi) ?>
        </div>

    <?php elseif (empty($danh_sach_ve)): ?>

        <div class="empty-message">
            Bạn chưa đăng ký sự kiện nào.
        </div>

    <?php else: ?>

        <?php foreach ($danh_sach_ve as $ve): ?>

            <div class="ticket-card">

                <h3><?= e($ve['TenSuKien']) ?></h3>

                <p class="ticket-info">
                    <strong>Địa điểm:</strong>
                    <?= e($ve['DiaDiem']) ?>
                </p>

                <p class="ticket-info">
                    <strong>Thời gian bắt đầu:</strong>
                    <?= e($ve['ThoiGianBatDau']) ?>
                </p>

                <p class="ticket-info">
                    <strong>Thời gian kết thúc:</strong>
                    <?= e($ve['ThoiGianKetThuc']) ?>
                </p>

                <p class="ticket-info">
                    <strong>Ngày đăng ký:</strong>
                    <?= e($ve['NgayDangKy']) ?>
                </p>

                <p class="ticket-info">
                    <strong>Mã vé:</strong>
                    <?= e($ve['MaVe']) ?>
                </p>

                <p class="ticket-info">
                    <strong>Trạng thái đăng ký:</strong>

                    <?php if ($ve['TrangThai'] === 'da_dang_ky'): ?>
                        <span class="status status-active">
                            Đã đăng ký
                        </span>
                    <?php else: ?>
                        <span class="status status-cancelled">
                            Đã hủy
                        </span>
                    <?php endif; ?>
                </p>

                <p class="ticket-info">
                    <strong>Trạng thái check-in:</strong>

                    <?php if ($ve['TrangThaiCheckIn'] === 'da_checkin'): ?>
                        <span class="status status-checked">
                            Đã check-in
                        </span>
                    <?php else: ?>
                        Chưa check-in
                    <?php endif; ?>
                </p>

                <?php if (
                    $ve['TrangThai'] === 'da_dang_ky' &&
                    $ve['TrangThaiCheckIn'] !== 'da_checkin' &&
                    strtotime($ve['ThoiGianBatDau']) > time()
                ): ?>

                    <form
                        action="<?= BASE_URL ?>/backend/registrations/cancel.php"
                        method="POST"
                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy đăng ký sự kiện này không?');"
                    >

                        <input
                            type="hidden"
                            name="ID_DangKy"
                            value="<?= (int) $ve['ID'] ?>"
                        >

                        <?= csrfField() ?>

                        <button type="submit" class="cancel-button">
                            Hủy đăng ký
                        </button>

                    </form>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

</main>

<?php require __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
