<?php
/**
 * API: Save RSVP (Buku Tamu Konfirmasi Kehadiran)
 * Ibnu Wedding Invitation
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Hanya menerima request POST']);
    exit;
}

define('IS_API', true);
require_once __DIR__ . '/../config/db.php';

// Ambil input baik dari FormData ($_POST) maupun raw JSON body
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

$name = trim($data['name'] ?? $_POST['name'] ?? '');
$status_rsvp = trim($data['status'] ?? $data['status_rsvp'] ?? $_POST['status'] ?? $_POST['status_rsvp'] ?? '');
$pax = intval($data['pax'] ?? $_POST['pax'] ?? 1);
$message = trim($data['message'] ?? $_POST['message'] ?? '');

// Validasi
if (empty($name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nama tamu wajib diisi']);
    exit;
}

$validStatuses = ['Hadir', 'Masih Ragu', 'Tidak Hadir'];
if (!in_array($status_rsvp, $validStatuses)) {
    // Normalisasi jika ada variasi string
    if (stripos($status_rsvp, 'Hadir') !== false && stripos($status_rsvp, 'Tidak') === false) {
        $status_rsvp = 'Hadir';
    } elseif (stripos($status_rsvp, 'Ragu') !== false) {
        $status_rsvp = 'Masih Ragu';
    } else {
        $status_rsvp = 'Tidak Hadir';
    }
}

if ($pax < 1) $pax = 1;
if ($pax > 10) $pax = 10;

if (empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ucapan & Doa wajib diisi']);
    exit;
}

try {
    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("
        INSERT INTO guests (name, status_rsvp, pax, message, is_checked_in, created_at)
        VALUES (:name, :status_rsvp, :pax, :message, 0, :created_at)
    ");
    $stmt->execute([
        ':name' => $name,
        ':status_rsvp' => $status_rsvp,
        ':pax' => $pax,
        ':message' => $message,
        ':created_at' => $now
    ]);

    $insertId = (int)$pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Konfirmasi kehadiran dan doa Anda berhasil disimpan!',
        'data' => [
            'id' => $insertId,
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'status' => $status_rsvp,
            'pax' => $pax,
            'message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
            'time' => 'Baru saja',
            'created_at' => $now
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan data: ' . $e->getMessage()
    ]);
}
