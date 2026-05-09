<?php
// ============================================================
// db.php - ملف الاتصال بقاعدة البيانات
// ============================================================

$host = getenv('mysql.railway.internal');
$port = getenv('3306    ');
$user = getenv('root');
$pass = getenv('UhJsLLrOGFBGpQhlbJydGQOWIbkOQfpj');
$db   = getenv('railway');

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('<div style="font-family:Arial;direction:rtl;padding:20px;color:red;">
         <h3>خطأ في الاتصال بقاعدة البيانات</h3>
         <p>تأكد من تشغيل MySQL وإعداد قاعدة البيانات بشكل صحيح.</p>
         <small>' . htmlspecialchars($e->getMessage()) . '</small>
         </div>');
}

// Helper: get category badge color
function getCategoryBadge(string $category): string {
    $colors = [
        'ثقافة'  => 'primary',
        'رياضة'  => 'success',
        'موسيقى' => 'warning',
        'عائلي'  => 'danger',
        'علوم'   => 'info',
        'أخرى'   => 'secondary',
    ];
    return $colors[$category] ?? 'secondary';
}

// Helper: format Arabic date
function formatDate(string $date): string {
    $ts = strtotime($date);
    $days   = ['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
    $months = ['','يناير','فبراير','مارس','أبريل','مايو','يونيو',
               'يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
    $day  = $days[(int)date('w', $ts)];
    $d    = (int)date('j', $ts);
    $m    = $months[(int)date('n', $ts)];
    $y    = date('Y', $ts);
    return $day . '، ' . $d . ' ' . $m . ' ' . $y;
}
?>
