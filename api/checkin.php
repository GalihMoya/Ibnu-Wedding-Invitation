<?php
/**
 * API: Check-in Tamu di Lokasi (Khusus Admin)
 * Ibnu Wedding Invitation
 */

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Proteksi Sesi Admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak. Sesi admin diperlukan.'
    ]);
    exit;
}

define('IS_API', true);
require_once __DIR__ . '/../config/db.php';

// Terima parameter id dan status baik dari GET, POST, atau JSON
$rawInput = file_get_contents('php://input');
$json = json_decode($rawInput, true);

$id = intval($json['id'] ?? $_POST['id'] ?? $_GET['id'] ?? 0);
$status = isset($json['status']) ? intval($json['status']) : (isset($_POST['status']) ? intval($_POST['status']) : (isset($_GET['status']) ? intval($_GET['status']) : -1));

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID tamu tidak valid']);
    exit;
}

// Jika status tidak dikirim, toggle status saat ini
try {
    if ($status !== 0 && $status !== 1) {
        $stmtCurrent = $pdo->prepare("SELECT is_checked_in FROM guests WHERE id = :id");
        $stmtCurrent->execute([':id' => $id]);
        $current = $stmtCurrent->fetchColumn();
        if ($current === false) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Data tamu tidak ditemukan']);
            exit;
        }
        $status = ($current == 1) ? 0 : 1;
    }

    $stmt = $pdo->prepare("UPDATE guests SET is_checked_in = :status WHERE id = :id");
    $stmt->execute([
        ':status' => $status,
        ':id' => $id
    ]);

    // Hitung ulang total check-in untuk realtime badge update di admin
    $totalCheckedIn = $pdo->query("SELECT COUNT(*) FROM guests WHERE is_checked_in = 1")->fetchColumn();

    echo json_encode([
        'success' => true,
        'id' => $id,
        'is_checked_in' => $status,
        'total_checked_in' => (int)$totalCheckedIn,
        'message' => $status === 1 ? 'Tamu berhasil di-checklist hadir di lokasi!' : 'Status check-in tamu dibatalkan.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal memperbarui status: ' . $e->getMessage()
    ]);
}
