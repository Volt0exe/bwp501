<?php
// about.php - صفحة عن الدليل
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>عن الدليل - <?= SITE_NAME ?></title>
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
                <li class="nav-item"><a class="nav-link active" href="about.php">عن الدليل</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">اتصل بنا</a></li>
            </ul>
            <button id="darkToggle"><i class="bi bi-moon-fill"></i> داكن</button>
        </div>
    </div>
</nav>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <h1><i class="bi bi-info-circle me-2"></i>عن دليل الفعاليات</h1>
        <p>تعرّف على هدفنا ورؤيتنا وفريق العمل الذي يجعل هذا الدليل ممكناً</p>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <h2 class="section-title">هدفنا ورؤيتنا</h2>
                <p style="font-size:1.05rem;line-height:1.9;color:var(--text);">
                    يهدف <strong>دليل فعاليات الجامعة الافتراضية السورية</strong> إلى تعزيز مشاركة الطلاب في الحياة الجامعية
                    من خلال تقديم منصة موحدة وسهلة الاستخدام تُتيح الاطلاع على جميع الفعاليات الأكاديمية
                    والثقافية والرياضية والاجتماعية في مكان واحد.
                </p>
                <p style="font-size:1.05rem;line-height:1.9;color:var(--text);">
                    نؤمن بأن الحياة الجامعية لا تقتصر على الدراسة فحسب، بل تمتد لتشمل تجارب إنسانية وإبداعية غنية
                    تُسهم في بناء شخصية الطالب وتوسيع آفاقه.
                </p>
                <div class="row g-3 mt-2">
                    <div class="col-6">
                        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.2rem;text-align:center;box-shadow:var(--shadow);">
                            <div style="font-size:2rem;font-weight:800;color:var(--primary);">
                                <?= $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn() ?>+
                            </div>
                            <div style="color:var(--text-muted);font-size:.88rem;">فعالية مسجلة</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.2rem;text-align:center;box-shadow:var(--shadow);">
                            <div style="font-size:2rem;font-weight:800;color:var(--primary);">5</div>
                            <div style="color:var(--text-muted);font-size:.88rem;">تصنيفات رئيسية</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&h=400&fit=crop"
                     style="width:100%;border-radius:18px;box-shadow:var(--shadow-hover);"
                     alt="جامعة افتراضية"/>
            </div>
        </div>
    </div>
</section>

