<?php
// index.php - الصفحة الرئيسية
require_once 'db.php';

// Fetch featured events (3 for slider)
$sliderStmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC LIMIT 3");
$sliderEvents = $sliderStmt->fetchAll();

// Fetch latest 6 events
$latestStmt = $pdo->query("SELECT * FROM events ORDER BY created_at DESC LIMIT 6");
$latestEvents = $latestStmt->fetchAll();

// Distinct categories
$catStmt = $pdo->query("SELECT DISTINCT category FROM events");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>الصفحة الرئيسية - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
    <link rel="stylesheet" href="assets/css/styles.css"/>
</head>
<body>

<!-- ══ Navbar ══ -->
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
                <li class="nav-item"><a class="nav-link active" href="index.php">الرئيسية</a></li>
                <li class="nav-item"><a class="nav-link" href="events.php">الفعاليات</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">عن الدليل</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">اتصل بنا</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <button id="darkToggle"><i class="bi bi-moon-fill"></i> داكن</button>
                <a href="admin/login.php" class="btn btn-sm btn-warning fw-bold">
                    <i class="bi bi-shield-lock me-1"></i>لوحة التحكم
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- ══ Hero Carousel ══ -->
<?php if (!empty($sliderEvents)): ?>
<div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="4000">
    <div class="carousel-indicators">
        <?php foreach($sliderEvents as $i => $ev): ?>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>"
                <?= $i === 0 ? 'class="active"' : '' ?>></button>
        <?php endforeach; ?>
    </div>
    <div class="carousel-inner">
        <?php foreach($sliderEvents as $i => $ev): ?>
        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
            <img src="<?= htmlspecialchars($ev['image']) ?>"
                 onerror="this.src='https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=1200&h=500&fit=crop'"
                 alt="<?= htmlspecialchars($ev['title']) ?>"/>
            <div class="carousel-caption-custom">
                <span class="badge bg-<?= getCategoryBadge($ev['category']) ?> mb-2"><?= htmlspecialchars($ev['category']) ?></span>
                <h2><?= htmlspecialchars($ev['title']) ?></h2>
                <p><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($ev['location']) ?>
                   &nbsp;|&nbsp;<i class="bi bi-calendar3 me-1"></i><?= formatDate($ev['event_date']) ?></p>
                <a href="event.php?id=<?= $ev['id'] ?>" class="btn btn-warning btn-sm fw-bold mt-1">
                    التفاصيل <i class="bi bi-arrow-left ms-1"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>
<?php endif; ?>

<!-- ══ Quick Categories ══ -->
<section class="py-4 bg-body-secondary">
    <div class="container">
        <div class="d-flex justify-content-center cat-filters">
            <a href="events.php" class="cat-btn">
                <i class="bi bi-grid me-1"></i>الكل
            </a>
            <a href="events.php?cat=ثقافة" class="cat-btn">
                <i class="bi bi-book me-1"></i>ثقافة
            </a>
            <a href="events.php?cat=رياضة" class="cat-btn">
                <i class="bi bi-trophy me-1"></i>رياضة
            </a>
            <a href="events.php?cat=موسيقى" class="cat-btn">
                <i class="bi bi-music-note-beamed me-1"></i>موسيقى
            </a>
            <a href="events.php?cat=عائلي" class="cat-btn">
                <i class="bi bi-people me-1"></i>عائلي
            </a>
        </div>
    </div>
</section>

