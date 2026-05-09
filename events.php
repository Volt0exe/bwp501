<?php
// events.php - صفحة قائمة الفعاليات
require_once 'db.php';

$category = $_GET['cat']   ?? '';
$search   = $_GET['search'] ?? '';
$date     = $_GET['date']   ?? '';

// Build query dynamically
$where  = [];
$params = [];

if ($category) {
    $where[]  = 'category = :cat';
    $params[':cat'] = $category;
}
if ($search) {
    $where[]  = '(title LIKE :s OR description LIKE :s2)';
    $params[':s']  = "%$search%";
    $params[':s2'] = "%$search%";
}
if ($date === 'upcoming') {
    $where[] = 'event_date >= CURDATE()';
} elseif ($date === 'past') {
    $where[] = 'event_date < CURDATE()';
} elseif ($date === 'thisweek') {
    $where[] = 'event_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)';
} elseif ($date === 'thismonth') {
    $where[] = 'MONTH(event_date) = MONTH(CURDATE()) AND YEAR(event_date) = YEAR(CURDATE())';
}

$sql = 'SELECT * FROM events';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY event_date ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

// All categories for filter bar
$catStmt   = $pdo->query("SELECT DISTINCT category FROM events");
$allCats   = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>الفعاليات - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
    <link rel="stylesheet" href="assets/css/styles.css"/>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-calendar-event-fill" style="color:var(--accent);font-size:1.5rem;"></i>
            دليل الفعاليات
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php">الرئيسية</a></li>
                <li class="nav-item"><a class="nav-link active" href="events.php">الفعاليات</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">عن الدليل</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">اتصل بنا</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <button id="darkToggle"><i class="bi bi-moon-fill"></i> داكن</button>
            </div>
        </div>
    </div>
</nav>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <h1><i class="bi bi-calendar3 me-2"></i>جميع الفعاليات</h1>
        <p>اكتشف الفعاليات الجامعية المتنوعة وشارك في أفضل التجارب الأكاديمية والثقافية والرياضية</p>
    </div>
</section>

