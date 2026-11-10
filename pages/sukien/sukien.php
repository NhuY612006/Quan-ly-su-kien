<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Toàn bộ xử lý dữ liệu nằm ở file riêng; file này chỉ hiển thị.
require_once __DIR__ . '/sukien_controller.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sự kiện</title>

    <link rel="stylesheet" href="sukien.css">
    <link rel="stylesheet" href="../../assets/css/style.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>

    <?php
    require_once __DIR__ . '/../../config/app.php';
    require_once ROOT_PATH . '/config/database.php';
    ?>
    <?php include __DIR__ . '/../../components/top.php'; ?>
    <?php include ROOT_PATH . '/components/header.php'; ?>

    <!-- Nội dung trang sự kiện -->
    <main class="main-section sk">

        <?php if ($flash): ?>
            <div class="sk-flash sk-flash--<?= e($flash['type']) ?>" role="status">
                <i class="fa-solid <?= $flash['type'] === 'ok' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                <?= e($flash['msg']) ?>
            </div>
        <?php endif; ?>

        <!-- Giới thiệu + sự kiện nổi bật -->
        <section class="sk-hero">
            <div class="sk-hero__text">
                <h1>Sự kiện sắp diễn ra</h1>
                <p>Concert, đại nhạc hội và đêm nhạc đang được chờ đợi nhất mùa cuối năm. Chọn đêm diễn và đăng ký quan tâm chỉ trong một phút.</p>
                <dl class="sk-stats">
                    <div><dt>Sắp diễn ra</dt><dd><?= count(array_filter($events, fn($x) => $x['status'] !== 'past')) ?></dd></div>
                    <div><dt>Đã tổ chức</dt><dd><?= count(array_filter($events, fn($x) => $x['status'] === 'past')) ?></dd></div>
                    <div><dt>Lượt đăng ký</dt><dd><?= array_sum(array_column($events, 'registered')) ?></dd></div>
                </dl>
            </div>

            <?php if ($featured): $d = strtotime($featured['date']); ?>
            <article class="sk-feature">
                <div class="sk-feature__date">
                    <b><?= date('d', $d) ?></b>
                    <span><?= $thang[(int)date('n', $d)] ?></span>
                </div>
                <div class="sk-feature__body">
                    <span class="sk-tag"><?= e($featured['category']) ?></span>
                    <h2><?= e($featured['title']) ?></h2>
                    <p class="sk-meta"><i class="fa-regular fa-clock"></i> <?= e($featured['time']) ?></p>
                    <p class="sk-meta"><i class="fa-solid fa-location-dot"></i> <?= e($featured['location']) ?></p>
                    <button class="sk-btn" data-register data-id="<?= $featured['id'] ?>" data-title="<?= e($featured['title']) ?>">Đăng ký tham gia</button>
                </div>
            </article>
            <?php endif; ?>
        </section>

        <!-- Bộ lọc -->
        <section class="sk-filter" id="danh-sach">
            <form method="get" class="sk-search" role="search">
                <input type="hidden" name="loai" value="<?= e($cat) ?>">
                <input type="hidden" name="tt" value="<?= e($tt) ?>">
                <label class="sr-only" for="sk-q">Tìm sự kiện</label>
                <i class="fa-solid fa-magnifying-glass"></i>
                <input id="sk-q" type="search" name="q" value="<?= e($q) ?>" placeholder="Tìm theo tên, địa điểm...">
                <button class="sk-btn sk-btn--sm" type="submit">Tìm</button>
            </form>

            <div class="sk-tabs" role="tablist" aria-label="Trạng thái">
                <?php foreach (['upcoming' => 'Sắp diễn ra', 'past' => 'Đã kết thúc', 'all' => 'Tất cả'] as $k => $label): ?>
                    <a href="<?= sk_url(['tt' => $k, 'trang' => '']) ?>#danh-sach" class="<?= $tt === $k ? 'is-active' : '' ?>"><?= $label ?></a>
                <?php endforeach; ?>
            </div>

            <div class="sk-chips">
                <a href="<?= sk_url(['loai' => '', 'trang' => '']) ?>#danh-sach" class="<?= $cat === '' ? 'is-active' : '' ?>">Mọi loại</a>
                <?php foreach ($cats as $c): ?>
                    <a href="<?= sk_url(['loai' => $c, 'trang' => '']) ?>#danh-sach" class="<?= $cat === $c ? 'is-active' : '' ?>"><?= e($c) ?></a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Danh sách -->
        <section class="sk-list">
            <?php if (!$shown): ?>
                <div class="sk-empty">
                    <i class="fa-regular fa-calendar-xmark"></i>
                    <p>Không có sự kiện nào khớp với bộ lọc.</p>
                    <a class="sk-btn sk-btn--ghost" href="?">Xóa bộ lọc</a>
                </div>
            <?php endif; ?>

            <?php foreach ($shown as $ev):
                $d = strtotime($ev['date']);
                $left = $ev['capacity'] - $ev['registered'];
                $pct = min(100, round($ev['registered'] / $ev['capacity'] * 100));
            ?>
            <article class="sk-card sk-card--<?= $ev['status'] ?>">
                <div class="sk-card__date">
                    <b><?= date('d', $d) ?></b>
                    <span><?= $thang[(int)date('n', $d)] ?> <?= date('Y', $d) ?></span>
                </div>
                <div class="sk-card__body">
                    <div class="sk-card__top">
                        <span class="sk-tag"><?= e($ev['category']) ?></span>
                        <?php if ($ev['status'] === 'past'): ?><span class="sk-state">Đã kết thúc</span>
                        <?php elseif ($ev['status'] === 'today'): ?><span class="sk-state sk-state--live">Diễn ra hôm nay</span>
                        <?php elseif ($ev['full']): ?><span class="sk-state">Hết chỗ</span>
                        <?php elseif ($left <= 10): ?><span class="sk-state sk-state--warn">Còn <?= $left ?> chỗ</span><?php endif; ?>
                    </div>
                    <h3><?= e($ev['title']) ?></h3>
                    <p class="sk-card__desc"><?= e($ev['desc']) ?></p>
                    <p class="sk-meta"><i class="fa-regular fa-clock"></i> <?= e($ev['time']) ?></p>
                    <p class="sk-meta"><i class="fa-solid fa-location-dot"></i> <?= e($ev['location']) ?></p>

                    <div class="sk-bar" title="<?= $ev['registered'] ?>/<?= $ev['capacity'] ?> đã đăng ký">
                        <span style="width:<?= $pct ?>%"></span>
                    </div>
                    <p class="sk-bar__label"><?= $ev['registered'] ?>/<?= $ev['capacity'] ?> người đã đăng ký</p>

                    <?php if ($ev['status'] === 'past' || $ev['full']): ?>
                        <button class="sk-btn sk-btn--ghost" disabled><?= $ev['status'] === 'past' ? 'Đã kết thúc' : 'Hết chỗ' ?></button>
                    <?php else: ?>
                        <button class="sk-btn" data-register data-id="<?= $ev['id'] ?>" data-title="<?= e($ev['title']) ?>">Đăng ký</button>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </section>

        <!-- Phân trang -->
        <?php if ($pages > 1): ?>
        <nav class="sk-pager" aria-label="Phân trang">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="<?= sk_url(['trang' => $i]) ?>#danh-sach" class="<?= $i === $page ? 'is-active' : '' ?>" <?= $i === $page ? 'aria-current="page"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>

    </main>

    <!-- Hộp thoại đăng ký -->
    <dialog class="sk-modal" id="sk-modal" aria-labelledby="sk-modal-title">
        <form method="post" action="" class="sk-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="event_id" id="sk-event-id">

            <header>
                <h2 id="sk-modal-title">Đăng ký tham gia</h2>
                <button type="button" class="sk-close" data-close aria-label="Đóng"><i class="fa-solid fa-xmark"></i></button>
            </header>
            <p class="sk-form__event" id="sk-event-title"></p>

            <label>Họ và tên
                <input type="text" name="name" required minlength="2" maxlength="80" autocomplete="name">
            </label>
            <label>Email
                <input type="email" name="email" required autocomplete="email">
            </label>
            <label>Số điện thoại
                <input type="tel" name="phone" required pattern="(0|\+84)\d{9}" inputmode="tel" autocomplete="tel" placeholder="0912345678">
            </label>
            <p class="sk-form__error" id="sk-form-error" role="alert"></p>

            <div class="sk-form__actions">
                <button type="button" class="sk-btn sk-btn--ghost" data-close>Hủy</button>
                <button type="submit" class="sk-btn">Xác nhận đăng ký</button>
            </div>
        </form>
    </dialog>

    <?php include ROOT_PATH . '/components/footer.php'; ?>
    <script src="../../assets/js/components.js"></script>
    <script src="sukien.js"></script>

</body>
</html>