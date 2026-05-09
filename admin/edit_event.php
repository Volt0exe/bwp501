<?php
// admin/edit_event.php - تعديل فعالية موجودة
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

// جلب الفعالية من القاعدة
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'الفعالية غير موجودة.'];
    header('Location: dashboard.php');
    exit;
}

$errors  = [];
$input   = $event; // القيم الأصلية كقيمة ابتدائية

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['title']       = trim($_POST['title']       ?? '');
    $input['description'] = trim($_POST['description'] ?? '');
    $input['category']    = trim($_POST['category']    ?? '');
    $input['location']    = trim($_POST['location']    ?? '');
    $input['event_date']  = trim($_POST['event_date']  ?? '');
    $input['image']       = trim($_POST['image']       ?? '');

    // التحقق
    if (empty($input['title']))       $errors['title']       = 'عنوان الفعالية مطلوب';
    if (empty($input['description'])) $errors['description'] = 'وصف الفعالية مطلوب';
    if (empty($input['category']))    $errors['category']    = 'التصنيف مطلوب';
    if (empty($input['location']))    $errors['location']    = 'مكان الفعالية مطلوب';
    if (empty($input['event_date'])) {
        $errors['event_date'] = 'تاريخ الفعالية مطلوب';
    } elseif (!strtotime($input['event_date'])) {
        $errors['event_date'] = 'صيغة التاريخ غير صحيحة';
    }

    // إذا فُرِّغ رابط الصورة — نحتفظ بالصورة القديمة
    if (empty($input['image'])) {
        $input['image'] = $event['image'];
    }

    if (empty($errors)) {
        $upd = $pdo->prepare(
            "UPDATE events
             SET title=:title, description=:description, category=:category,
                 location=:location, event_date=:event_date, image=:image
             WHERE id=:id"
        );
        $upd->execute([
            ':title'       => $input['title'],
            ':description' => $input['description'],
            ':category'    => $input['category'],
            ':location'    => $input['location'],
            ':event_date'  => $input['event_date'],
            ':image'       => $input['image'],
            ':id'          => $id,
        ]);

        $_SESSION['flash'] = [
            'type' => 'success',
            'msg'  => 'تم تحديث الفعالية "' . $input['title'] . '" بنجاح!',
        ];
        header('Location: dashboard.php');
        exit;
    }
}

