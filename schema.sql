-- ============================================================
-- Ibnu & Adinda Wedding Invitation - MySQL / MariaDB Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS `db_wedding` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_wedding`;

CREATE TABLE IF NOT EXISTS `guests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `status_rsvp` ENUM('Hadir', 'Masih Ragu', 'Tidak Hadir') NOT NULL DEFAULT 'Hadir',
    `pax` INT NOT NULL DEFAULT 1,
    `message` TEXT NULL,
    `is_checked_in` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Initial Data
INSERT INTO `guests` (`name`, `status_rsvp`, `pax`, `message`, `is_checked_in`, `created_at`) VALUES
('H. Muhammad Ridwan & Keluarga', 'Hadir', 2, 'Barakallahu lakuma wa baraka \'alaikuma wa jama\'a bainakuma fii khoir. Semoga Ibnu & Adinda menjadi keluarga yang sakinah, mawaddah, warahmah. Aamiin!', 1, NOW() - INTERVAL 1 HOUR),
('Clarissa Dewi, S.I.Kom', 'Hadir', 1, 'Selamat untuk Adinda dan Mas Ibnu! Lancar sampai hari H yaa cantik, so happy for both of you! 🥰✨', 0, NOW() - INTERVAL 3 HOUR),
('Doni Prasetyo', 'Masih Ragu', 1, 'Selamat menempuh hidup baru bro Ibnu! Diusahakan banget bisa hadir ya bro.', 0, NOW() - INTERVAL 5 HOUR);
