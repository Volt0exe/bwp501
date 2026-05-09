<?php
// event.php - صفحة تفاصيل الفعالية
require_once 'db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: events.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) { header('Location: events.php'); exit; }

// Related events (same category, excluding current)
$relStmt = $pdo->prepare(
    "SELECT * FROM events WHERE category = ? AND id != ? ORDER BY event_date ASC LIMIT 3"
);
$relStmt->execute([$event['category'], $id]);
$related = $relStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($event['title']) ?> - <?= SITE_NAME ?></title>
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
            <button id="darkToggle"><i class="bi bi-moon-fill"></i> داكن</button>
        </div>
    </div>
</nav>

<!-- Breadcrumb -->
<div class="container py-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="events.php">الفعاليات</a></li>
            <li class="breadcrumb-item active"><?= mb_substr(htmlspecialchars($event['title']), 0, 40) ?>...</li>
        </ol>
    </nav>
</div>

<!-- Event Detail -->
<section class="pb-5">
    <div class="container">
        <div class="row g-5">

            <!-- Left: Image + Description -->
            <div class="col-lg-8">
                <img src="<?= htmlspecialchars($event['image']) ?>"
                     class="event-detail-img mb-4"
                     onerror="this.src='https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=800&h=400&fit=crop'"
                     alt="<?= htmlspecialchars($event['title']) ?>"/>

                <div class="mb-3">
                    <span class="badge bg-<?= getCategoryBadge($event['category']) ?> fs-6 me-2">
                        <?= htmlspecialchars($event['category']) ?>
                    </span>
                    <?php
                    $evDate = new DateTime($event['event_date']);
                    $today  = new DateTime();
                    $isPast = $evDate < $today;
                    ?>
                    <span class="badge bg-<?= $isPast ? 'secondary' : 'success' ?> fs-6">
                        <?= $isPast ? 'منتهية' : 'قادمة' ?>
                    </span>
                </div>

                <h1 style="font-weight:800;font-size:1.9rem;color:var(--text);margin-bottom:1rem;">
                    <?= htmlspecialchars($event['title']) ?>
                </h1>

                <div style="color:var(--text-muted);font-size:.9rem;margin-bottom:1.5rem;">
                    <i class="bi bi-clock me-1"></i>
                    نُشر في: <?= date('d/m/Y', strtotime($event['created_at'])) ?>
                </div>

                <div style="font-size:1.05rem;line-height:1.9;color:var(--text);">
                    <?= nl2br(htmlspecialchars($event['description'])) ?>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <button id="addCalendar" class="btn btn-accent"
                            data-title="<?= htmlspecialchars($event['title']) ?>"
                            data-date="<?= $event['event_date'] ?>"
                            data-location="<?= htmlspecialchars($event['location']) ?>">
                        <i class="bi bi-calendar-plus me-2"></i>أضف للتقويم
                    </button>
                    <button id="shareBtn" class="btn btn-outline-primary">
                        <i class="bi bi-share me-2"></i>شارك
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bookingModal">
                        <i class="bi bi-ticket-perforated me-2"></i>احجز مقعدك
                    </button>
                    <a href="events.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-right me-2"></i>العودة للفعاليات
                    </a>
                </div>
            </div>

            <!-- Right: Meta Box -->
            <div class="col-lg-4">
                <div class="detail-meta-box">
                    <h5 style="font-weight:700;color:var(--primary);margin-bottom:1.2rem;">
                        <i class="bi bi-info-circle me-2"></i>معلومات الفعالية
                    </h5>

                    <div class="detail-meta-row">
                        <i class="bi bi-card-heading"></i>
                        <div>
                            <div style="font-size:.78rem;color:var(--text-muted);">العنوان</div>
                            <div style="font-weight:600;"><?= htmlspecialchars($event['title']) ?></div>
                        </div>
                    </div>

                    <div class="detail-meta-row">
                        <i class="bi bi-calendar3"></i>
                        <div>
                            <div style="font-size:.78rem;color:var(--text-muted);">التاريخ</div>
                            <div style="font-weight:600;"><?= formatDate($event['event_date']) ?></div>
                        </div>
                    </div>

                    <div class="detail-meta-row">
                        <i class="bi bi-geo-alt-fill"></i>
                        <div>
                            <div style="font-size:.78rem;color:var(--text-muted);">المكان</div>
                            <div style="font-weight:600;"><?= htmlspecialchars($event['location']) ?></div>
                        </div>
                    </div>

                    <div class="detail-meta-row">
                        <i class="bi bi-tag-fill"></i>
                        <div>
                            <div style="font-size:.78rem;color:var(--text-muted);">التصنيف</div>
                            <div>
                                <span class="badge bg-<?= getCategoryBadge($event['category']) ?>">
                                    <?= htmlspecialchars($event['category']) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="detail-meta-row">
                        <i class="bi bi-hourglass-split"></i>
                        <div>
                            <div style="font-size:.78rem;color:var(--text-muted);">الحالة</div>
                            <div style="font-weight:600;color:var(--<?= $isPast ? 'text-muted' : 'primary' ?>);">
                                <?php if($isPast): ?>
                                <span style="color:#6c757d;">منتهية</span>
                                <?php else:
                                    $diff = $today->diff($evDate)->days;
                                ?>
                                <span style="color:var(--primary);">
                                    <?= $diff > 0 ? "بعد $diff يوم" : "اليوم!" ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Share Box -->
                <div class="detail-meta-box mt-4">
                    <h6 style="font-weight:700;margin-bottom:1rem;">
                        <i class="bi bi-share me-2"></i>شارك الفعالية
                    </h6>
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>"
                           target="_blank" class="btn btn-sm" style="background:#1877f2;color:#fff;">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode($event['title']) ?>&url=<?= urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>"
                           target="_blank" class="btn btn-sm" style="background:#1da1f2;color:#fff;">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                        <a href="https://wa.me/?text=<?= urlencode($event['title'].' - '.'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>"
                           target="_blank" class="btn btn-sm" style="background:#25d366;color:#fff;">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Events -->
        <?php if (!empty($related)): ?>
        <div class="mt-5">
            <h3 class="section-title">فعاليات ذات صلة</h3>
            <div class="row g-4">
                <?php foreach($related as $rel): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="event-card h-100">
                        <img src="<?= htmlspecialchars($rel['image']) ?>"
                             class="card-img-top"
                             onerror="this.src='https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&h=300&fit=crop'"
                             alt="<?= htmlspecialchars($rel['title']) ?>"/>
                        <div class="card-body">
                            <span class="badge bg-<?= getCategoryBadge($rel['category']) ?> mb-2">
                                <?= htmlspecialchars($rel['category']) ?>
                            </span>
                            <h5 class="card-title"><?= htmlspecialchars($rel['title']) ?></h5>
                            <div class="event-meta">
                                <span><i class="bi bi-calendar3"></i><?= formatDate($rel['event_date']) ?></span>
                                <span><i class="bi bi-geo-alt"></i><?= htmlspecialchars($rel['location']) ?></span>
                            </div>
                            <a href="event.php?id=<?= $rel['id'] ?>"
                               class="btn btn-primary-custom mt-3 w-100">
                                <i class="bi bi-eye me-1"></i>التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-ticket-perforated me-2"></i>حجز مقعد
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong><?= htmlspecialchars($event['title']) ?></strong><br/>
                    <small><i class="bi bi-calendar3 me-1"></i><?= formatDate($event['event_date']) ?></small><br/>
                    <small><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($event['location']) ?></small>
                </div>
                <div class="mb-3">
                    <label class="form-label">الاسم الكامل</label>
                    <input type="text" class="form-control" placeholder="أدخل اسمك الكامل"/>
                </div>
                <div class="mb-3">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control" placeholder="example@email.com"/>
                </div>
                <div class="mb-3">
                    <label class="form-label">عدد المقاعد</label>
                    <select class="form-select">
                        <option>1</option><option>2</option>
                        <option>3</option><option>4</option><option>5</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success fw-bold" onclick="
                    this.innerHTML='<i class=\'bi bi-check-circle me-1\'></i>تم الحجز!';
                    this.disabled=true;
                    setTimeout(()=>bootstrap.Modal.getInstance(document.getElementById(\'bookingModal\')).hide(),1500);
                ">
                    <i class="bi bi-check-circle me-1"></i>تأكيد الحجز
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="site-footer">
    <div class="container">
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
