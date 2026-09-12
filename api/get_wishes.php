<?php
/**
 * API: Get Wishes (Buku Ucapan & RSVP)
 * Ibnu Wedding Invitation
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

define('IS_API', true);
require_once __DIR__ . '/../config/db.php';

try {
    // Ambil data ucapan terurut dari yang paling baru
    $stmt = $pdo->query("
        SELECT id, name, status_rsvp, pax, message, created_at 
        FROM guests 
        ORDER BY id DESC
    ");
    $guests = $stmt->fetchAll();

    // Fungsi format waktu human-friendly dalam Bahasa Indonesia
    function timeElapsedString($datetime) {
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        if ($diff->y > 0) return $diff->y . ' tahun yang lalu';
        if ($diff->m > 0) return $diff->m . ' bulan yang lalu';
        if ($diff->d > 0) return $diff->d . ' hari yang lalu';
        if ($diff->h > 0) return $diff->h . ' jam yang lalu';
        if ($diff->i > 0) return $diff->i . ' menit yang lalu';
        return 'Baru saja';
    }

    $wishes = [];
    foreach ($guests as $row) {
        $wishes[] = [
            'id' => (int)$row['id'],
            'name' => htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'),
            'status' => $row['status_rsvp'],
            'pax' => (int)$row['pax'],
            'message' => htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8'),
            'time' => timeElapsedString($row['created_at']),
            'created_at' => $row['created_at']
        ];
    }

    echo json_encode([
        'success' => true,
        'count' => count($wishes),
        'data' => $wishes
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengambil data ucapan: ' . $e->getMessage()
    ]);
}
