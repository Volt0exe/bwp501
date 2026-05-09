<?php
// admin/login.php - صفحة تسجيل دخول المشرف
session_start();

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

require_once '../db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Support both plain MD5 (for demo) and bcrypt
        $valid = false;
        if ($user) {
            if (strlen($user['password']) === 32) {
                // MD5 hash
                $valid = (md5($password) === $user['password']);
            } else {
                // bcrypt
                $valid = password_verify($password, $user['password']);
            }
        }

        if ($valid) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username']  = $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
        }
    } else {
        $error = 'يرجى إدخال اسم المستخدم وكلمة المرور';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>تسجيل الدخول - لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
    <link rel="stylesheet" href="../assets/css/styles.css"/>
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">

        <!-- Logo / Brand -->
        <div class="text-center mb-4">
            <div style="width:70px;height:70px;background:linear-gradient(135deg,var(--primary),var(--primary-light));border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:2rem;color:#fff;">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h2>لوحة التحكم</h2>
            <p style="color:var(--text-muted);font-size:.9rem;margin:0;">
                دليل فعاليات الجامعة الافتراضية
            </p>
        </div>

        <!-- Error Alert -->
        <?php if($error): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label fw-bold">اسم المستخدم</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--bg);border-color:var(--border);color:var(--text-muted);">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <input type="text" class="form-control" name="username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           placeholder="أدخل اسم المستخدم" required autofocus/>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--bg);border-color:var(--border);color:var(--text-muted);">
                        <i class="bi bi-lock-fill"></i>
                    </span>
                    <input type="password" class="form-control" name="password"
                           placeholder="أدخل كلمة المرور" required id="pwdInput"/>
                    <button class="btn btn-outline-secondary" type="button"
                            onclick="var p=document.getElementById('pwdInput');p.type=p.type==='password'?'text':'password'">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary-custom w-100 py-2 fw-bold" style="font-size:1rem;">
                <i class="bi bi-box-arrow-in-left me-2"></i>تسجيل الدخول
            </button>
        </form>

        <!-- Demo Credentials Info -->
        <div class="alert alert-info mt-4 mb-0" style="font-size:.85rem;">
            <i class="bi bi-info-circle me-1"></i>
            <strong>بيانات الدخول التجريبية:</strong><br/>
            المستخدم: <code>admin</code> &nbsp;|&nbsp; كلمة المرور: <code>admin</code>
        </div>

        <div class="text-center mt-3">
            <a href="../index.php" style="color:var(--text-muted);font-size:.88rem;">
                <i class="bi bi-house me-1"></i>العودة للموقع
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
