<?php
// admin/dashboard.php - لوحة التحكم الرئيسية
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../db.php';

// Stats
$totalEvents    = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$upcomingEvents = $pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn();
$pastEvents     = $pdo->query("SELECT COUNT(*) FROM events WHERE event_date < CURDATE()")->fetchColumn();
$catCount       = $pdo->query("SELECT COUNT(DISTINCT category) FROM events")->fetchColumn();

// All events
$events = $pdo->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();

// Flash message
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>لوحة التحكم - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
    <link rel="stylesheet" href="../assets/css/styles.css"/>
</head>
<body>
<div class="d-flex" style="min-height:100vh;">

    <!-- ═══ Sidebar ═══ -->
    <div class="admin-sidebar d-none d-lg-flex flex-column" style="width:260px;flex-shrink:0;">
        <div class="sidebar-brand">
            <i class="bi bi-calendar-event-fill" style="color:var(--accent);font-size:1.4rem;"></i>
            لوحة التحكم
        </div>
        <nav class="nav flex-column mt-2 flex-grow-1">
            <a class="nav-link active" href="dashboard.php">
                <i class="bi bi-grid-1x2-fill"></i> لوحة البيانات
            </a>
            <a class="nav-link" href="add_event.php">
                <i class="bi bi-plus-circle-fill"></i> إضافة فعالية
            </a>
            <a class="nav-link" href="../index.php" target="_blank">
                <i class="bi bi-globe"></i> عرض الموقع
            </a>
            <hr style="border-color:rgba(255,255,255,.15);margin:.5rem 1rem;"/>
            <a class="nav-link" href="../events.php" target="_blank">
                <i class="bi bi-calendar3"></i> صفحة الفعاليات
            </a>
        </nav>
        <div class="p-3" style="border-top:1px solid rgba(255,255,255,.1);">
            <div style="color:rgba(255,255,255,.7);font-size:.85rem;margin-bottom:.5rem;">
                <i class="bi bi-person-circle me-1"></i>
                مرحباً، <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>
            </div>
            <a href="logout.php" class="btn btn-sm btn-danger w-100">
                <i class="bi bi-box-arrow-right me-1"></i>تسجيل الخروج
            </a>
        </div>
    </div>

    <!-- ═══ Main Content ═══ -->
    <div class="flex-grow-1">

        <!-- Top Bar -->
        <div class="admin-topbar">
            <h4><i class="bi bi-grid-1x2 me-2"></i>لوحة البيانات</h4>
            <div class="d-flex align-items-center gap-3">
                <button id="darkToggle" style="background:var(--bg);border:1px solid var(--border);color:var(--text);border-radius:20px;padding:.3rem .8rem;font-size:.85rem;cursor:pointer;">
                    <i class="bi bi-moon-fill"></i> داكن
                </button>
                <a href="logout.php" class="btn btn-sm btn-outline-danger d-lg-none">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="admin-main">

            <!-- Flash Message -->
            <?php if($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show d-flex gap-2 mb-4">
                <i class="bi bi-<?= $flash['type']==='success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
                <?= htmlspecialchars($flash['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(37,99,168,.12);">
                            <i class="bi bi-calendar3" style="color:var(--primary);"></i>
                        </div>
                        <div>
                            <div class="stat-num"><?= $totalEvents ?></div>
                            <div class="stat-label">إجمالي الفعاليات</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(34,197,94,.12);">
                            <i class="bi bi-calendar-check-fill" style="color:#22c55e;"></i>
                        </div>
                        <div>
                            <div class="stat-num"><?= $upcomingEvents ?></div>
                            <div class="stat-label">فعاليات قادمة</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(107,114,128,.12);">
                            <i class="bi bi-calendar-x-fill" style="color:#6b7280;"></i>
                        </div>
                        <div>
                            <div class="stat-num"><?= $pastEvents ?></div>
                            <div class="stat-label">فعاليات منتهية</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(234,160,32,.12);">
                            <i class="bi bi-tags-fill" style="color:var(--accent);"></i>
                        </div>
                        <div>
                            <div class="stat-num"><?= $catCount ?></div>
                            <div class="stat-label">تصنيفات</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Events Table -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="font-weight:700;color:var(--text);margin:0;">
                    <i class="bi bi-table me-2"></i>قائمة الفعاليات
                </h5>
                <a href="add_event.php" class="btn btn-primary-custom">
                    <i class="bi bi-plus-lg me-1"></i>إضافة فعالية جديدة
                </a>
            </div>

            <div class="table-custom">
                <div style="overflow-x:auto;">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الصورة</th>
                                <th>العنوان</th>
                                <th>التصنيف</th>
                                <th>المكان</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($events)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-calendar-x" style="font-size:2rem;display:block;margin-bottom:.5rem;color:var(--text-muted);"></i>
                                    لا توجد فعاليات. <a href="add_event.php">أضف فعالية الآن</a>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($events as $i => $ev): ?>
                            <?php
                                $evDate = new DateTime($ev['event_date']);
                                $today  = new DateTime();
                                $isPast = $evDate < $today;
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($ev['image']) ?>"
                                         class="img-event-thumb"
                                         onerror="this.src='https://via.placeholder.com/60x45?text=SVU'"
                                         alt="<?= htmlspecialchars($ev['title']) ?>"/>
                                </td>
                                <td style="max-width:200px;">
                                    <strong><?= htmlspecialchars(mb_substr($ev['title'], 0, 40)) ?>
                                    <?= mb_strlen($ev['title']) > 40 ? '...' : '' ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?= getCategoryBadge($ev['category']) ?>">
                                        <?= htmlspecialchars($ev['category']) ?>
                                    </span>
                                </td>
                                <td style="font-size:.87rem;">
                                    <?= htmlspecialchars(mb_substr($ev['location'], 0, 30)) ?>
                                </td>
                                <td style="font-size:.87rem;white-space:nowrap;">
                                    <?= date('Y-m-d', strtotime($ev['event_date'])) ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $isPast ? 'secondary' : 'success' ?>">
                                        <?= $isPast ? 'منتهية' : 'قادمة' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="../event.php?id=<?= $ev['id'] ?>" target="_blank"
                                           class="btn btn-sm btn-outline-primary" title="عرض">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit_event.php?id=<?= $ev['id'] ?>"
                                           class="btn btn-sm btn-warning" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete_event.php?id=<?= $ev['id'] ?>"
                                           class="btn btn-sm btn-danger" title="حذف"
                                           onclick="return confirm('هل أنت متأكد من حذف هذه الفعالية؟')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /admin-main -->
    </div><!-- /flex-grow-1 -->
</div><!-- /d-flex -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
