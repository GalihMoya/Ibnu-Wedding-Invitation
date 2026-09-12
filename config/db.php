<?php
/**
 * Database Connection & Auto-Migration
 * Ibnu Wedding Invitation
 * 
 * Menggunakan SQLite PDO yang mandiri (zero-configuration).
 * Otomatis membuat database dan tabel guests saat pertama kali diakses.
 */

// Aktifkan reporting error untuk debugging internal
error_reporting(E_ALL);
ini_set('display_errors', 0);

$dbDir = __DIR__ . '/../database';
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

$dbPath = $dbDir . '/wedding.db';

try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Buat tabel guests jika belum ada
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS guests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            status_rsvp TEXT NOT NULL,
            pax INTEGER DEFAULT 1,
            message TEXT,
            is_checked_in INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Periksa apakah tabel masih kosong, jika kosong isi dengan data contoh awal
    $count = $pdo->query("SELECT COUNT(*) FROM guests")->fetchColumn();
    if ($count == 0) {
        $stmt = $pdo->prepare("
            INSERT INTO guests (name, status_rsvp, pax, message, is_checked_in, created_at)
            VALUES (:name, :status_rsvp, :pax, :message, :is_checked_in, :created_at)
        ");

        $defaultGuests = [
            [
                'name' => 'H. Muhammad Ridwan & Keluarga',
                'status_rsvp' => 'Hadir',
                'pax' => 2,
                'message' => "Barakallahu lakuma wa baraka 'alaikuma wa jama'a bainakuma fii khoir. Semoga Ibnu & Adinda menjadi keluarga yang sakinah, mawaddah, warahmah. Aamiin!",
                'is_checked_in' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ],
            [
                'name' => 'Clarissa Dewi, S.I.Kom',
                'status_rsvp' => 'Hadir',
                'pax' => 1,
                'message' => 'Selamat untuk Adinda dan Mas Ibnu! Lancar sampai hari H yaa cantik, so happy for both of you! 🥰✨',
                'is_checked_in' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))
            ],
            [
                'name' => 'Doni Prasetyo',
                'status_rsvp' => 'Masih Ragu',
                'pax' => 1,
                'message' => 'Selamat menempuh hidup baru bro Ibnu! Diusahakan banget bisa hadir ya bro.',
                'is_checked_in' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours'))
            ]
        ];

        foreach ($defaultGuests as $guest) {
            $stmt->execute([
                ':name' => $guest['name'],
                ':status_rsvp' => $guest['status_rsvp'],
                ':pax' => $guest['pax'],
                ':message' => $guest['message'],
                ':is_checked_in' => $guest['is_checked_in'],
                ':created_at' => $guest['created_at']
            ]);
        }
    }

} catch (PDOException $e) {
    // Jika koneksi gagal, kembalikan response JSON error bila dipanggil oleh API
    if (defined('IS_API')) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
    die('Database connection failed: ' . $e->getMessage());
}
