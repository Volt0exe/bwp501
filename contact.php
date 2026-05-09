<?php
// contact.php - صفحة اتصل بنا
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>اتصل بنا - <?= SITE_NAME ?></title>
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
                <li class="nav-item"><a class="nav-link" href="events.php">الفعاليات</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">عن الدليل</a></li>
                <li class="nav-item"><a class="nav-link active" href="contact.php">اتصل بنا</a></li>
            </ul>
            <button id="darkToggle"><i class="bi bi-moon-fill"></i> داكن</button>
        </div>
    </div>
</nav>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <h1><i class="bi bi-envelope-at me-2"></i>اتصل بنا</h1>
        <p>نحن هنا لمساعدتك — تواصل معنا ولن نبخل بردٍّ سريع</p>
    </div>
</section>

<!-- Contact Content -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">

            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="contact-form-box">
                    <h3 style="font-weight:700;color:var(--primary);margin-bottom:1.5rem;">
                        <i class="bi bi-send me-2"></i>أرسل رسالة
                    </h3>

                    <div id="formAlert" class="alert" style="display:none;"></div>

                    <form id="contactForm" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="cName" class="form-label">
                                    الاسم الكامل <span style="color:red;">*</span>
                                </label>
                                <input type="text" class="form-control" id="cName"
                                       placeholder="أدخل اسمك الكامل" required/>
                                <div class="invalid-feedback">يرجى إدخال الاسم (3 أحرف على الأقل)</div>
                            </div>
                            <div class="col-md-6">
                                <label for="cEmail" class="form-label">
                                    البريد الإلكتروني <span style="color:red;">*</span>
                                </label>
                                <input type="email" class="form-control" id="cEmail"
                                       placeholder="example@email.com" required/>
                                <div class="invalid-feedback">يرجى إدخال بريد إلكتروني صحيح</div>
                            </div>
                            <div class="col-12">
                                <label for="cSubject" class="form-label">الموضوع</label>
                                <select class="form-select" id="cSubject">
                                    <option value="">اختر الموضوع...</option>
                                    <option>استفسار عن فعالية</option>
                                    <option>تقديم فعالية جديدة</option>
                                    <option>شكوى أو اقتراح</option>
                                    <option>التعاون والشراكة</option>
                                    <option>أخرى</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="cMessage" class="form-label">
                                    الرسالة <span style="color:red;">*</span>
                                </label>
                                <textarea class="form-control" id="cMessage" rows="5"
                                          placeholder="اكتب رسالتك هنا..." required></textarea>
                                <div class="invalid-feedback">يرجى كتابة رسالة (10 أحرف على الأقل)</div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newsletter"/>
                                    <label class="form-check-label" for="newsletter">
                                        أرغب في الاشتراك بنشرة الفعاليات الأسبوعية
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom w-100 py-2">
                                    <i class="bi bi-send me-2"></i>إرسال الرسالة
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-5">
                <h3 style="font-weight:700;color:var(--primary);margin-bottom:1.5rem;">
                    <i class="bi bi-info-circle me-2"></i>معلومات التواصل
                </h3>

                <div class="d-flex flex-column gap-3">
                    <?php
                    $contacts = [
                        ['bi-envelope-fill', 'primary', 'البريد الإلكتروني', 'events@svuonline.org', 'mailto:events@svuonline.org'],
                        ['bi-telephone-fill', 'success', 'الهاتف', '+963 11 xxx xxxx', 'tel:+963'],
                        ['bi-geo-alt-fill', 'danger', 'العنوان', 'دمشق، الجامعة الافتراضية السورية', null],
                        ['bi-clock-fill', 'warning', 'ساعات العمل', 'السبت - الخميس: 8 صباحاً - 4 مساءً', null],
                    ];
                    foreach($contacts as $c): ?>
                    <div style="display:flex;gap:1rem;align-items:flex-start;background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.2rem;box-shadow:var(--shadow);">
                        <div style="width:46px;height:46px;background:var(--bs-<?= $c[1] ?>);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;flex-shrink:0;">
                            <i class="bi <?= $c[0] ?>"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:.9rem;color:var(--text-muted);margin-bottom:.2rem;"><?= $c[2] ?></div>
                            <?php if($c[4]): ?>
                            <a href="<?= $c[4] ?>" style="color:var(--text);font-weight:600;"><?= $c[3] ?></a>
                            <?php else: ?>
                            <div style="color:var(--text);font-weight:600;"><?= $c[3] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Social Media -->
                <div style="margin-top:2rem;">
                    <h5 style="font-weight:700;color:var(--text);margin-bottom:1rem;">
                        <i class="bi bi-share me-2"></i>وسائل التواصل الاجتماعي
                    </h5>
                    <div class="d-flex flex-wrap gap-3">
                        <?php
                        $socials = [
                            ['bi-facebook', 'Facebook', '#1877f2', '#'],
                            ['bi-twitter-x', 'Twitter / X', '#000', '#'],
                            ['bi-instagram', 'Instagram', '#e4405f', '#'],
                            ['bi-youtube', 'YouTube', '#ff0000', '#'],
                            ['bi-linkedin', 'LinkedIn', '#0077b5', '#'],
                        ];
                        foreach($socials as $s): ?>
                        <a href="<?= $s[3] ?>" target="_blank"
                           style="display:flex;align-items:center;gap:.5rem;background:var(--bg-card);border:1px solid var(--border);border-radius:8px;padding:.5rem 1rem;color:var(--text);font-size:.88rem;font-weight:600;transition:.2s;"
                           onmouseover="this.style.background='<?= $s[2] ?>';this.style.color='#fff';this.style.borderColor='<?= $s[2] ?>'"
                           onmouseout="this.style.background='';this.style.color='';this.style.borderColor=''">
                            <i class="bi <?= $s[0] ?>"></i> <?= $s[1] ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- FAQ -->
                <div style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);color:#fff;border-radius:14px;padding:1.5rem;margin-top:2rem;">
                    <h6 style="font-weight:700;margin-bottom:.8rem;">
                        <i class="bi bi-question-circle me-2"></i>هل لديك سؤال سريع؟
                    </h6>
                    <p style="font-size:.9rem;opacity:.88;margin:0;">
                        تفضّل بزيارة صفحة <a href="about.php" style="color:var(--accent);font-weight:600;">عن الدليل</a>
                        للاطلاع على سياساتنا، أو راسلنا مباشرةً وسنجيبك خلال 24 ساعة.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

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
