<?php
/**
 * Xử lý dữ liệu cho trang Sự kiện: truy vấn CSDL, lọc, phân trang, đăng ký.
 * Được nạp từ sukien.php (sau session_start và tạo CSRF token).
 */
require_once __DIR__ . '/../../config/app.php';
require_once ROOT_PATH . '/config/database.php';   // cần cung cấp biến $pdo (đối tượng PDO)

/* ---------- Dữ liệu sự kiện (từ CSDL, xem sukien.sql) ---------- */
$sql = "SELECT e.id, e.title, e.category, e.event_date AS `date`,
               DATE_FORMAT(e.event_time, '%H:%i') AS `time`,
               e.location, e.description AS `desc`, e.capacity,
               COUNT(r.id) AS registered
        FROM events e
        LEFT JOIN event_registrations r ON r.event_id = e.id
        WHERE e.is_published = 1
        GROUP BY e.id
        ORDER BY e.event_date";
try {
    $events = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    error_log('sukien.php: ' . $ex->getMessage());
    $events = [];
    $dbError = true;
}
foreach ($events as &$ev) { $ev['id'] = (int)$ev['id']; $ev['capacity'] = (int)$ev['capacity']; $ev['registered'] = (int)$ev['registered']; }
unset($ev);

/* ---------- Hàm hỗ trợ ---------- */
if (!function_exists('e')) { function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } }
if (!function_exists('sk_url')) { function sk_url(array $over = []) {
    $q = array_merge($_GET, $over);
    $q = array_filter($q, fn($v) => $v !== '' && $v !== null);
    return '?' . http_build_query($q);
} }
$today = date('Y-m-d');
foreach ($events as &$ev) {
    $ev['status'] = $ev['date'] < $today ? 'past' : ($ev['date'] === $today ? 'today' : 'upcoming');
    $ev['full']   = $ev['registered'] >= $ev['capacity'];
}
unset($ev);

/* ---------- Xử lý đăng ký (POST) ---------- */
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $err = null;
    $token = $_POST['csrf_token'] ?? '';
    $id    = (int)($_POST['event_id'] ?? 0);
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = preg_replace('/\s+/', '', $_POST['phone'] ?? '');

    $target = null;
    foreach ($events as $ev) { if ($ev['id'] === $id) { $target = $ev; break; } }

    if (!hash_equals($_SESSION['csrf_token'], $token))              $err = 'Phiên làm việc đã hết hạn. Hãy tải lại trang và thử lại.';
    elseif (!$target)                                                $err = 'Không tìm thấy sự kiện.';
    elseif ($target['status'] === 'past')                            $err = 'Sự kiện này đã kết thúc.';
    elseif ($target['full'])                                         $err = 'Sự kiện đã đủ số lượng đăng ký.';
    elseif (mb_strlen($name) < 2 || mb_strlen($name) > 80)           $err = 'Họ tên cần từ 2 đến 80 ký tự.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))              $err = 'Email chưa đúng định dạng.';
    elseif (!preg_match('/^(0|\+84)\d{9}$/', $phone))                $err = 'Số điện thoại cần có 10 chữ số, ví dụ 0912345678.';

    if ($err) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => $err];
    } else {
        try {
            $pdo->beginTransaction();
            $row = $pdo->prepare("SELECT capacity FROM events WHERE id = ? AND is_published = 1 FOR UPDATE");
            $row->execute([$id]);
            $cap = (int)$row->fetchColumn();
            $cnt = $pdo->prepare("SELECT COUNT(*) FROM event_registrations WHERE event_id = ?");
            $cnt->execute([$id]);
            if ((int)$cnt->fetchColumn() >= $cap) {
                $pdo->rollBack();
                $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Sự kiện vừa hết chỗ.'];
                header('Location: ' . strtok($_SERVER['REQUEST_URI'], '#') . '#danh-sach');
                exit;
            }
            $pdo->prepare("INSERT INTO event_registrations (event_id, name, email, phone) VALUES (?, ?, ?, ?)")
                ->execute([$id, $name, $email, $phone]);
            $pdo->commit();
        } catch (PDOException $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $_SESSION['flash'] = ['type' => 'error', 'msg' => $ex->getCode() === '23000'
                ? 'Email này đã đăng ký sự kiện trên.'
                : 'Không thể lưu đăng ký lúc này. Hãy thử lại sau.'];
            if ($ex->getCode() !== '23000') error_log('sukien.php: ' . $ex->getMessage());
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '#') . '#danh-sach');
            exit;
        }
        $_SESSION['flash'] = ['type' => 'ok', 'msg' => 'Đã đăng ký "' . $target['title'] . '". Thông tin xác nhận sẽ gửi tới ' . $email . '.'];
    }
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '#') . '#danh-sach');
    exit;
}

/* ---------- Lọc, tìm kiếm, phân trang ---------- */
$q     = trim($_GET['q'] ?? '');
$cat   = $_GET['loai'] ?? '';
$tt    = $_GET['tt'] ?? 'upcoming';           // upcoming | past | all
$cats  = array_values(array_unique(array_column($events, 'category')));

$list = array_filter($events, function ($ev) use ($q, $cat, $tt) {
    if ($tt === 'upcoming' && $ev['status'] === 'past')  return false;
    if ($tt === 'past'     && $ev['status'] !== 'past')  return false;
    if ($cat !== '' && $ev['category'] !== $cat)         return false;
    if ($q !== '' && mb_stripos($ev['title'] . ' ' . $ev['location'] . ' ' . $ev['desc'], $q) === false) return false;
    return true;
});
usort($list, fn($a, $b) => $tt === 'past' ? strcmp($b['date'], $a['date']) : strcmp($a['date'], $b['date']));

$perPage = 6;
$total   = count($list);
$pages   = max(1, (int)ceil($total / $perPage));
$page    = min(max(1, (int)($_GET['trang'] ?? 1)), $pages);
$shown   = array_slice($list, ($page - 1) * $perPage, $perPage);

// Sự kiện nổi bật: sự kiện sắp diễn ra gần nhất còn chỗ
$featured = null;
foreach ($events as $ev) {
    if ($ev['status'] !== 'past' && !$ev['full'] && (!$featured || $ev['date'] < $featured['date'])) $featured = $ev;
}
$thang = ['', 'Th1','Th2','Th3','Th4','Th5','Th6','Th7','Th8','Th9','Th10','Th11','Th12'];