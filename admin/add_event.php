<?php
// admin/add_event.php - إضافة فعالية جديدة
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../db.php';

$errors = [];
$success = false;

// القيم المدخلة (للاحتفاظ بها عند وجود أخطاء)
$input = [
    'title'       => '',
    'description' => '',
    'category'    => '',
    'location'    => '',
    'event_date'  => '',
    'image'       => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['title']       = trim($_POST['title']       ?? '');
    $input['description'] = trim($_POST['description'] ?? '');
    $input['category']    = trim($_POST['category']    ?? '');
    $input['location']    = trim($_POST['location']    ?? '');
    $input['event_date']  = trim($_POST['event_date']  ?? '');
    $input['image']       = trim($_POST['image']       ?? '');

    // التحقق من الحقول المطلوبة
    if (empty($input['title']))      $errors['title']      = 'عنوان الفعالية مطلوب';
    if (empty($input['description'])) $errors['description'] = 'وصف الفعالية مطلوب';
    if (empty($input['category']))   $errors['category']   = 'التصنيف مطلوب';
    if (empty($input['location']))   $errors['location']   = 'مكان الفعالية مطلوب';
    if (empty($input['event_date'])) {
        $errors['event_date'] = 'تاريخ الفعالية مطلوب';
    } elseif (!strtotime($input['event_date'])) {
        $errors['event_date'] = 'صيغة التاريخ غير صحيحة';
    }

    // رابط الصورة اختياري — نضع صورة افتراضية إن لم يُدخَل
    if (empty($input['image'])) {
        $defaults = [
            'ثقافة'  => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=800&h=400&fit=crop',
            'رياضة'  => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=800&h=400&fit=crop',
            'موسيقى' => 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=800&h=400&fit=crop',
            'عائلي'  => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800&h=400&fit=crop',
            'علوم'   => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800&h=400&fit=crop',
        ];
        $input['image'] = $defaults[$input['category']]
            ?? 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=800&h=400&fit=crop';
    }

    // إذا لا يوجد أخطاء — أضف للقاعدة
    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO events (title, description, category, location, event_date, image)
             VALUES (:title, :description, :category, :location, :event_date, :image)"
        );
        $stmt->execute([
            ':title'       => $input['title'],
            ':description' => $input['description'],
            ':category'    => $input['category'],
            ':location'    => $input['location'],
            ':event_date'  => $input['event_date'],
            ':image'       => $input['image'],
        ]);

        $_SESSION['flash'] = [
            'type' => 'success',
            'msg'  => 'تمت إضافة الفعالية "' . $input['title'] . '" بنجاح!',
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
    <title>إضافة فعالية - لوحة التحكم</title>
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
            <a class="nav-link active" href="add_event.php">
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
            <div class="d-flex align-items-center gap-2">
                <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-right"></i>
                </a>
                <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>إضافة فعالية جديدة</h4>
            </div>
            <button id="darkToggle" style="background:var(--bg);border:1px solid var(--border);color:var(--text);border-radius:20px;padding:.3rem .8rem;font-size:.85rem;cursor:pointer;">
                <i class="bi bi-moon-fill"></i> داكن
            </button>
        </div>

        <div class="admin-main">

            <!-- Summary Errors -->
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

            <div class="row g-4">

                <!-- Form -->
                <div class="col-lg-8">
                    <div class="contact-form-box">
                        <h5 style="font-weight:700;color:var(--primary);margin-bottom:1.5rem;padding-bottom:.8rem;border-bottom:2px solid var(--border);">
                            <i class="bi bi-card-text me-2"></i>بيانات الفعالية
                        </h5>

                        <form method="POST" action="" id="addForm" novalidate>

                            <!-- العنوان -->
                            <div class="mb-3">
                                <label class="form-label">
                                    عنوان الفعالية <span style="color:red;">*</span>
                                </label>
                                <input type="text" name="title" id="f_title"
                                       class="form-control <?= isset($errors['title']) ? 'is-invalid' : (isset($_POST['title']) ? 'is-valid' : '') ?>"
                                       value="<?= htmlspecialchars($input['title']) ?>"
                                       placeholder="مثال: مهرجان الثقافة والفنون الجامعي"
                                       maxlength="255" required/>
                                <?php if(isset($errors['title'])): ?>
                                <div class="invalid-feedback"><?= $errors['title'] ?></div>
                                <?php endif; ?>
                                <div class="form-text">
                                    <span id="titleCount">0</span> / 255 حرف
                                </div>
                            </div>

                            <!-- الوصف -->
                            <div class="mb-3">
                                <label class="form-label">
                                    وصف الفعالية <span style="color:red;">*</span>
                                </label>
                                <textarea name="description" id="f_desc" rows="5"
                                          class="form-control <?= isset($errors['description']) ? 'is-invalid' : (isset($_POST['description']) ? 'is-valid' : '') ?>"
                                          placeholder="اكتب وصفاً تفصيلياً للفعالية..."
                                          required><?= htmlspecialchars($input['description']) ?></textarea>
                                <?php if(isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?= $errors['description'] ?></div>
                                <?php endif; ?>
                                <div class="form-text">
                                    <span id="descCount">0</span> حرف مُدخل
                                </div>
                            </div>

                            <!-- التصنيف + المكان -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        التصنيف <span style="color:red;">*</span>
                                    </label>
                                    <select name="category" id="f_cat"
                                            class="form-select <?= isset($errors['category']) ? 'is-invalid' : (isset($_POST['category']) ? 'is-valid' : '') ?>"
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
                                           class="form-control <?= isset($errors['location']) ? 'is-invalid' : (isset($_POST['location']) ? 'is-valid' : '') ?>"
                                           value="<?= htmlspecialchars($input['location']) ?>"
                                           placeholder="مثال: دمشق - قاعة الكبير"
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
                                       class="form-control <?= isset($errors['event_date']) ? 'is-invalid' : (isset($_POST['event_date']) ? 'is-valid' : '') ?>"
                                       value="<?= htmlspecialchars($input['event_date']) ?>"
                                       required/>
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
                                </div>
                                <div class="form-text">
                                    اتركه فارغاً لاستخدام صورة افتراضية حسب التصنيف.
                                    يمكنك استخدام روابط من <a href="https://unsplash.com" target="_blank">Unsplash</a>.
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary-custom px-4 py-2 fw-bold">
                                    <i class="bi bi-plus-circle me-2"></i>إضافة الفعالية
                                </button>
                                <button type="reset" class="btn btn-outline-secondary px-4"
                                        onclick="resetPreview()">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>مسح
                                </button>
                                <a href="dashboard.php" class="btn btn-outline-danger px-4">
                                    <i class="bi bi-x-lg me-1"></i>إلغاء
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="col-lg-4">
                    <div class="contact-form-box" style="position:sticky;top:80px;">
                        <h5 style="font-weight:700;color:var(--primary);margin-bottom:1.2rem;padding-bottom:.8rem;border-bottom:2px solid var(--border);">
                            <i class="bi bi-eye me-2"></i>معاينة الفعالية
                        </h5>

                        <!-- Preview -->
                        <div class="event-card" id="previewCard">
                            <img id="prevImg"
                                 src="https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&h=300&fit=crop"
                                 class="card-img-top"
                                 onerror="this.src='https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&h=300&fit=crop'"
                                 style="height:180px;object-fit:cover;"
                                 alt="معاينة"/>
                            <div class="card-body" style="padding:1rem;">
                                <span id="prevBadge" class="badge bg-secondary mb-2">التصنيف</span>
                                <h6 id="prevTitle" style="font-weight:700;color:var(--text);min-height:40px;">
                                    عنوان الفعالية سيظهر هنا...
                                </h6>
                                <p id="prevDesc" style="font-size:.85rem;color:var(--text-muted);min-height:50px;">
                                    الوصف سيظهر هنا...
                                </p>
                                <div style="font-size:.82rem;color:var(--text-muted);">
                                    <div><i class="bi bi-calendar3 me-1" style="color:var(--accent);"></i>
                                        <span id="prevDate">-- / -- / ----</span>
                                    </div>
                                    <div class="mt-1">
                                        <i class="bi bi-geo-alt me-1" style="color:var(--accent);"></i>
                                        <span id="prevLoc">المكان</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tips -->
                        <div class="alert alert-info mt-3 mb-0" style="font-size:.83rem;">
                            <i class="bi bi-lightbulb-fill me-1"></i>
                            <strong>نصائح:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                <li>العنوان لا يتجاوز 255 حرفاً</li>
                                <li>الوصف يجب أن يكون تفصيلياً</li>
                                <li>التاريخ بصيغة YYYY-MM-DD</li>
                                <li>الصورة من Unsplash أو Imgur</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /admin-main -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
<script>
// ── Live Preview ──
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
const fallback = 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&h=300&fit=crop';

function updatePreview() {
    const title = document.getElementById('f_title').value || 'عنوان الفعالية سيظهر هنا...';
    const desc  = document.getElementById('f_desc').value  || 'الوصف سيظهر هنا...';
    const cat   = document.getElementById('f_cat').value;
    const loc   = document.getElementById('f_loc').value   || 'المكان';
    const date  = document.getElementById('f_date').value;
    const img   = document.getElementById('f_img').value;

    document.getElementById('prevTitle').textContent = title.substring(0, 60) + (title.length > 60 ? '...' : '');
    document.getElementById('prevDesc').textContent  = desc.substring(0, 100) + (desc.length > 100 ? '...' : '');
    document.getElementById('prevLoc').textContent   = loc;
    document.getElementById('prevDate').textContent  = date || '-- / -- / ----';

    const badge = document.getElementById('prevBadge');
    badge.textContent  = cat || 'التصنيف';
    badge.className    = 'badge mb-2 bg-' + (badgeColors[cat] || 'secondary');

    const prevImg = document.getElementById('prevImg');
    prevImg.src = img || defaultImgs[cat] || fallback;

    // Char counters
    document.getElementById('titleCount').textContent = document.getElementById('f_title').value.length;
    document.getElementById('descCount').textContent  = document.getElementById('f_desc').value.length;
}

function resetPreview() {
    setTimeout(() => {
        document.getElementById('prevTitle').textContent = 'عنوان الفعالية سيظهر هنا...';
        document.getElementById('prevDesc').textContent  = 'الوصف سيظهر هنا...';
        document.getElementById('prevLoc').textContent   = 'المكان';
        document.getElementById('prevDate').textContent  = '-- / -- / ----';
        document.getElementById('prevBadge').className   = 'badge mb-2 bg-secondary';
        document.getElementById('prevBadge').textContent = 'التصنيف';
        document.getElementById('prevImg').src           = fallback;
        document.getElementById('titleCount').textContent = '0';
        document.getElementById('descCount').textContent  = '0';
    }, 50);
}

['f_title','f_desc','f_cat','f_loc','f_date','f_img'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', updatePreview);
        el.addEventListener('change', updatePreview);
    }
});

// Init on page load (in case of form re-submit with values)
updatePreview();

// ── Client-side validation before submit ──
document.getElementById('addForm').addEventListener('submit', function(e) {
    let ok = true;
    const required = [
        {id:'f_title',  errKey:'title'},
        {id:'f_desc',   errKey:'description'},
        {id:'f_cat',    errKey:'category'},
        {id:'f_loc',    errKey:'location'},
        {id:'f_date',   errKey:'event_date'},
    ];
    required.forEach(({id}) => {
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
        window.scrollTo({top:0, behavior:'smooth'});
    }
});
</script>
</body>
</html>
