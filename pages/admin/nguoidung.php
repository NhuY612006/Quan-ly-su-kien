<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$tab  = ($_GET['tab'] ?? 'taikhoan') === 'truycap' ? 'truycap' : 'taikhoan';
$q    = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per  = 20;
$off  = ($page - 1) * $per;
$like = '%' . $q . '%';

if ($tab === 'taikhoan') {
    $cnt = $conn->prepare("SELECT COUNT(*) FROM nguoidung WHERE HoTen LIKE ? OR Email LIKE ?");
    $cnt->execute([$like, $like]);
    $total = (int)$cnt->fetchColumn();

    $st = $conn->prepare("
        SELECT n.ID, n.HoTen, n.Email, n.VaiTro, n.TrangThai,
               (SELECT MAX(ThoiGian) FROM nhatky_truycap t WHERE t.ID_NguoiDung = n.ID) AS LanCuoi,
               (SELECT DiaChiIP FROM nhatky_truycap t WHERE t.ID_NguoiDung = n.ID
                 ORDER BY ThoiGian DESC LIMIT 1) AS IPCuoi
        FROM nguoidung n
        WHERE n.HoTen LIKE ? OR n.Email LIKE ?
        ORDER BY n.ID DESC LIMIT $per OFFSET $off");
    $st->execute([$like, $like]);
} else {
    // tab truy cập: tìm theo IP hoặc trang
    $cnt = $conn->prepare("SELECT COUNT(*) FROM nhatky_truycap WHERE DiaChiIP LIKE ? OR Trang LIKE ?");
    $cnt->execute([$like, $like]);
    $total = (int)$cnt->fetchColumn();

    $st = $conn->prepare("
        SELECT t.*, n.HoTen
        FROM nhatky_truycap t LEFT JOIN nguoidung n ON n.ID = t.ID_NguoiDung
        WHERE t.DiaChiIP LIKE ? OR t.Trang LIKE ?
        ORDER BY t.ThoiGian DESC LIMIT $per OFFSET $off");
    $st->execute([$like, $like]);
}
$rows  = $st->fetchAll();
$pages = max(1, (int)ceil($total / $per));
?>
<!DOCTYPE html>
<html lang="vi"><head>
<meta charset="utf-8">
<title>Quản lý người dùng</title>
<link rel="stylesheet" href="../../assets/css/style.css">
<link rel="stylesheet" href="../../pages/taikhoan/taikhoan.css">
</head>
<body>
<?php require_once __DIR__ . '/../../components/top.php'; ?>
<?php require_once __DIR__ . '/../../components/header.php'; ?>
<h1>Quản lý người dùng</h1>
<nav>
  <a href="?tab=taikhoan">Tài khoản</a> |
  <a href="?tab=truycap">Lượt truy cập hệ thống</a>
</nav>

<form method="get">
  <input type="hidden" name="tab" value="<?= e($tab) ?>">
  <input name="q" value="<?= e($q) ?>" placeholder="<?= $tab==='taikhoan' ? 'Tên hoặc email' : 'IP hoặc trang' ?>">
  <button>Tìm</button>
</form>

<?php if ($tab === 'taikhoan'): ?>
<table border="1" cellpadding="6">
  <tr>
    <th>ID</th><th>Họ tên</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th>
    <th>Truy cập cuối</th><th>IP cuối</th><th>Thao tác</th>
  </tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td><?= (int)$r['ID'] ?></td>
    <td><a href="nguoidung_chitiet.php?id=<?= (int)$r['ID'] ?>"><?= e($r['HoTen']) ?></a></td>
    <td><?= e($r['Email']) ?></td>
    <td><?= e($r['VaiTro']) ?></td>
    <td><?= e($r['TrangThai']) ?></td>
    <td><?= e($r['LanCuoi'] ?? '—') ?></td>
    <td><?= e($r['IPCuoi'] ?? '—') ?></td>
    <td>
      <?php if ($r['VaiTro'] !== 'admin' && (int)$r['ID'] !== (int)$_SESSION['user_id']): ?>
      <form method="post" action="nguoidung_khoa.php" style="display:inline"
            onsubmit="return confirm('Xác nhận thay đổi trạng thái tài khoản này?');">
        <?= csrfField() ?>
        <input type="hidden" name="id" value="<?= (int)$r['ID'] ?>">
        <input type="hidden" name="hanh_dong" value="<?= $r['TrangThai'] === 'khoa' ? 'mo' : 'khoa' ?>">
        <button type="submit"><?= $r['TrangThai'] === 'khoa' ? '🔓 Mở khoá' : '🔒 Khoá' ?></button>
      </form>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php else: ?>
<table class="user-access" border="1" cellpadding="6">
  <tr><th>Thời gian</th><th>Tài khoản</th><th>Địa chỉ IP</th><th>Trang</th><th>Method</th><th>Trình duyệt</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td><?= e($r['ThoiGian']) ?></td>
    <td><?php if ($r['ID_NguoiDung']): ?>
          <a href="nguoidungchitiet.php?id=<?= (int)$r['ID_NguoiDung'] ?>"><?= e($r['HoTen']) ?></a>
        <?php else: ?>Khách<?php endif; ?></td>
    <td><?= e($r['DiaChiIP']) ?></td>
    <td><?= e($r['Trang']) ?></td>
    <td><?= e($r['PhuongThuc']) ?></td>
    <td><?= e($r['TrinhDuyet']) ?></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<p>
  <?php for ($i = 1; $i <= $pages; $i++): ?>
    <a href="?tab=<?= e($tab) ?>&q=<?= urlencode($q) ?>&page=<?= $i ?>"><?= $i ?></a>
  <?php endfor; ?>
</p>
<?php require_once __DIR__ . '/../../components/footer.php'; ?>
<script src="/../../assets/js/components.js"></script>
</body>
</html>