<!-- Values -->
<section class="py-5" style="background:var(--bg-card);">
    <div class="container">
        <h2 class="section-title text-center" style="text-align:center!important;">قيمنا الأساسية</h2>
        <p class="text-center mb-5" style="color:var(--text-muted);">نلتزم بمجموعة من القيم التي توجّه عملنا وخدماتنا</p>
        <div class="row g-4">
            <?php
            $values = [
                ['bi-lightbulb', 'الابتكار', 'نسعى دائماً لتقديم حلول إبداعية تُحسّن تجربة المستخدم وتُسهّل الوصول إلى الفعاليات.', 'primary'],
                ['bi-shield-check', 'الموثوقية', 'نضمن دقة المعلومات المقدمة وتحديثها باستمرار لنكون مصدركم الأول الموثوق.', 'success'],
                ['bi-people', 'الشمولية', 'نرحب بجميع الفعاليات التي تخدم المجتمع الجامعي دون تمييز أو إقصاء.', 'warning'],
                ['bi-star', 'الجودة', 'نلتزم بأعلى معايير الجودة في تقديم المحتوى وضمان سهولة التصفح والاستخدام.', 'danger'],
            ];
            foreach($values as $v): ?>
            <div class="col-md-6 col-lg-3">
                <div style="text-align:center;padding:1.5rem;background:var(--bg);border-radius:14px;border:1px solid var(--border);height:100%;">
                    <div style="width:60px;height:60px;background:var(--bs-<?= $v[3] ?>);border-radius:15px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.6rem;color:#fff;">
                        <i class="bi <?= $v[0] ?>"></i>
                    </div>
                    <h5 style="font-weight:700;color:var(--text);margin-bottom:.6rem;"><?= $v[1] ?></h5>
                    <p style="font-size:.9rem;color:var(--text-muted);margin:0;"><?= $v[2] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Team -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title">فريق العمل</h2>
        <p style="color:var(--text-muted);margin-bottom:2.5rem;">
            يضمّ الدليل فريقاً متكاملاً من الأكاديميين والمطورين والمنسقين المتفانين.
        </p>
        <div class="row g-4">
            <?php
            $team = [
                ['https://images.unsplash.com/photo-1776715139556-f7d420661a89?q=80&w=685&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'احمد ياسين سندس', 'ahmad_yassin_289918', ''],
                ['https://images.unsplash.com/photo-1777287514156-ba7eabea01f9?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'أماني الامام
', ' amany_190573 ' , ''],
                ['https://images.unsplash.com/photo-1774375569394-8a4f35475fc7?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'نور الدين هشام النوري
', 'nour_aldin_271490 ', ''],
                ['https://images.unsplash.com/photo-1774375569394-8a4f35475fc7?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'عبد الرحمن الهواش', 'abdul_rahman_277365', ''],
                ['', '', '', ''],
            ];
            foreach($team as $member): ?>
            <div class="col-lg-4 col-md-6">
                <div class="team-card">
                    <img src="<?= $member[0] ?>" alt="<?= $member[1] ?>"/>
                    <h5><?= $member[1] ?></h5>
                    <p style="color:var(--primary);font-weight:600;font-size:.9rem;"><?= $member[2] ?></p>
                    <p><?= $member[3] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Submission Policy -->
<section class="py-5" style="background:var(--bg-card);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="section-title">سياسات تقديم الفعاليات</h2>
                <div class="accordion" id="policyAccordion">
                    <?php
                    $policies = [
                        ['من يمكنه تقديم فعالية؟', 'يمكن لأي عضو في المجتمع الجامعي (طالب، أستاذ، موظف، كلية، قسم) تقديم طلب إدراج فعاليته في الدليل، شريطة أن تكون الفعالية ذات صلة بالمجتمع الجامعي.'],
                        ['كيف يتم تقديم فعالية؟', 'تُقدَّم الفعاليات عبر نموذج التواصل في صفحة "اتصل بنا" أو مباشرةً عبر البريد الإلكتروني الرسمي، مع ذكر جميع التفاصيل المطلوبة كالعنوان والتاريخ والمكان والوصف.'],
                        ['ما هي معايير قبول الفعاليات؟', 'تُقبل الفعاليات التي تنسجم مع قيم الجامعة وأهدافها الأكاديمية والثقافية، وتعود بالنفع على المجتمع الجامعي، وتلتزم بالأنظمة والتعليمات المعمول بها.'],
                        ['ما هي مدة معالجة الطلبات؟', 'تُعالَج طلبات إدراج الفعاليات خلال 2-3 أيام عمل من تاريخ استلامها، ويتم إشعار مقدّم الطلب بالقبول أو الرفض عبر البريد الإلكتروني.'],
                        ['هل يمكن تعديل معلومات الفعالية؟', 'نعم، يمكن طلب تعديل معلومات الفعالية في أي وقت قبل تاريخ انعقادها عبر التواصل المباشر مع إدارة الدليل.'],
                    ];
                    foreach($policies as $i => $p): ?>
                    <div class="accordion-item" style="background:var(--bg);border-color:var(--border);">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#policy<?= $i ?>"
                                    style="background:var(--bg);color:var(--text);font-weight:600;">
                                <i class="bi bi-question-circle-fill me-2" style="color:var(--accent);"></i>
                                <?= $p[0] ?>
                            </button>
                        </h2>
                        <div id="policy<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>"
                             data-bs-parent="#policyAccordion">
                            <div class="accordion-body" style="color:var(--text-muted);">
                                <?= $p[1] ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sponsors / Partners -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="section-title" style="text-align:center!important;">شركاؤنا</h2>
        <p style="color:var(--text-muted);margin-bottom:2rem;">نفخر بشراكتنا مع مجموعة متميزة من المؤسسات الأكاديمية والثقافية</p>
        <div class="row g-4 justify-content-center align-items-center">
            <?php
            $partners = [
                'الجامعة الافتراضية السورية', 'وزارة التعليم العالي',
                'الاتحاد الرياضي الجامعي', 'الاتحاد الثقافي الطلابي',
            ];
            foreach($partners as $p): ?>
            <div class="col-6 col-md-3">
                <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.5rem;box-shadow:var(--shadow);">
                    <i class="bi bi-building" style="font-size:2rem;color:var(--primary);display:block;margin-bottom:.5rem;"></i>
                    <div style="font-weight:600;font-size:.9rem;color:var(--text);"><?= $p ?></div>
                </div>
            </div>
            <?php endforeach; ?>
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