<!-- ══ Latest Events ══ -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">أحدث الفعاليات</h2>
            <a href="events.php" class="btn btn-outline-primary">عرض الكل <i class="bi bi-arrow-left ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach($latestEvents as $ev): ?>
            <div class="col-lg-4 col-md-6">
                <div class="event-card h-100">
                    <img src="<?= htmlspecialchars($ev['image']) ?>"
                         class="card-img-top"
                         onerror="this.src='https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&h=300&fit=crop'"
                         alt="<?= htmlspecialchars($ev['title']) ?>"/>
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-<?= getCategoryBadge($ev['category']) ?>">
                                <?= htmlspecialchars($ev['category']) ?>
                            </span>
                        </div>
                        <h5 class="card-title"><?= htmlspecialchars($ev['title']) ?></h5>
                        <p class="card-text">
                            <?= mb_substr(htmlspecialchars($ev['description']), 0, 100) ?>...
                        </p>
                        <div class="event-meta">
                            <span><i class="bi bi-calendar3"></i><?= formatDate($ev['event_date']) ?></span>
                            <span><i class="bi bi-geo-alt"></i><?= htmlspecialchars($ev['location']) ?></span>
                        </div>
                        <a href="event.php?id=<?= $ev['id'] ?>"
                           class="btn btn-primary-custom mt-3 w-100">
                            <i class="bi bi-eye me-1"></i>التفاصيل
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($latestEvents)): ?>
            <div class="col-12 no-events">
                <i class="bi bi-calendar-x"></i>
                <p>لا توجد فعاليات حالياً</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ══ Stats Banner ══ -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);">
    <div class="container">
        <div class="row text-center text-white g-4">
            <?php
            $totalEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
            $upcomingCount = $pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn();
            $catCount = $pdo->query("SELECT COUNT(DISTINCT category) FROM events")->fetchColumn();
            ?>
            <div class="col-md-4">
                <div style="font-size:2.5rem;font-weight:800;"><?= $totalEvents ?></div>
                <div style="opacity:.85;font-size:1rem;">إجمالي الفعاليات</div>
            </div>
            <div class="col-md-4">
                <div style="font-size:2.5rem;font-weight:800;"><?= $upcomingCount ?></div>
                <div style="opacity:.85;font-size:1rem;">فعالية قادمة</div>
            </div>
            <div class="col-md-4">
                <div style="font-size:2.5rem;font-weight:800;"><?= $catCount ?></div>
                <div style="opacity:.85;font-size:1rem;">تصنيف</div>
            </div>
        </div>
    </div>
</section>

<!-- ══ Footer ══ -->
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5><i class="bi bi-calendar-event-fill me-2" style="color:var(--accent);"></i>دليل الفعاليات</h5>
                <p style="color:rgba(255,255,255,.65);font-size:.9rem;">
                    منصة متخصصة لعرض وإدارة فعاليات الجامعة الافتراضية السورية،
                    نُقرّب المسافة بين الطلاب والأنشطة الجامعية.
                </p>
                <div class="social-icons">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-twitter-x"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <h5>روابط سريعة</h5>
                <ul class="list-unstyled" style="line-height:2;">
                    <li><a href="index.php"><i class="bi bi-chevron-left me-1"></i>الرئيسية</a></li>
                    <li><a href="events.php"><i class="bi bi-chevron-left me-1"></i>الفعاليات</a></li>
                    <li><a href="about.php"><i class="bi bi-chevron-left me-1"></i>عن الدليل</a></li>
                    <li><a href="contact.php"><i class="bi bi-chevron-left me-1"></i>اتصل بنا</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>تواصل معنا</h5>
                <ul class="list-unstyled" style="line-height:2.2;font-size:.92rem;">
                    <li><i class="bi bi-envelope me-2" style="color:var(--accent);"></i>events@svuonline.org</li>
                    <li><i class="bi bi-telephone me-2" style="color:var(--accent);"></i>+963 11 xxx xxxx</li>
                    <li><i class="bi bi-geo-alt me-2" style="color:var(--accent);"></i>دمشق، سوريا</li>
                    <li><i class="bi bi-clock me-2" style="color:var(--accent);"></i>السبت - الخميس، 8ص - 4م</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?= date('Y') ?> دليل فعاليات الجامعة الافتراضية السورية &mdash; جميع الحقوق محفوظة
        </div>
    </div>
</footer>

<!-- Scroll to Top -->
<button id="scrollTop" title="العودة للأعلى"><i class="bi bi-arrow-up"></i></button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
