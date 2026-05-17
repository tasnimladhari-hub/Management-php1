-- ============================================================
-- HERFA Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS herfa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE herfa;

-- Users table (admin accounts)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Brands table
CREATE TABLE IF NOT EXISTS brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    category VARCHAR(80) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    brand_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE CASCADE
);

-- ── Seed data ──────────────────────────────────────────────
-- Default admin: username=admin  password=admin123
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@herfa.tn', '$2y$12$6X2bVHFgQzLNM.qT/pEFk.uEq9KNnz.3HO.VHFz5nFfQXt6fFiKAa', 'admin');
-- Password hash above = password_hash('admin123', PASSWORD_BCRYPT)

INSERT INTO brands (name, category, description, image) VALUES
('Dar El Fen',       'Pottery',  'Handmade pottery following the Nabeul tradition',       'images/potterie.png'),
('Medenine Weaves',  'Textiles', 'Traditional carpet & textile craft from the south',     'images/arpet.png'),
('Sidi Bou Said Crafts', 'Decor','Handmade candles & decor inspired by Tunisian arch.',  'images/candles.png'),
('Olive Heritage',   'Food',     'Premium natural olive oil from Tunisian groves',        'images/oil.png');

INSERT INTO products (name, brand_id, price, description) VALUES
('Clay Bowl – Blue Nabeul',     1, 45.00,  'Hand-painted ceramic bowl with traditional motifs'),
('Terracotta Vase',             1, 75.00,  'Artisan-made vase, earthy red finish'),
('Berber Carpet 120×80 cm',     2, 320.00, 'Authentic hand-woven wool carpet'),
('Woven Cushion Cover',         2, 55.00,  'Colorful kilim-style cushion cover'),
('Jasmine Candle',              3, 28.00,  'Scented soy wax candle, jasmine fragrance'),
('Blue-White Decor Set',        3, 90.00,  'Set of 3 painted pots — Sidi Bou Said palette'),
('Extra Virgin Olive Oil 500ml',4, 22.00,  'Cold-pressed, single-estate EVOO'),
('Olive Oil Gift Box',          4, 65.00,  'Two bottles + artisan soap gift set');

-- ── Cart table (session-based cart is used at runtime; this table is optional for persistence) ──
-- Uncomment below if you want to persist carts in the database instead of PHP sessions
/*
CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);
*/
