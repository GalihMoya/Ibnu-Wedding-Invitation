<?php
/**
 * Dashboard Admin & Manajemen Buku Tamu
 * Ibnu & Adinda Wedding Invitation
 */

session_start();

// Proteksi Sesi Admin: Tamu umum tidak boleh mengakses halaman ini
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/db.php';

// Ambil seluruh data tamu
$stmt = $pdo->query("SELECT * FROM guests ORDER BY id DESC");
$guests = $stmt->fetchAll();

// Hitung metrik ringkasan
$totalGuests = count($guests);
$totalHadir = 0;
$totalRagu = 0;
$totalTidakHadir = 0;
$totalPaxHadir = 0;
$totalCheckedIn = 0;

foreach ($guests as $g) {
    if ($g['status_rsvp'] === 'Hadir') {
        $totalHadir++;
        $totalPaxHadir += (int)$g['pax'];
    } elseif ($g['status_rsvp'] === 'Masih Ragu') {
        $totalRagu++;
    } else {
        $totalTidakHadir++;
    }

    if ((int)$g['is_checked_in'] === 1) {
        $totalCheckedIn++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin Buku Tamu | The Wedding of Ibnu & Adinda</title>
  <meta name="robots" content="noindex, nofollow">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --primary-gold: #d4af37;
      --gold-light: #f3e5ab;
      --gold-dark: #aa8c2c;
      --bg-dark: #0f1115;
      --bg-card: rgba(22, 25, 33, 0.9);
      --bg-card-hover: rgba(30, 34, 45, 0.95);
      --gold-border: rgba(212, 175, 55, 0.3);
      --text-main: #f3f4f6;
      --text-muted: #9ca3af;
      --font-serif: 'Cinzel', serif;
      --font-sans: 'Plus Jakarta Sans', sans-serif;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --radius: 12px;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: var(--bg-dark);
      background-image: 
        radial-gradient(circle at 10% 20%, rgba(212, 175, 55, 0.05) 0%, transparent 40%),
        radial-gradient(circle at 90% 80%, rgba(212, 175, 55, 0.04) 0%, transparent 40%);
      color: var(--text-main);
      font-family: var(--font-sans);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Navbar */
    .admin-navbar {
      background: rgba(15, 17, 21, 0.95);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--gold-border);
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .nav-brand {
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }

    .nav-logo-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 1.5px solid var(--primary-gold);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary-gold);
      font-size: 1.1rem;
      background: rgba(212, 175, 55, 0.1);
    }

    .nav-title {
      font-family: var(--font-serif);
      font-size: 1.15rem;
      color: #ffffff;
      letter-spacing: 0.5px;
    }

    .nav-subtitle {
      font-size: 0.75rem;
      color: var(--gold-light);
    }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .btn-nav {
      padding: 0.5rem 1rem;
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.2s ease;
    }

    .btn-outline {
      border: 1px solid var(--gold-border);
      color: var(--gold-light);
      background: transparent;
    }

    .btn-outline:hover {
      background: rgba(212, 175, 55, 0.15);
      border-color: var(--primary-gold);
      color: #ffffff;
    }

    .btn-logout {
      border: 1px solid rgba(239, 68, 68, 0.4);
      color: #fca5a5;
      background: rgba(239, 68, 68, 0.1);
    }

    .btn-logout:hover {
      background: rgba(239, 68, 68, 0.25);
      color: #ffffff;
    }

    /* Main Container */
    .admin-container {
      max-width: 1280px;
      margin: 0 auto;
      padding: 2rem 1.5rem;
      width: 100%;
      flex: 1;
    }

    /* Page Header */
    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-bottom: 2rem;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .header-title h1 {
      font-family: var(--font-serif);
      font-size: 1.8rem;
      color: #ffffff;
      margin-bottom: 0.3rem;
    }

    .header-title p {
      color: var(--text-muted);
      font-size: 0.9rem;
    }

    .header-tools {
      display: flex;
      gap: 0.8rem;
    }

    .btn-action {
      background: var(--bg-card);
      border: 1px solid var(--gold-border);
      color: var(--gold-light);
      padding: 0.55rem 1.1rem;
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.2s ease;
    }

    .btn-action:hover {
      background: var(--primary-gold);
      color: #0f1115;
    }

    /* Stat Cards Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 1.2rem;
      margin-bottom: 2.2rem;
    }

    .stat-card {
      background: var(--bg-card);
      border: 1px solid var(--gold-border);
      border-radius: var(--radius);
      padding: 1.4rem;
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
      transition: transform 0.2s ease;
    }

    .stat-card:hover {
      transform: translateY(-3px);
    }

    .stat-card.featured {
      background: linear-gradient(145deg, rgba(212, 175, 55, 0.15), rgba(22, 25, 33, 0.95));
      border-color: var(--primary-gold);
      box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
    }

    .stat-icon {
      font-size: 1.6rem;
      margin-bottom: 0.8rem;
      color: var(--primary-gold);
    }

    .stat-card.featured .stat-icon {
      color: #f3e5ab;
      animation: pulse-ring 2.5s infinite;
    }

    @keyframes pulse-ring {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.15); }
    }

    .stat-label {
      font-size: 0.78rem;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-bottom: 0.4rem;
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      color: #ffffff;
      font-family: var(--font-serif);
      line-height: 1;
    }

    .stat-sub {
      font-size: 0.75rem;
      color: var(--gold-light);
      margin-top: 0.4rem;
    }

    /* Filters & Search Bar */
    .table-controls {
      background: var(--bg-card);
      border: 1px solid var(--gold-border);
      border-radius: var(--radius);
      padding: 1.2rem;
      margin-bottom: 1.5rem;
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      align-items: center;
      justify-content: space-between;
    }

    .search-box {
      flex: 1;
      min-width: 260px;
      position: relative;
    }

    .search-box i {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
    }

    .search-input {
      width: 100%;
      padding: 0.7rem 1rem 0.7rem 2.6rem;
      background: rgba(15, 17, 21, 0.8);
      border: 1px solid rgba(212, 175, 55, 0.25);
      border-radius: 8px;
      color: #ffffff;
      font-size: 0.9rem;
      outline: none;
      font-family: inherit;
    }

    .search-input:focus {
      border-color: var(--primary-gold);
    }

    .filter-group {
      display: flex;
      gap: 0.8rem;
      flex-wrap: wrap;
    }

    .filter-select {
      background: rgba(15, 17, 21, 0.8);
      border: 1px solid rgba(212, 175, 55, 0.25);
      color: #ffffff;
      padding: 0.68rem 1rem;
      border-radius: 8px;
      font-size: 0.85rem;
      outline: none;
      cursor: pointer;
      font-family: inherit;
    }

    .filter-select:focus {
      border-color: var(--primary-gold);
    }

    /* Table Section */
    .table-card {
      background: var(--bg-card);
      border: 1px solid var(--gold-border);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
    }

    .table-responsive {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 0.88rem;
    }

    thead th {
      background: rgba(15, 17, 21, 0.95);
      padding: 1rem 1.2rem;
      color: var(--gold-light);
      font-family: var(--font-serif);
      font-size: 0.82rem;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      border-bottom: 1px solid var(--gold-border);
      white-space: nowrap;
    }

    tbody tr {
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      transition: background 0.2s ease;
    }

    tbody tr:hover {
      background: rgba(212, 175, 55, 0.04);
    }

    tbody tr.row-checked-in {
      background: rgba(16, 185, 129, 0.06);
    }

    tbody td {
      padding: 1.1rem 1.2rem;
      vertical-align: middle;
    }

    .col-guest-name {
      font-weight: 600;
      color: #ffffff;
      font-size: 0.95rem;
    }

    .guest-initial {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: rgba(212, 175, 55, 0.15);
      border: 1px solid var(--primary-gold);
      color: var(--primary-gold);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      margin-right: 0.6rem;
      font-size: 0.85rem;
    }

    /* Badges */
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.25rem 0.65rem;
      border-radius: 50px;
      font-size: 0.74rem;
      font-weight: 600;
      white-space: nowrap;
    }

    .badge-hadir {
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid rgba(16, 185, 129, 0.4);
      color: #6ee7b7;
    }

    .badge-ragu {
      background: rgba(245, 158, 11, 0.15);
      border: 1px solid rgba(245, 158, 11, 0.4);
      color: #fcd34d;
    }

    .badge-tidakhadir {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.4);
      color: #fca5a5;
    }

    .badge-pax {
      background: rgba(212, 175, 55, 0.12);
      border: 1px solid rgba(212, 175, 55, 0.3);
      color: var(--gold-light);
      padding: 0.2rem 0.55rem;
      border-radius: 6px;
      font-size: 0.8rem;
    }

    .wish-message-cell {
      max-width: 320px;
      color: #d1d5db;
      font-size: 0.82rem;
      line-height: 1.4;
      font-style: italic;
    }

    .time-cell {
      color: var(--text-muted);
      font-size: 0.78rem;
      white-space: nowrap;
    }

    /* Checkbox & Switch Check-in */
    .checkin-switch-wrapper {
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .custom-checkbox {
      position: relative;
      display: inline-block;
      width: 22px;
      height: 22px;
      cursor: pointer;
    }

    .custom-checkbox input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .checkbox-mark {
      position: absolute;
      top: 0;
      left: 0;
      height: 22px;
      width: 22px;
      background-color: rgba(15, 17, 21, 0.9);
      border: 1.5px solid rgba(212, 175, 55, 0.5);
      border-radius: 6px;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .custom-checkbox:hover .checkbox-mark {
      border-color: var(--primary-gold);
    }

    .custom-checkbox input:checked ~ .checkbox-mark {
      background-color: var(--success);
      border-color: var(--success);
      box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    .checkbox-mark::after {
      content: "";
      position: absolute;
      display: none;
      left: 7px;
      top: 3px;
      width: 5px;
      height: 10px;
      border: solid #ffffff;
      border-width: 0 2.5px 2.5px 0;
      transform: rotate(45deg);
    }

    .custom-checkbox input:checked ~ .checkbox-mark::after {
      display: block;
    }

    .checkin-status-text {
      font-size: 0.78rem;
      font-weight: 600;
      transition: color 0.2s ease;
    }

    .status-checked {
      color: #6ee7b7;
    }

    .status-unchecked {
      color: var(--text-muted);
    }

    /* Toast Notification */
    .admin-toast-container {
      position: fixed;
      bottom: 2rem;
      right: 2rem;
      z-index: 1000;
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
    }

    .admin-toast {
      background: rgba(22, 25, 33, 0.95);
      border: 1px solid var(--primary-gold);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
      color: #ffffff;
      padding: 0.85rem 1.2rem;
      border-radius: 10px;
      font-size: 0.88rem;
      display: flex;
      align-items: center;
      gap: 0.8rem;
      animation: slideIn 0.3s ease;
      backdrop-filter: blur(10px);
    }

    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    /* Empty State */
    .empty-state {
      padding: 3rem;
      text-align: center;
      color: var(--text-muted);
    }

    .empty-state i {
      font-size: 2.5rem;
      color: var(--primary-gold);
      margin-bottom: 1rem;
    }

    /* Print Styles */
    @media print {
      .admin-navbar, .dashboard-header .header-tools, .table-controls, .admin-toast-container {
        display: none !important;
      }
      body {
        background: #ffffff !important;
        color: #000000 !important;
      }
      .table-card {
        border: 1px solid #ccc;
        box-shadow: none;
      }
      thead th {
        background: #f0f0f0 !important;
        color: #000000 !important;
      }
      tbody td {
        color: #000000 !important;
      }
      .col-guest-name {
        color: #000000 !important;
      }
      .badge {
        border: 1px solid #999;
        color: #000 !important;
      }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <header class="admin-navbar">
    <div class="nav-brand">
      <div class="nav-logo-icon">
        <i class="fa-solid fa-crown"></i>
      </div>
      <div>
        <div class="nav-title">Ibnu &amp; Adinda</div>
        <div class="nav-subtitle">Sesi User &bull; Admin Panitia Pernikahan</div>
      </div>
    </div>
    <div class="nav-actions">
      <a href="index.php" target="_blank" class="btn-nav btn-outline">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Undangan
      </a>
      <a href="logout.php" class="btn-nav btn-logout">
        <i class="fa-solid fa-right-from-bracket"></i> Keluar
      </a>
    </div>
  </header>

  <!-- Container -->
  <main class="admin-container">
    
    <!-- Header -->
    <div class="dashboard-header">
      <div class="header-title">
        <h1>Buku Tamu &amp; Manajemen Kehadiran (RSVP)</h1>
        <p>Kelola konfirmasi tamu undangan serta checklist kehadiran fisik di lokasi resepsi.</p>
      </div>
      <div class="header-tools">
        <button class="btn-action" onclick="window.location.reload();">
          <i class="fa-solid fa-arrows-rotate"></i> Muat Ulang
        </button>
        <button class="btn-action" onclick="window.print();">
          <i class="fa-solid fa-print"></i> Cetak Daftar Tamu
        </button>
      </div>
    </div>

    <!-- Metrik Statistik -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-label">Total Konfirmasi (RSVP)</div>
        <div class="stat-value" id="stat-total"><?= $totalGuests ?></div>
        <div class="stat-sub">Semua respon tamu</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-user-check"></i></div>
        <div class="stat-label">Pasti Hadir</div>
        <div class="stat-value" id="stat-hadir" style="color: #6ee7b7;"><?= $totalHadir ?></div>
        <div class="stat-sub">Tamu menyatakan hadir</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-champagne-glasses"></i></div>
        <div class="stat-label">Estimasi Porsi / Pax</div>
        <div class="stat-value" id="stat-pax" style="color: var(--gold-light);"><?= $totalPaxHadir ?></div>
        <div class="stat-sub">Orang (dari tamu hadir)</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-user-clock"></i></div>
        <div class="stat-label">Ragu / Berhalangan</div>
        <div class="stat-value" style="color: #fcd34d;"><?= $totalRagu + $totalTidakHadir ?></div>
        <div class="stat-sub"><?= $totalRagu ?> ragu, <?= $totalTidakHadir ?> tidak hadir</div>
      </div>

      <div class="stat-card featured">
        <div class="stat-icon"><i class="fa-solid fa-clipboard-check"></i></div>
        <div class="stat-label">Hadir di Lokasi (Check-in)</div>
        <div class="stat-value" id="stat-checked-in" style="color: #6ee7b7;"><?= $totalCheckedIn ?></div>
        <div class="stat-sub">Telah diverifikasi di pintu acara</div>
      </div>
    </div>

    <!-- Controls: Filter & Search -->
    <div class="table-controls">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="guest-search" class="search-input" placeholder="Cari nama tamu atau isi ucapan...">
      </div>

      <div class="filter-group">
        <select id="filter-rsvp" class="filter-select">
          <option value="all">Semua Status RSVP</option>
          <option value="Hadir">Hadir Saja</option>
          <option value="Masih Ragu">Masih Ragu Saja</option>
          <option value="Tidak Hadir">Tidak Hadir Saja</option>
        </select>

        <select id="filter-checkin" class="filter-select">
          <option value="all">Semua Status Check-in</option>
          <option value="checked">Sudah Hadir di Lokasi</option>
          <option value="unchecked">Belum Hadir di Lokasi</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="table-card">
      <div class="table-responsive">
        <table id="guests-table">
          <thead>
            <tr>
              <th width="50">No</th>
              <th>Nama Tamu Undangan</th>
              <th>Status RSVP</th>
              <th>Pax</th>
              <th>Ucapan &amp; Doa Restu</th>
              <th>Waktu Kirim</th>
              <th width="180">Hadir di Lokasi?</th>
            </tr>
          </thead>
          <tbody id="guests-tbody">
            <?php if (empty($guests)): ?>
              <tr>
                <td colspan="7">
                  <div class="empty-state">
                    <i class="fa-regular fa-folder-open"></i>
                    <p>Belum ada data tamu atau konfirmasi kehadiran yang masuk.</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($guests as $index => $row): 
                $isChecked = (int)$row['is_checked_in'] === 1;
                $statusClass = 'badge-hadir';
                $statusLabel = 'Hadir';
                if ($row['status_rsvp'] === 'Masih Ragu') {
                    $statusClass = 'badge-ragu';
                    $statusLabel = 'Ragu-ragu';
                } elseif ($row['status_rsvp'] === 'Tidak Hadir') {
                    $statusClass = 'badge-tidakhadir';
                    $statusLabel = 'Tidak Hadir';
                }
                $initial = strtoupper(mb_substr(trim($row['name']), 0, 1, 'UTF-8')) ?: 'T';
              ?>
                <tr id="row-guest-<?= $row['id'] ?>" class="<?= $isChecked ? 'row-checked-in' : '' ?>" data-name="<?= strtolower(htmlspecialchars($row['name'])) ?>" data-rsvp="<?= $row['status_rsvp'] ?>" data-checkin="<?= $isChecked ? 'checked' : 'unchecked' ?>">
                  <td><?= $index + 1 ?></td>
                  <td>
                    <div style="display: flex; align-items: center;">
                      <span class="guest-initial"><?= $initial ?></span>
                      <span class="col-guest-name"><?= htmlspecialchars($row['name']) ?></span>
                    </div>
                  </td>
                  <td>
                    <span class="badge <?= $statusClass ?>">
                      <?= $statusLabel ?>
                    </span>
                  </td>
                  <td>
                    <span class="badge-pax"><?= (int)$row['pax'] ?> Org</span>
                  </td>
                  <td>
                    <div class="wish-message-cell">
                      "<?= htmlspecialchars($row['message']) ?>"
                    </div>
                  </td>
                  <td>
                    <span class="time-cell">
                      <?= date('d M Y, H:i', strtotime($row['created_at'])) ?>
                    </span>
                  </td>
                  <td>
                    <!-- Checkbox Check-in AJAX -->
                    <div class="checkin-switch-wrapper">
                      <label class="custom-checkbox">
                        <input type="checkbox" class="checkin-toggle" data-id="<?= $row['id'] ?>" <?= $isChecked ? 'checked' : '' ?>>
                        <span class="checkbox-mark"></span>
                      </label>
                      <span id="label-checkin-<?= $row['id'] ?>" class="checkin-status-text <?= $isChecked ? 'status-checked' : 'status-unchecked' ?>">
                        <?= $isChecked ? 'Hadir di Lokasi' : 'Belum Check-In' ?>
                      </span>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>

  <!-- Toast Container -->
  <div id="admin-toast-container" class="admin-toast-container"></div>

  <!-- Admin Interactive Scripts -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // 1. Checklist Kehadiran Fisik (Check-in) via AJAX
      const checkboxes = document.querySelectorAll(".checkin-toggle");
      const statCheckedIn = document.getElementById("stat-checked-in");

      checkboxes.forEach(chk => {
        chk.addEventListener("change", async (e) => {
          const guestId = chk.getAttribute("data-id");
          const isChecked = chk.checked ? 1 : 0;
          const row = document.getElementById(`row-guest-${guestId}`);
          const label = document.getElementById(`label-checkin-${guestId}`);

          // Optimistic UI
          if (isChecked) {
            if (row) {
              row.classList.add("row-checked-in");
              row.setAttribute("data-checkin", "checked");
            }
            if (label) {
              label.textContent = "Hadir di Lokasi";
              label.className = "checkin-status-text status-checked";
            }
          } else {
            if (row) {
              row.classList.remove("row-checked-in");
              row.setAttribute("data-checkin", "unchecked");
            }
            if (label) {
              label.textContent = "Belum Check-In";
              label.className = "checkin-status-text status-unchecked";
            }
          }

          try {
            const res = await fetch(`api/checkin.php?id=${guestId}&status=${isChecked}`, {
              method: "POST"
            });
            const result = await res.json();

            if (result.success) {
              if (statCheckedIn && result.total_checked_in !== undefined) {
                statCheckedIn.textContent = result.total_checked_in;
              }
              showAdminToast(`✓ ${result.message}`);
            } else {
              // Rollback jika gagal
              chk.checked = !chk.checked;
              showAdminToast(`⚠️ ${result.message || 'Gagal mengubah status'}`, true);
            }
          } catch (err) {
            console.error("Checkin Error:", err);
            chk.checked = !chk.checked;
            showAdminToast("⚠️ Terjadi kesalahan koneksi ke server", true);
          }
        });
      });

      // 2. Realtime Search & Filter Tamu
      const searchInput = document.getElementById("guest-search");
      const filterRsvp = document.getElementById("filter-rsvp");
      const filterCheckin = document.getElementById("filter-checkin");
      const rows = document.querySelectorAll("#guests-tbody tr[id^='row-guest-']");

      function applyFilters() {
        const query = (searchInput.value || "").toLowerCase().trim();
        const rsvpVal = filterRsvp.value;
        const checkinVal = filterCheckin.value;

        rows.forEach(row => {
          const name = row.getAttribute("data-name") || "";
          const text = row.innerText.toLowerCase();
          const rsvp = row.getAttribute("data-rsvp") || "";
          const checkin = row.getAttribute("data-checkin") || "";

          const matchesQuery = !query || name.includes(query) || text.includes(query);
          const matchesRsvp = (rsvpVal === "all") || (rsvp === rsvpVal);
          const matchesCheckin = (checkinVal === "all") || (checkin === checkinVal);

          if (matchesQuery && matchesRsvp && matchesCheckin) {
            row.style.display = "";
          } else {
            row.style.display = "none";
          }
        });
      }

      if (searchInput) searchInput.addEventListener("input", applyFilters);
      if (filterRsvp) filterRsvp.addEventListener("change", applyFilters);
      if (filterCheckin) filterCheckin.addEventListener("change", applyFilters);

      // 3. Admin Toast Notification
      function showAdminToast(msg, isError = false) {
        const container = document.getElementById("admin-toast-container");
        if (!container) return;

        const toast = document.createElement("div");
        toast.className = "admin-toast";
        if (isError) {
          toast.style.borderColor = "#ef4444";
          toast.style.boxShadow = "0 0 15px rgba(239, 68, 68, 0.4)";
        }
        toast.innerHTML = `<i class="${isError ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-circle-check'}" style="color: ${isError ? '#ef4444' : '#10b981'}"></i> <span>${msg}</span>`;

        container.appendChild(toast);

        setTimeout(() => {
          toast.style.opacity = "0";
          toast.style.transform = "translateX(100%)";
          toast.style.transition = "all 0.4s ease";
          setTimeout(() => toast.remove(), 400);
        }, 3000);
      }
    });
  </script>

</body>
</html>