$categories = ['ثقافة', 'رياضة', 'موسيقى', 'عائلي', 'علوم', 'أخرى'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>تعديل فعالية #<?= $id ?> - لوحة التحكم</title>
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
                <i class="bi bi-pencil-fill"></i> تعديل فعالية
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
                <h4 class="mb-0">
                    <i class="bi bi-pencil-square me-2"></i>
                    تعديل الفعالية
                    <span class="badge bg-secondary ms-2" style="font-size:.75rem;">#<?= $id ?></span>
                </h4>
            </div>
            <button id="darkToggle" style="background:var(--bg);border:1px solid var(--border);color:var(--text);border-radius:20px;padding:.3rem .8rem;font-size:.85rem;cursor:pointer;">
                <i class="bi bi-moon-fill"></i> داكن
            </button>
        </div>

        <div class="admin-main">

            <!-- Errors -->
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger d-flex gap-2 align-items-start mb-4">
                <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                <div>
                    <strong>يوجد <?= count($errors) ?> خطأ — يرجى مراجعة الحقول المحددة بالأحمر:</strong>
                    <ul class="mb-0 mt-1">
                        <?php foreach($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <!-- Info Banner -->
            <div class="alert alert-warning d-flex gap-2 align-items-center mb-4">
                <i class="bi bi-pencil-square"></i>
                <div>
                    تقوم بتعديل الفعالية: <strong>"<?= htmlspecialchars($event['title']) ?>"</strong>
                    &nbsp;|&nbsp; أُضيفت في: <?= date('d/m/Y', strtotime($event['created_at'])) ?>
                    &nbsp;|&nbsp;
                    <a href="../event.php?id=<?= $id ?>" target="_blank" class="fw-bold">
                        <i class="bi bi-eye me-1"></i>عرض في الموقع
                    </a>
                </div>
            </div>

            <div class="row g-4">

                <!-- Form -->
                <div class="col-lg-8">
                    <div class="contact-form-box">
                        <h5 style="font-weight:700;color:var(--primary);margin-bottom:1.5rem;padding-bottom:.8rem;border-bottom:2px solid var(--border);">
                            <i class="bi bi-card-text me-2"></i>بيانات الفعالية
                        </h5>

                        <form method="POST" action="" id="editForm" novalidate>

                            <!-- العنوان -->
                            <div class="mb-3">
                                <label class="form-label">
                                    عنوان الفعالية <span style="color:red;">*</span>
                                </label>
                                <input type="text" name="title" id="f_title"
                                       class="form-control <?= isset($errors['title']) ? 'is-invalid' : 'is-valid' ?>"
                                       value="<?= htmlspecialchars($input['title']) ?>"
                                       placeholder="عنوان الفعالية" maxlength="255" required/>
                                <?php if(isset($errors['title'])): ?>
                                <div class="invalid-feedback"><?= $errors['title'] ?></div>
                                <?php endif; ?>
                                <div class="form-text">
                                    <span id="titleCount"><?= mb_strlen($input['title']) ?></span> / 255 حرف
                                </div>
                            </div>

                            <!-- الوصف -->
                            <div class="mb-3">
                                <label class="form-label">
                                    وصف الفعالية <span style="color:red;">*</span>
                                </label>
                                <textarea name="description" id="f_desc" rows="6"
                                          class="form-control <?= isset($errors['description']) ? 'is-invalid' : 'is-valid' ?>"
                                          required><?= htmlspecialchars($input['description']) ?></textarea>
                                <?php if(isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?= $errors['description'] ?></div>
                                <?php endif; ?>
                                <div class="form-text">
                                    <span id="descCount"><?= mb_strlen($input['description']) ?></span> حرف مُدخل
                                </div>
                            </div>

                            <!-- التصنيف + المكان -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        التصنيف <span style="color:red;">*</span>
                                    </label>
                                    <select name="category" id="f_cat"
                                            class="form-select <?= isset($errors['category']) ? 'is-invalid' : 'is-valid' ?>"
                                            required>
                                        <option value="">-- اختر التصنيف --</option>
                                        <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat ?>"
                                                <?= $input['category'] === $cat ? 'selected' : '' ?>>
                                            <?= $cat ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if(isset($errors['category'])): ?>
                                    <div class="invalid-feedback"><?= $errors['category'] ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        مكان الفعالية <span style="color:red;">*</span>
                                    </label>
                                    <input type="text" name="location" id="f_loc"
                                           class="form-control <?= isset($errors['location']) ? 'is-invalid' : 'is-valid' ?>"
                                           value="<?= htmlspecialchars($input['location']) ?>"
                                           maxlength="255" required/>
                                    <?php if(isset($errors['location'])): ?>
                                    <div class="invalid-feedback"><?= $errors['location'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- التاريخ -->
                            <div class="mb-3">
                                <label class="form-label">
                                    تاريخ الفعالية <span style="color:red;">*</span>
                                </label>
                                <input type="date" name="event_date" id="f_date"
                                       class="form-control <?= isset($errors['event_date']) ? 'is-invalid' : 'is-valid' ?>"
                                       value="<?= htmlspecialchars($input['event_date']) ?>" required/>
                                <?php if(isset($errors['event_date'])): ?>
                                <div class="invalid-feedback"><?= $errors['event_date'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- رابط الصورة -->
                            <div class="mb-4">
                                <label class="form-label">
                                    رابط صورة الفعالية
                                    <span class="badge bg-secondary ms-1">اختياري</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:var(--bg);border-color:var(--border);">
                                        <i class="bi bi-image" style="color:var(--text-muted);"></i>
                                    </span>
                                    <input type="url" name="image" id="f_img"
                                           class="form-control"
                                           value="<?= htmlspecialchars($input['image']) ?>"
                                           placeholder="https://example.com/image.jpg"/>
                                    <button type="button" class="btn btn-outline-secondary"
                                            onclick="document.getElementById('f_img').value='';updatePreview();"
                                            title="مسح الصورة الحالية">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    اتركه فارغاً للاحتفاظ بالصورة الحالية.
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-3 flex-wrap">
                                <button type="submit" class="btn btn-warning px-4 py-2 fw-bold text-dark">
                                    <i class="bi bi-save me-2"></i>حفظ التعديلات
                                </button>
                                <a href="dashboard.php" class="btn btn-outline-secondary px-4">
                                    <i class="bi bi-x-lg me-1"></i>إلغاء
                                </a>
                                <a href="../event.php?id=<?= $id ?>" target="_blank"
                                   class="btn btn-outline-primary px-4">
                                    <i class="bi bi-eye me-1"></i>عرض الفعالية
                                </a>
                                <a href="delete_event.php?id=<?= $id ?>"
                                   class="btn btn-outline-danger px-4"
                                   onclick="return confirm('هل أنت متأكد من حذف هذه الفعالية؟ لا يمكن التراجع!')">
                                    <i class="bi bi-trash me-1"></i>حذف
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview -->
                <div class="col-lg-4">
                    <div class="contact-form-box" style="position:sticky;top:80px;">
                        <h5 style="font-weight:700;color:var(--primary);margin-bottom:1.2rem;padding-bottom:.8rem;border-bottom:2px solid var(--border);">
                            <i class="bi bi-eye me-2"></i>معاينة مباشرة
                        </h5>

                        <div class="event-card" id="previewCard">
                            <img id="prevImg"
                                 src="<?= htmlspecialchars($input['image']) ?>"
                                 class="card-img-top"
                                 onerror="this.src='https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&h=300&fit=crop'"
                                 style="height:180px;object-fit:cover;"
                                 alt="معاينة"/>
                            <div class="card-body" style="padding:1rem;">
                                <span id="prevBadge" class="badge bg-<?= getCategoryBadge($input['category']) ?> mb-2">
                                    <?= htmlspecialchars($input['category']) ?>
                                </span>
                                <h6 id="prevTitle" style="font-weight:700;color:var(--text);min-height:40px;">
                                    <?= htmlspecialchars(mb_substr($input['title'], 0, 60)) ?>
                                </h6>
                                <p id="prevDesc" style="font-size:.85rem;color:var(--text-muted);min-height:50px;">
                                    <?= htmlspecialchars(mb_substr($input['description'], 0, 100)) ?>...
                                </p>
                                <div style="font-size:.82rem;color:var(--text-muted);">
                                    <div>
                                        <i class="bi bi-calendar3 me-1" style="color:var(--accent);"></i>
                                        <span id="prevDate"><?= $input['event_date'] ?></span>
                                    </div>
                                    <div class="mt-1">
                                        <i class="bi bi-geo-alt me-1" style="color:var(--accent);"></i>
                                        <span id="prevLoc"><?= htmlspecialchars($input['location']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Original values summary -->
                        <div class="alert alert-light mt-3 mb-0" style="font-size:.82rem;border:1px solid var(--border);">
                            <strong><i class="bi bi-clock-history me-1"></i>القيم الأصلية:</strong>
                            <div class="mt-1" style="color:var(--text-muted);">
                                <div><strong>الحالة:</strong>
                                    <?php
                                    $origDate = new DateTime($event['event_date']);
                                    $today    = new DateTime();
                                    echo $origDate < $today ? 'منتهية' : 'قادمة';
                                    ?>
                                </div>
                                <div><strong>تاريخ الإضافة:</strong> <?= date('d/m/Y', strtotime($event['created_at'])) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
<script>
const badgeColors = {
    'ثقافة':'primary','رياضة':'success','موسيقى':'warning',
    'عائلي':'danger','علوم':'info','أخرى':'secondary'
};
const defaultImgs = {
    'ثقافة':'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&h=300&fit=crop',
    'رياضة':'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=600&h=300&fit=crop',
    'موسيقى':'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=600&h=300&fit=crop',
    'عائلي':'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=600&h=300&fit=crop',
    'علوم':'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600&h=300&fit=crop',
};
const originalImg = "<?= addslashes($event['image']) ?>";
const fallback    = 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&h=300&fit=crop';

function updatePreview() {
    const title = document.getElementById('f_title').value || 'عنوان الفعالية...';
    const desc  = document.getElementById('f_desc').value  || 'الوصف...';
    const cat   = document.getElementById('f_cat').value;
    const loc   = document.getElementById('f_loc').value   || 'المكان';
    const date  = document.getElementById('f_date').value;
    const img   = document.getElementById('f_img').value;

    document.getElementById('prevTitle').textContent = title.substring(0, 60) + (title.length > 60 ? '...' : '');
    document.getElementById('prevDesc').textContent  = desc.substring(0, 100)  + (desc.length > 100  ? '...' : '');
    document.getElementById('prevLoc').textContent   = loc;
    document.getElementById('prevDate').textContent  = date || '-- / -- / ----';

    const badge = document.getElementById('prevBadge');
    badge.textContent = cat || 'التصنيف';
    badge.className   = 'badge mb-2 bg-' + (badgeColors[cat] || 'secondary');

    // Image: typed URL → original (if cleared) → category default → fallback
    const imgEl = document.getElementById('prevImg');
    imgEl.src = img ? img : (originalImg || defaultImgs[cat] || fallback);

    document.getElementById('titleCount').textContent = document.getElementById('f_title').value.length;
    document.getElementById('descCount').textContent  = document.getElementById('f_desc').value.length;
}

['f_title','f_desc','f_cat','f_loc','f_date','f_img'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('input',  updatePreview);
        el.addEventListener('change', updatePreview);
    }
});

// ── Client-side validation ──
document.getElementById('editForm').addEventListener('submit', function(e) {
    let ok = true;
    ['f_title','f_desc','f_cat','f_loc','f_date'].forEach(id => {
        const el = document.getElementById(id);
        if (!el.value.trim()) {
            el.classList.add('is-invalid');
            el.classList.remove('is-valid');
            ok = false;
        } else {
            el.classList.remove('is-invalid');
            el.classList.add('is-valid');
        }
    });
    if (!ok) {
        e.preventDefault();
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
});
</script>
</body>
</html>
