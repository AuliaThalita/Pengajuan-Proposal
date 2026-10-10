-- =====================================================
-- Database: ukm_proposal
-- Sistem Informasi Pengajuan & Monitoring Kegiatan UKM
-- Biro Kemahasiswaan (Birmawa) - Universitas Mercu Buana
-- Import: mysql -u root -p < ukm_proposal.sql
-- =====================================================
CREATE DATABASE IF NOT EXISTS ukm_proposal CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ukm_proposal;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS proposal_log, dokumen, proposals, users, ukm;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE ukm (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama_ukm    VARCHAR(150) NOT NULL,
  deskripsi   TEXT NULL,
  pembina_id  INT UNSIGNED NULL,
  created_at  DATETIME NULL,
  updated_at  DATETIME NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE users (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama        VARCHAR(100) NOT NULL,
  email       VARCHAR(100) NOT NULL,
  password    VARCHAR(255) NOT NULL,
  role        ENUM('ukm','pembina','birmawa') NOT NULL,
  ukm_id      INT UNSIGNED NULL COMMENT 'hanya untuk role ukm',
  created_at  DATETIME NULL,
  updated_at  DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email),
  CONSTRAINT fk_users_ukm FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE SET NULL
) ENGINE=InnoDB;

ALTER TABLE ukm
  ADD CONSTRAINT fk_ukm_pembina FOREIGN KEY (pembina_id) REFERENCES users(id) ON DELETE SET NULL;

