<?php
// admin/delete_event.php - حذف فعالية مع صفحة تأكيد
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

// جلب الفعالية
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'الفعالية غير موجودة أو تم حذفها مسبقاً.'];
    header('Location: dashboard.php');
    exit;
}

// ── تنفيذ الحذف عند الضغط على "تأكيد الحذف" ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $del = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $del->execute([$id]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'msg'  => 'تم حذف الفعالية "' . $event['title'] . '" بنجاح.',
    ];
    header('Location: dashboard.php');
    exit;
}

// إحصاء الفعاليات المتبقية
$totalAfter = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn() - 1;
$evDate     = new DateTime($event['event_date']);
$today      = new DateTime();
$isPast     = $evDate < $today;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>حذف الفعالية #<?= $id ?> - لوحة التحكم</title>
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
            <a class="nav-link" href="dashboard.php">
                <i class="bi bi-grid-1x2-fill"></i> لوحة البيانات
            </a>
            <a class="nav-link" href="add_event.php">
                <i class="bi bi-plus-circle-fill"></i> إضافة فعالية
            </a>
            <a class="nav-link" href="../index.php" target="_blank">
                <i class="bi bi-globe"></i> عرض الموقع
            </a>
            <hr style="border-color:rgba(255,255,255,.15);margin:.5rem 1rem;"/>
            <a class="nav-link active" href="#">
                <i class="bi bi-trash-fill"></i> حذف فعالية
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
            <div class="d-flex align-items-center gap-2">
                <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-right"></i>
                </a>
                <h4 class="mb-0" style="color:#dc3545;">
                    <i class="bi bi-trash me-2"></i>
                    حذف الفعالية
                    <span class="badge bg-danger ms-2" style="font-size:.75rem;">#<?= $id ?></span>
                </h4>
            </div>
            <button id="darkToggle" style="background:var(--bg);border:1px solid var(--border);color:var(--text);border-radius:20px;padding:.3rem .8rem;font-size:.85rem;cursor:pointer;">
                <i class="bi bi-moon-fill"></i> داكن
            </button>
        </div>

        <div class="admin-main">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-10">

                    <!-- ── Warning Banner ── -->
                    <div class="alert alert-danger d-flex gap-3 align-items-start mb-4"
                         style="border-radius:14px;font-size:1rem;">
                        <i class="bi bi-exclamation-octagon-fill"
                           style="font-size:2rem;flex-shrink:0;margin-top:.1rem;"></i>
                        <div>
                            <strong style="font-size:1.1rem;">تحذير: هذا الإجراء لا يمكن التراجع عنه!</strong>
                            <p class="mb-0 mt-1" style="opacity:.9;">
                                سيتم حذف هذه الفعالية نهائياً من قاعدة البيانات.
                                لن تتمكن من استعادتها لاحقاً.
                            </p>
                        </div>
                    </div>

                    <!-- ── Event Preview Card ── -->
                    <div class="contact-form-box mb-4">
                        <h5 style="font-weight:700;color:var(--text);margin-bottom:1.2rem;padding-bottom:.8rem;border-bottom:2px solid var(--border);">
                            <i class="bi bi-calendar-event me-2" style="color:var(--primary);"></i>
                            الفعالية المراد حذفها
                        </h5>

                        <div class="row g-3 align-items-start">
                            <div class="col-md-4">
                                <img src="<?= htmlspecialchars($event['image']) ?>"
                                     onerror="this.src='https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=400&h=250&fit=crop'"
                                     style="width:100%;height:160px;object-fit:cover;border-radius:10px;"
                                     alt="<?= htmlspecialchars($event['title']) ?>"/>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-2">
                                    <span class="badge bg-<?= getCategoryBadge($event['category']) ?> me-2">
                                        <?= htmlspecialchars($event['category']) ?>
                                    </span>
                                    <span class="badge bg-<?= $isPast ? 'secondary' : 'success' ?>">
                                        <?= $isPast ? 'منتهية' : 'قادمة' ?>
                                    </span>
                                </div>
                                <h5 style="font-weight:700;color:var(--text);">
                                    <?= htmlspecialchars($event['title']) ?>
                                </h5>
                                <p style="color:var(--text-muted);font-size:.9rem;margin-bottom:.8rem;">
                                    <?= htmlspecialchars(mb_substr($event['description'], 0, 150)) ?>...
                                </p>
                                <div style="font-size:.88rem;display:flex;flex-direction:column;gap:.3rem;">
                                    <span>
                                        <i class="bi bi-calendar3 me-2" style="color:var(--accent);"></i>
                                        <strong>التاريخ:</strong> <?= formatDate($event['event_date']) ?>
                                    </span>
                                    <span>
                                        <i class="bi bi-geo-alt me-2" style="color:var(--accent);"></i>
                                        <strong>المكان:</strong> <?= htmlspecialchars($event['location']) ?>
                                    </span>
                                    <span>
                                        <i class="bi bi-hash me-2" style="color:var(--accent);"></i>
                                        <strong>المعرّف:</strong> #<?= $event['id'] ?>
                                    </span>
                                    <span>
                                        <i class="bi bi-clock me-2" style="color:var(--accent);"></i>
                                        <strong>أُضيفت في:</strong>
                                        <?= date('d/m/Y H:i', strtotime($event['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Impact Info ── -->
                    <div class="alert alert-warning d-flex gap-2 align-items-start mb-4">
                        <i class="bi bi-info-circle-fill mt-1"></i>
                        <div style="font-size:.9rem;">
                            <strong>تأثير الحذف:</strong>
                            <ul class="mb-0 mt-1">
                                <li>ستصبح جميع روابط هذه الفعالية غير صالحة.</li>
                                <li>سيتبقى <strong><?= $totalAfter ?></strong> فعالية في النظام بعد الحذف.</li>
                                <li>إذا كنت غير متأكد، يمكنك <a href="edit_event.php?id=<?= $id ?>">تعديل الفعالية</a> بدلاً من حذفها.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- ── Confirmation Form ── -->
                    <div class="contact-form-box">
                        <h5 style="font-weight:700;color:#dc3545;margin-bottom:1.2rem;">
                            <i class="bi bi-shield-exclamation me-2"></i>تأكيد الحذف
                        </h5>

                        <form method="POST" action="" id="deleteForm">

                            <!-- Checkbox التأكيد -->
                            <div class="mb-4">
                                <div class="form-check"
                                     style="background:rgba(220,53,69,.07);border:1px solid rgba(220,53,69,.3);border-radius:10px;padding:1rem 1rem 1rem 2.5rem;">
                                    <input class="form-check-input" type="checkbox" id="confirmCheck"
                                           style="border-color:#dc3545;"/>
                                    <label class="form-check-label fw-bold" for="confirmCheck"
                                           style="color:#dc3545;cursor:pointer;">
                                        نعم، أؤكد أنني أريد حذف هذه الفعالية نهائياً ولا يمكن التراجع عن ذلك
                                    </label>
                                </div>
                            </div>

                            <!-- Countdown + Buttons -->
                            <div class="d-flex gap-3 flex-wrap align-items-center">
                                <button type="submit" name="confirm_delete" value="1"
                                        id="deleteBtn"
                                        class="btn btn-danger fw-bold px-4 py-2"
                                        disabled>
                                    <i class="bi bi-trash me-2"></i>
                                    تأكيد الحذف النهائي
                                    <span id="countdown" class="ms-1 badge bg-light text-danger"
                                          style="display:none;"></span>
                                </button>

                                <a href="dashboard.php"
                                   class="btn btn-outline-secondary px-4 py-2">
                                    <i class="bi bi-x-lg me-1"></i>إلغاء
                                </a>

                                <a href="edit_event.php?id=<?= $id ?>"
                                   class="btn btn-warning px-4 py-2">
                                    <i class="bi bi-pencil me-1"></i>تعديل بدلاً من الحذف
                                </a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
<script>
(function() {
    const checkbox    = document.getElementById('confirmCheck');
    const deleteBtn   = document.getElementById('deleteBtn');
    const countdownEl = document.getElementById('countdown');
    let timer = null;
    let secs  = 3;

    checkbox.addEventListener('change', function() {
        if (this.checked) {
            // Start 3-second countdown before enabling
            secs = 3;
            countdownEl.style.display = 'inline';
            countdownEl.textContent   = secs + 's';
            deleteBtn.disabled = true;

            timer = setInterval(() => {
                secs--;
                if (secs > 0) {
                    countdownEl.textContent = secs + 's';
                } else {
                    clearInterval(timer);
                    countdownEl.style.display = 'none';
                    deleteBtn.disabled = false;
                    deleteBtn.classList.add('btn-danger');
                }
            }, 1000);
        } else {
            clearInterval(timer);
            countdownEl.style.display = 'none';
            deleteBtn.disabled = true;
        }
    });

    // Extra confirmation on submit
    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        if (!checkbox.checked) {
            e.preventDefault();
            return;
        }
        if (!confirm('آخر تحذير!\n\nهل أنت متأكد تماماً من حذف الفعالية:\n"<?= addslashes($event['title']) ?>"\n\nهذا الإجراء لا يمكن التراجع عنه.')) {
            e.preventDefault();
        }
    });
})();
</script>
</body>
</html>
