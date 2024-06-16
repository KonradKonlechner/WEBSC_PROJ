-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server-Version:               10.4.28-MariaDB - mariadb.org binary distribution
-- Server-Betriebssystem:        Win64
-- HeidiSQL Version:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Exportiere Datenbank-Struktur für pawsomedb
CREATE DATABASE IF NOT EXISTS `pawsomedb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `pawsomedb`;

DROP TABLE IF EXISTS `users`;

-- Exportiere Struktur von Tabelle pawsomedb.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `sex` enum('Keine','Herr','Frau') NOT NULL DEFAULT 'Keine',
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `address` varchar(250) NOT NULL,
  `postal_code` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_user_inactive` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `idx_username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle pawsomedb.users: ~0 rows (ungefähr)
INSERT INTO `users` (`id`, `username`, `password`, `sex`, `firstname`, `lastname`, `address`, `postal_code`, `city`, `email`, `is_admin`, `is_user_inactive`)
VALUES
    (1, 'maxim', '$2y$10$mBjBRPMcb2hsPdQ1D6dmJe3.zzo.sPr2/vY/ywC4ZRz5sUsP9RMvi', 'Herr', 'Max', 'Meier', 'Hauptstraße 123', '3210', 'Meierndorf', 'max@meier.at', 0, 0),
	(2, 'admin', '$2y$10$EDDkpyGs3izycQrfP/XFZela9Ua8HMqpNguFNpJt7wy3AgAlhgZj6', 'Herr', 'Admin', 'LeBoss', '47th floor', '47001', 'Rooftop Penthouse', 'chef.admin@mailmail.com', 1, 0);

DROP TABLE IF EXISTS `products`;

-- Exportiere Struktur von Tabelle pawsomedb.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `description` text NOT NULL,
  `category` ENUM('food','toys','accessories') NOT NULL,
  `price_per_unit_eur` float NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exportiere Daten aus Tabelle pawsomedb.products: ~0 rows (ungefähr)
INSERT INTO `products` (`id`, `name`, `description`, `category`, `price_per_unit_eur`, `image_path`, `thumbnail_path`)
VALUES
   (1, 'Whiskas Crunch', 'Whiskas Snacks Crunch Huhn, Truthahn & Ente 100g Katzensnack', 'food', 2.89, 'uploads/whiskas_crunch.jpeg', 'thumbnails/thumbnail_whiskas_crunch.jpg'),
   (2, 'Pedigree Classic', 'Pedigree Classic 12x800g 3 Sorten Geflügel', 'food', 29.99, 'uploads/pedigree_classic.jpg', 'thumbnails/thumbnail_pedigree_classic.jpg'),
   (3, 'Katzenangel 3 in 1 XXL', 'Katzenspielangel mit extra langem Stab für besonders großen Spielradius; mit drei unterschiedlichen, austauschbaren Federanhängern, befriedigt den Jagdtrieb Ihrer Katze. Gesamtlänge ca. 200 cm', 'toys', 3.49, 'uploads/cat_teasing_stick.jpg', 'thumbnails/thumbnail_cat_teasing_stick.jpg');

DROP TABLE IF EXISTS `orders`;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,  
  `total_price_eur` float NOT NULL,
  `state` enum('new','confirmed','cancelled', 'shipped') NOT NULL DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_user_id` (`user_id`) USING BTREE,    
  CONSTRAINT `FK_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=UTF8MB4_GENERAL_CI;

INSERT INTO `orders` (`id`, `user_id`, `total_price_eur`) 
VALUES
	(1, 1, 2.89);

DROP TABLE IF EXISTS `orderpositions`;

CREATE TABLE IF NOT EXISTS `orderpositions` (
  `order_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,    
  `product_id` int(11) NOT NULL,
  `quantity` int NOT NULL DEFAULT 1,  
  PRIMARY KEY (`order_id`, `position_id`) USING BTREE,
  KEY `FK_order_id` (`order_id`) USING BTREE,    
  CONSTRAINT `FK_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `FK_product_id` (`product_id`) USING BTREE,    
  CONSTRAINT `FK_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=UTF8MB4_GENERAL_CI;

INSERT INTO `orderpositions` (`order_id`, `position_id`, `product_id`, `quantity`) 
VALUES
	(1, 1, 1, 1);
	
DROP TABLE IF EXISTS `invoices`;
	
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,   
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,  
  KEY `FK_invoice_order_id` (`order_id`) USING BTREE,    
  CONSTRAINT `FK_invoice_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=UTF8MB4_GENERAL_CI;

GRANT USAGE ON *.* TO `pawsomeadmin`@`localhost` IDENTIFIED BY PASSWORD '*122F3B009504D146B213A98575C23E23B399E76C';

GRANT ALL PRIVILEGES ON `pawsomedb`.* TO `pawsomeadmin`@`localhost`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
