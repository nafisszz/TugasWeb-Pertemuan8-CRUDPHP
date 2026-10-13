CREATE DATABASE IF NOT EXISTS inventaris_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE inventaris_db;

CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    kota VARCHAR(100) NOT NULL,
    telepon VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(20) NOT NULL UNIQUE,
    nama_produk VARCHAR(150) NOT NULL,
    kategori_id INT NOT NULL,
    supplier_id INT NOT NULL,
    harga DECIMAL(12,0) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id),
    FOREIGN KEY (supplier_id) REFERENCES supplier(id)
) ENGINE=InnoDB;

-- tabel tambahan untuk bonus log aktivitas
CREATE TABLE log_aktivitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aksi VARCHAR(20) NOT NULL,
    keterangan VARCHAR(255) NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO kategori (nama_kategori) VALUES
('Laptop'),
('Aksesoris'),
('Perangkat Jaringan'),
('Penyimpanan'),
('Monitor');

INSERT INTO supplier (nama_supplier, kota, telepon) VALUES
('PT Mitra Teknologi', 'Medan', '061-4521890'),
('CV Sinar Digital', 'Jakarta', '021-5567123'),
('Toko Maju Jaya', 'Bandung', '022-7301456'),
('PT Nusa Komputer', 'Surabaya', '031-8824017'),
('CV Data Prima', 'Medan', '061-4478210');

INSERT INTO produk (kode_produk, nama_produk, kategori_id, supplier_id, harga, stok) VALUES
('LPT-001', 'Laptop Asus Vivobook 14', 1, 1, 7450000, 12),
('AKS-001', 'Mouse Wireless Logitech M185', 2, 2, 135000, 40),
('AKS-002', 'Keyboard Mechanical Rexus', 2, 2, 420000, 18),
('JRG-001', 'Router TP-Link Archer C6', 3, 3, 489000, 15),
('JRG-002', 'Switch 8 Port TP-Link', 3, 3, 210000, 22),
('STR-001', 'SSD Kingston 480GB', 4, 4, 520000, 30),
('STR-002', 'Flashdisk Sandisk 64GB', 4, 5, 85000, 8),
('MON-001', 'Monitor LG 24 Inci', 5, 5, 1650000, 9);