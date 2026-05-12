<?php
require_once 'auth.php';
$auth = new Auth();
if (isset($_POST['login'])) {
    $role = $auth->login($_POST['username'], $_POST['password']);
    if ($role) { header("Location: dashboard.php"); exit(); }
    else { $error = "Username atau Password Salah!"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html { height: 100%; margin: 0; font-family: 'Segoe UI', sans-serif; background-color: #28a745; overflow-x: hidden; }
        .top-section { height: 45vh; background-color: #ffffff; display: flex; justify-content: center; align-items: center; position: relative; }
        .custom-shape-divider-bottom { position: absolute; bottom: 0; left: 0; width: 100%; overflow: hidden; line-height: 0; transform: rotate(180deg); }
        .custom-shape-divider-bottom svg { position: relative; display: block; width: calc(100% + 1.3px); height: 80px; }
        .custom-shape-divider-bottom .shape-fill { fill: #28a745; }
        .login-container { position: relative; z-index: 10; margin-top: -50px; display: flex; justify-content: center; padding: 0 15px; }
        .login-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 350px; text-align: center; }
        .logo-img { width: 180px; z-index: 5; margin-bottom: 20px; padding-top: 20px; }
        .btn-login { background-color: #28a745; color: white; border-radius: 50px; padding: 10px; width: 100%; font-weight: bold; }
    </style>
</head>
<body>
    <div class="top-section">
        <img src="logo.png" alt="Logo" class="logo-img">
        <div class="custom-shape-divider-bottom">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V120H0c68.47,1.61,156.24-8.15,231.22-35.63C251.36,77.29,285.56,65.11,321.39,56.44Z" class="shape-fill"></path></svg>
        </div>
    </div>
    <div class="login-container">
        <div class="login-card">
            <h4 class="mb-4 fw-bold">LOGIN</h4>
            <?php if(isset($error)) echo "<div class='alert alert-danger p-2' style='font-size:13px;'>$error</div>"; ?>
            <form method="POST">
                <div class="mb-3 text-start"><label class="small fw-bold">Username</label><input type="text" name="username" class="form-control rounded-pill" placeholder="Masukkan Username" required></div>
                <div class="mb-4 text-start"><label class="small fw-bold">Password</label><input type="password" name="password" class="form-control rounded-pill" placeholder="Masukkan Password" required></div>
                <button type="submit" name="login" class="btn btn-login shadow-sm">MASUK</button>
            </form>
        </div>
    </div>
</body>
</html>