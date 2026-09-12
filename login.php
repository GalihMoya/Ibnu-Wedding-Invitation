<?php
/**
 * Login Page - Admin Panitia
 * Ibnu & Adinda Wedding Invitation
 */

session_start();

// Jika admin sudah login, langsung arahkan ke dashboard admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

$hasError = isset($_GET['error']);
$isLoggedOut = isset($_GET['msg']) && $_GET['msg'] === 'logout';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Panitia & Admin | The Wedding of Ibnu & Adinda</title>
  <meta name="robots" content="noindex, nofollow">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --primary-gold: #d4af37;
      --gold-light: #f3e5ab;
      --gold-dark: #aa8c2c;
      --bg-dark: #0f1115;
      --bg-card: rgba(22, 25, 33, 0.85);
      --gold-border: rgba(212, 175, 55, 0.35);
      --font-serif: 'Cinzel', serif;
      --font-quote: 'Cormorant Garamond', serif;
      --font-sans: 'Plus Jakarta Sans', sans-serif;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: var(--bg-dark);
      background-image: 
        radial-gradient(circle at 15% 25%, rgba(212, 175, 55, 0.08) 0%, transparent 45%),
        radial-gradient(circle at 85% 75%, rgba(212, 175, 55, 0.06) 0%, transparent 45%),
        url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23d4af37' fill-opacity='0.02' fill-rule='evenodd'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");
      color: #e5e7eb;
      font-family: var(--font-sans);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }

    .login-container {
      width: 100%;
      max-width: 440px;
    }

    .login-card {
      background: var(--bg-card);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--gold-border);
      border-radius: 20px;
      padding: 2.8rem 2.2rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 25px rgba(212, 175, 55, 0.12);
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .login-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, transparent, var(--primary-gold), transparent);
    }

    .logo-seal {
      width: 68px;
      height: 68px;
      margin: 0 auto 1.2rem;
      border: 2px solid var(--primary-gold);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary-gold);
      font-size: 1.8rem;
      background: rgba(212, 175, 55, 0.08);
      box-shadow: 0 0 15px rgba(212, 175, 55, 0.25);
    }

    .login-badge {
      display: inline-block;
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--gold-light);
      background: rgba(212, 175, 55, 0.12);
      border: 1px solid rgba(212, 175, 55, 0.3);
      padding: 0.3rem 0.9rem;
      border-radius: 50px;
      margin-bottom: 0.8rem;
      font-weight: 600;
    }

    .login-title {
      font-family: var(--font-serif);
      color: #ffffff;
      font-size: 1.55rem;
      letter-spacing: 1px;
      margin-bottom: 0.4rem;
    }

    .login-subtitle {
      font-size: 0.88rem;
      color: #9ca3af;
      margin-bottom: 2rem;
    }

    .form-group {
      margin-bottom: 1.3rem;
      text-align: left;
    }

    .form-label {
      display: block;
      font-size: 0.82rem;
      font-weight: 500;
      color: #d1d5db;
      margin-bottom: 0.5rem;
    }

    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-icon {
      position: absolute;
      left: 1rem;
      color: var(--primary-gold);
      font-size: 0.95rem;
      pointer-events: none;
    }

    .form-control {
      width: 100%;
      padding: 0.85rem 1rem 0.85rem 2.8rem;
      background: rgba(15, 17, 21, 0.7);
      border: 1px solid rgba(212, 175, 55, 0.25);
      border-radius: 10px;
      color: #ffffff;
      font-size: 0.95rem;
      font-family: inherit;
      outline: none;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--primary-gold);
      box-shadow: 0 0 12px rgba(212, 175, 55, 0.3);
      background: rgba(15, 17, 21, 0.9);
    }

    .btn-login {
      width: 100%;
      padding: 0.95rem;
      background: linear-gradient(135deg, #d4af37, #aa8c2c);
      color: #0f1115;
      border: none;
      border-radius: 10px;
      font-size: 0.95rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
      margin-top: 1.5rem;
    }

    .btn-login:hover {
      background: linear-gradient(135deg, #f3e5ab, #d4af37);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(212, 175, 55, 0.45);
    }

    .alert {
      padding: 0.75rem 1rem;
      border-radius: 8px;
      font-size: 0.85rem;
      margin-bottom: 1.2rem;
      display: flex;
      align-items: center;
      gap: 0.6rem;
      text-align: left;
    }

    .alert-danger {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.4);
      color: #fca5a5;
    }

    .alert-success {
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid rgba(16, 185, 129, 0.4);
      color: #6ee7b7;
    }

    .credential-hint {
      margin-top: 1.8rem;
      padding: 0.9rem;
      background: rgba(212, 175, 55, 0.06);
      border: 1px dashed rgba(212, 175, 55, 0.3);
      border-radius: 8px;
      font-size: 0.78rem;
      color: var(--gold-light);
      line-height: 1.5;
    }

    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      margin-top: 1.5rem;
      color: #9ca3af;
      font-size: 0.85rem;
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .back-link:hover {
      color: var(--primary-gold);
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-card">
      <div class="logo-seal">
        <i class="fa-solid fa-crown"></i>
      </div>
      
      <span class="login-badge">Portal Sesi Panitia</span>
      <h1 class="login-title">Ibnu &amp; Adinda</h1>
      <p class="login-subtitle">Masuk untuk mengelola data RSVP &amp; kehadiran tamu</p>

      <?php if ($hasError): ?>
        <div class="alert alert-danger">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <span>Username atau password salah. Silakan coba lagi.</span>
        </div>
      <?php endif; ?>

      <?php if ($isLoggedOut): ?>
        <div class="alert alert-success">
          <i class="fa-solid fa-circle-check"></i>
          <span>Anda telah berhasil logout dengan aman.</span>
        </div>
      <?php endif; ?>

      <form action="auth.php" method="POST">
        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-user input-icon"></i>
            <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" value="admin" required autofocus>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-lock input-icon"></i>
            <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" value="admin123" required>
          </div>
        </div>

        <button type="submit" class="btn-login">
          <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Dashboard
        </button>
      </form>

      <div class="credential-hint">
        <strong><i class="fa-solid fa-key"></i> Kredensial Akses Panitia:</strong><br>
        Username: <code>admin</code> &bull; Password: <code>admin123</code>
      </div>

      <div>
        <a href="index.php" class="back-link">
          <i class="fa-solid fa-arrow-left"></i> Kembali ke Undangan Pernikahan
        </a>
      </div>
    </div>
  </div>

</body>
</html>
