CREATE DATABASE IF NOT EXISTS nusawear_inventory
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nusawear_inventory;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS product_suppliers;
DROP TABLE IF EXISTS stock_movements;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS user_profiles;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE user_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  full_name VARCHAR(100) NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE suppliers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) DEFAULT NULL,
  address VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  sku VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  size VARCHAR(20) NOT NULL DEFAULT 'All Size',
  color VARCHAR(40) NOT NULL DEFAULT 'Campuran',
  price DECIMAL(12,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  minimum_stock INT NOT NULL DEFAULT 5,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_product_price CHECK (price >= 0),
  CONSTRAINT chk_product_stock CHECK (stock >= 0),
  CONSTRAINT chk_product_min_stock CHECK (minimum_stock >= 0),
  FOREIGN KEY (category_id) REFERENCES categories(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE product_suppliers (
  product_id INT NOT NULL,
  supplier_id INT NOT NULL,
  supplier_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (product_id, supplier_id),
  FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE stock_movements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  user_id INT NOT NULL,
  type ENUM('IN','OUT') NOT NULL,
  quantity INT NOT NULL,
  note VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_movement_quantity CHECK (quantity > 0),
  FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_movement_product_date (product_id, created_at)
) ENGINE=InnoDB;

INSERT INTO categories(name,description) VALUES
('Atasan','Kaos, kemeja, dan blouse'),
('Bawahan','Celana dan rok'),
('Outerwear','Jaket dan cardigan'),
('Aksesori','Aksesori fashion');

INSERT INTO suppliers(name,phone,address) VALUES
('CV Mode Nusantara','081234567890','Banda Aceh'),
('Sumber Sandang','082112223333','Medan');

INSERT INTO products(category_id,sku,name,size,color,price,stock,minimum_stock) VALUES
(1,'NW-TS-001','Kaos Basic','M','Hitam',75000,25,5),
(1,'NW-KM-002','Kemeja Linen','L','Putih',145000,12,5),
(2,'NW-CL-003','Celana Chino','L','Khaki',185000,4,5),
(3,'NW-JK-004','Jaket Denim','M','Biru',275000,8,3),
(4,'NW-TT-005','Topi Minimalis','All Size','Hitam',55000,3,5);

INSERT INTO product_suppliers(product_id,supplier_id,supplier_price) VALUES
(1,1,42000),(2,1,90000),(3,2,120000),(4,2,190000),(5,1,28000);