<!-- Main Content -->
<section class="py-5">
    <div class="container">

        <!-- Search & Filter Box -->
        <div class="search-box">
            <form method="GET" action="events.php" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label"><i class="bi bi-search me-1"></i>بحث عن فعالية</label>
                    <input type="text" name="search" id="searchInput" class="form-control"
                           placeholder="ابحث بالعنوان..." value="<?= htmlspecialchars($search) ?>"/>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-tag me-1"></i>التصنيف</label>
                    <select name="cat" class="form-select">
                        <option value="">جميع التصنيفات</option>
                        <?php foreach($allCats as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"
                                <?= $category === $cat ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="bi bi-calendar me-1"></i>الفترة الزمنية</label>
                    <select name="date" id="dateFilter" class="form-select">
                        <option value="">جميع الأوقات</option>
                        <option value="upcoming" <?= $date==='upcoming' ? 'selected' : '' ?>>القادمة</option>
                        <option value="thisweek" <?= $date==='thisweek' ? 'selected' : '' ?>>هذا الأسبوع</option>
                        <option value="thismonth" <?= $date==='thismonth' ? 'selected' : '' ?>>هذا الشهر</option>
                        <option value="past"     <?= $date==='past'     ? 'selected' : '' ?>>السابقة</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom flex-fill">
                        <i class="bi bi-funnel"></i> فلترة
                    </button>
                    <a href="events.php" class="btn btn-outline-secondary" title="إعادة تعيين">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Category Quick Filters -->
        <div class="d-flex cat-filters mb-4">
            <button class="cat-btn active" data-cat="all">
                <i class="bi bi-grid me-1"></i>الكل (<?= count($events) ?>)
            </button>
            <?php
            $catGroups = [];
            foreach($events as $ev) {
                $c = $ev['category'];
                $catGroups[$c] = ($catGroups[$c] ?? 0) + 1;
            }
            foreach($catGroups as $c => $cnt): ?>
            <button class="cat-btn" data-cat="<?= htmlspecialchars($c) ?>">
                <?= htmlspecialchars($c) ?> (<?= $cnt ?>)
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Results Info -->
        <?php if ($search || $category || $date): ?>
        <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-info-circle-fill"></i>
            <span>تم العثور على <strong><?= count($events) ?></strong> فعالية
            <?php if($search) echo "تطابق: \"$search\""; ?>
            <?php if($category) echo " | التصنيف: $category"; ?>
            </span>
        </div>
        <?php endif; ?>

        <!-- Events Grid -->
        <div class="row g-4" id="eventsGrid">
            <?php foreach($events as $ev): ?>
            <div class="col-lg-4 col-md-6 event-item"
                 data-category="<?= htmlspecialchars($ev['category']) ?>"
                 data-title="<?= htmlspecialchars($ev['title']) ?>"
                 data-date="<?= $ev['event_date'] ?>">
                <div class="event-card h-100">
                    <img src="<?= htmlspecialchars($ev['image']) ?>"
                         class="card-img-top"
                         onerror="this.src='https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&h=300&fit=crop'"
                         alt="<?= htmlspecialchars($ev['title']) ?>"/>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-<?= getCategoryBadge($ev['category']) ?>">
                                <?= htmlspecialchars($ev['category']) ?>
                            </span>
                            <?php
                            $evDate = new DateTime($ev['event_date']);
                            $today  = new DateTime();
                            $isPast = $evDate < $today;
                            ?>
                            <?php if($isPast): ?>
                            <span class="badge bg-secondary">منتهية</span>
                            <?php else: ?>
                            <span class="badge bg-success">قادمة</span>
                            <?php endif; ?>
                        </div>
                        <h5 class="card-title"><?= htmlspecialchars($ev['title']) ?></h5>
                        <p class="card-text">
                            <?= mb_substr(htmlspecialchars($ev['description']), 0, 110) ?>...
                        </p>
                        <div class="event-meta">
                            <span><i class="bi bi-calendar3"></i><?= formatDate($ev['event_date']) ?></span>
                            <span><i class="bi bi-geo-alt"></i><?= htmlspecialchars($ev['location']) ?></span>
                        </div>
                        <a href="event.php?id=<?= $ev['id'] ?>"
                           class="btn btn-primary-custom mt-3 w-100">
                            <i class="bi bi-eye me-1"></i>التفاصيل الكاملة
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- No Events -->
        <div id="noEvents" class="no-events" style="display:<?= empty($events) ? 'block' : 'none' ?>;">
            <i class="bi bi-calendar-x"></i>
            <h4>لا توجد فعاليات مطابقة</h4>
            <p>جرّب تغيير معايير البحث أو <a href="events.php">عرض جميع الفعاليات</a></p>
        </div>

    </div>
</section>

<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5><i class="bi bi-calendar-event-fill me-2" style="color:var(--accent);"></i>دليل الفعاليات</h5>
                <p style="color:rgba(255,255,255,.65);font-size:.9rem;">منصة متخصصة لعرض فعاليات الجامعة الافتراضية السورية.</p>
                <div class="social-icons">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-twitter-x"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <h5>التصنيفات</h5>
                <ul class="list-unstyled" style="line-height:2;">
                    <?php foreach($allCats as $c): ?>
                    <li><a href="events.php?cat=<?= urlencode($c) ?>">
                        <i class="bi bi-chevron-left me-1"></i><?= htmlspecialchars($c) ?>
                    </a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>تواصل معنا</h5>
                <ul class="list-unstyled" style="line-height:2.2;font-size:.92rem;">
                    <li><i class="bi bi-envelope me-2" style="color:var(--accent);"></i>events@svuonline.org</li>
                    <li><i class="bi bi-geo-alt me-2" style="color:var(--accent);"></i>دمشق، سوريا</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?= date('Y') ?> دليل فعاليات الجامعة الافتراضية السورية &mdash; جميع الحقوق محفوظة
        </div>
    </div>
</footer>

<button id="scrollTop" title="العودة للأعلى"><i class="bi bi-arrow-up"></i></button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