CREATE TABLE proposals (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ukm_id          INT UNSIGNED NOT NULL,
  diajukan_oleh   INT UNSIGNED NULL,
  judul           VARCHAR(200) NOT NULL,
  latar_belakang  TEXT NOT NULL,
  tujuan          TEXT NOT NULL,
  sasaran         VARCHAR(255) NOT NULL,
  tanggal_mulai   DATE NOT NULL,
  tanggal_selesai DATE NOT NULL,
  lokasi          VARCHAR(200) NOT NULL,
  anggaran        DECIMAL(15,2) NOT NULL DEFAULT 0,
  status          ENUM('diajukan','disetujui_pembina','ditolak_pembina','divalidasi','ditolak_birmawa') NOT NULL DEFAULT 'diajukan',
  catatan_pembina TEXT NULL,
  catatan_birmawa TEXT NULL,
  created_at      DATETIME NULL,
  updated_at      DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_proposals_status (status),
  CONSTRAINT fk_prop_ukm  FOREIGN KEY (ukm_id) REFERENCES ukm(id) ON DELETE RESTRICT,
  CONSTRAINT fk_prop_user FOREIGN KEY (diajukan_oleh) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE dokumen (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  proposal_id INT UNSIGNED NOT NULL,
  jenis       VARCHAR(30) NOT NULL COMMENT 'proposal | surat_pengantar | rab | panitia',
  nama_asli   VARCHAR(255) NOT NULL,
  nama_file   VARCHAR(255) NOT NULL,
  ukuran      INT UNSIGNED NOT NULL DEFAULT 0,
  created_at  DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_dok_prop FOREIGN KEY (proposal_id) REFERENCES proposals(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE proposal_log (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  proposal_id INT UNSIGNED NOT NULL,
  user_id     INT UNSIGNED NULL,
  status      VARCHAR(30) NOT NULL,
  catatan     TEXT NULL,
  created_at  DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_log_prop FOREIGN KEY (proposal_id) REFERENCES proposals(id) ON DELETE CASCADE,
  CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------- DATA CONTOH (password semua akun: Password123) ----------------
SET @pw = '$2y$12$GyQREdkkz11o/dzW7ACL6O0ios8nNYvd7kmU/WKL7vEQa7F/pef.a';

INSERT INTO ukm (id, nama_ukm, deskripsi, created_at, updated_at) VALUES
 (1, 'UKM Robotika',     'Pengembangan minat robotika dan teknologi.', NOW(), NOW()),
 (2, 'UKM Basket',       'Pembinaan olahraga bola basket.',            NOW(), NOW()),
 (3, 'UKM Paduan Suara', 'Pengembangan seni musik vokal.',             NOW(), NOW());

INSERT INTO users (id, nama, email, password, role, ukm_id, created_at, updated_at) VALUES
 (1, 'Admin Birmawa',          'birmawa@mercubuana.ac.id',     @pw, 'birmawa', NULL, NOW(), NOW()),
 (2, 'Dr. Budi Santoso',       'pembina1@mercubuana.ac.id',    @pw, 'pembina', NULL, NOW(), NOW()),
 (3, 'Siti Rahayu, M.Kom.',    'pembina2@mercubuana.ac.id',    @pw, 'pembina', NULL, NOW(), NOW()),
 (4, 'Ketua UKM Robotika',     'robotika@mercubuana.ac.id',    @pw, 'ukm', 1, NOW(), NOW()),
 (5, 'Ketua UKM Basket',       'basket@mercubuana.ac.id',      @pw, 'ukm', 2, NOW(), NOW()),
 (6, 'Ketua UKM Paduan Suara', 'paduansuara@mercubuana.ac.id', @pw, 'ukm', 3, NOW(), NOW());

UPDATE ukm SET pembina_id = 2 WHERE id IN (1,2);
UPDATE ukm SET pembina_id = 3 WHERE id = 3;

INSERT INTO proposals (id, ukm_id, diajukan_oleh, judul, latar_belakang, tujuan, sasaran, tanggal_mulai, tanggal_selesai, lokasi, anggaran, status, catatan_pembina, catatan_birmawa, created_at, updated_at) VALUES
 (1,1,4,'Workshop Arduino untuk Pemula','Meningkatnya minat mahasiswa terhadap robotika.','Memberi dasar pemrograman mikrokontroler.','Mahasiswa semua fakultas','2026-11-10','2026-11-10','Lab Teknik Lt. 3',3500000,'diajukan',NULL,NULL,DATE_SUB(NOW(), INTERVAL 2 DAY),NOW()),
 (2,1,4,'Kontes Robot Internal','Wadah kompetisi anggota UKM.','Menyaring tim untuk kompetisi nasional.','Anggota UKM Robotika','2026-12-05','2026-12-06','Aula Kampus',8000000,'disetujui_pembina','Disetujui, mohon lengkapi RAB.',NULL,DATE_SUB(NOW(), INTERVAL 20 DAY),NOW()),
 (3,2,5,'Turnamen Basket Antar Fakultas','Mempererat hubungan antar fakultas.','Mencari bibit atlet basket.','Mahasiswa aktif','2026-10-25','2026-10-27','GOR Kampus',12000000,'divalidasi','Setuju.','Dokumen lengkap.',DATE_SUB(NOW(), INTERVAL 45 DAY),NOW()),
 (4,2,5,'Latihan Gabungan Regional','Persiapan kompetisi regional.','Meningkatkan kemampuan tim.','Tim inti','2026-11-15','2026-11-15','GOR Kampus',2500000,'ditolak_pembina','Jadwal bentrok dengan UTS, mohon ubah tanggal.',NULL,DATE_SUB(NOW(), INTERVAL 70 DAY),NOW()),
 (5,3,6,'Konser Amal Akhir Tahun','Kegiatan sosial melalui seni musik.','Menggalang dana sosial.','Masyarakat umum','2026-12-20','2026-12-20','Auditorium',15000000,'ditolak_birmawa','Setuju.','Surat pengantar belum diunggah.',DATE_SUB(NOW(), INTERVAL 100 DAY),NOW());

INSERT INTO proposal_log (proposal_id, user_id, status, catatan, created_at) VALUES
 (1,4,'diajukan','Proposal diajukan.',DATE_SUB(NOW(), INTERVAL 2 DAY)),
 (2,4,'diajukan','Proposal diajukan.',DATE_SUB(NOW(), INTERVAL 20 DAY)),
 (2,2,'disetujui_pembina','Disetujui, mohon lengkapi RAB.',DATE_SUB(NOW(), INTERVAL 18 DAY)),
 (3,5,'diajukan','Proposal diajukan.',DATE_SUB(NOW(), INTERVAL 45 DAY)),
 (3,2,'disetujui_pembina','Setuju.',DATE_SUB(NOW(), INTERVAL 43 DAY)),
 (3,1,'divalidasi','Dokumen lengkap.',DATE_SUB(NOW(), INTERVAL 40 DAY)),
 (4,5,'diajukan','Proposal diajukan.',DATE_SUB(NOW(), INTERVAL 70 DAY)),
 (4,2,'ditolak_pembina','Jadwal bentrok dengan UTS, mohon ubah tanggal.',DATE_SUB(NOW(), INTERVAL 68 DAY)),
 (5,6,'diajukan','Proposal diajukan.',DATE_SUB(NOW(), INTERVAL 100 DAY)),
 (5,3,'disetujui_pembina','Setuju.',DATE_SUB(NOW(), INTERVAL 98 DAY)),
 (5,1,'ditolak_birmawa','Surat pengantar belum diunggah.',DATE_SUB(NOW(), INTERVAL 95 DAY));
