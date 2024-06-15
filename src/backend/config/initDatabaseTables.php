<?php
namespace db;

use mysqli_sql_exception;

require_once "./dbaccess.php";

$dbconnection = new DBConnection();

$connection = $dbconnection->getConnection();

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$hashedMaximPW = '$2y$10$mBjBRPMcb2hsPdQ1D6dmJe3.zzo.sPr2/vY/ywC4ZRz5sUsP9RMvi';
$hashedAdminPW = '$2y$10$EDDkpyGs3izycQrfP/XFZela9Ua8HMqpNguFNpJt7wy3AgAlhgZj6';

// sql to create table
$tableSqlList = [
    "CREATE TABLE `users` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `username` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
        `password` VARCHAR(250) NOT NULL COLLATE 'utf8mb4_general_ci',
        `sex` ENUM('Keine','Herr','Frau') NOT NULL DEFAULT 'Keine' COLLATE 'utf8mb4_general_ci',
        `firstname` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
        `lastname` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
        `address` varchar(250) NOT NULL COLLATE 'utf8mb4_general_ci',
        `postal_code` varchar(50) NOT NULL COLLATE 'utf8mb4_general_ci',
        `city` varchar(50) NOT NULL COLLATE 'utf8mb4_general_ci',
        `email` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
        `is_admin` TINYINT(1) NOT NULL DEFAULT '0',
        `is_user_inactive` TINYINT(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`) USING BTREE,
        UNIQUE INDEX `idx_username` (`username`) USING BTREE
    )
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;"
    ,
    "INSERT INTO `users` (`id`, `username`, `password`, `sex`, `firstname`, `lastname`, `address`, `postal_code`, `city`, `email`, `is_admin`) 
    VALUES 
    (1, 'maxim', '$hashedMaximPW', 'Herr', 'Max', 'Meier', 'Hauptstraße 123', '3210', 'Meierndorf', 'max@meier.at', 0),
    (2, 'admin', '$hashedAdminPW', 'Herr', 'Admin', 'LeBoss', '47th floor', '47001', 'Rooftop Penthouse', 'chef.admin@mailmail.com', 1);"
    ,
    "CREATE TABLE `products` (
          `id` int(11) NOT NULL AUTO_INCREMENT,  
          `name` VARCHAR(50) NOT NULL,
          `description` text NOT NULL,  
          `category` ENUM('food','toys','accessories') NOT NULL,
          `price_per_unit_eur` float NOT NULL,
          `image_path` varchar(255) DEFAULT NULL,
          `thumbnail_path` varchar(255) DEFAULT NULL,
          `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP(),  
          PRIMARY KEY (`id`) USING BTREE
    ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ,
    "INSERT INTO `products` (`id`, `name`, `description`, `category`, `price_per_unit_eur`, `image_path`, `thumbnail_path`) 
    VALUES
    (1, 'Whiskas Crunch', 'Whiskas Snacks Crunch Huhn, Truthahn & Ente 100g Katzensnack', 'food', 2.89, 'uploads/whiskas_crunch.jpeg', 'thumbnails/thumbnail_whiskas_crunch.jpg'),
	(2, 'Pedigree Classic', 'Pedigree Classic 12x800g 3 Sorten Geflügel ', 'food', 29.99, 'uploads/pedigree_classic.jpg', 'thumbnails/thumbnail_pedigree_classic.jpg')
    (3, 'Katzenangel 3 in 1 XXL', 'Katzenspielangel mit extra langem Stab für besonders großen Spielradius; mit drei unterschiedlichen, austauschbaren Federanhängern, befriedigt den Jagdtrieb Ihrer Katze. Gesamtlänge ca. 200 cm', 'toys', 3.49, 'uploads/cat_teasing_stick.jpg', 'thumbnails/thumbnail_cat_teasing_stick.jpg');"
    ,
    "CREATE TABLE `orders` (
       `id` int(11) NOT NULL AUTO_INCREMENT,
       `user_id` int(11) NOT NULL,
       `total_price_eur` float NOT NULL,
       `state` enum('new','confirmed','cancelled', 'shipped') NOT NULL DEFAULT 'new',
       `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
       PRIMARY KEY (`id`) USING BTREE,
       KEY `FK_user_id` (`user_id`) USING BTREE,
       CONSTRAINT `FK_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
     ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=UTF8MB4_GENERAL_CI;"
    ,
    "INSERT INTO `orders` (`id`, `user_id`, `total_price_eur`) 
    VALUES
    (1, 1, 2.89);"
     ,
     "CREATE TABLE `orderpositions` (
        `order_id` int(11) NOT NULL,
        `position_id` int(11) NOT NULL,
        `product_id` int(11) NOT NULL,
        `quantity` int NOT NULL DEFAULT 1,
        PRIMARY KEY (`order_id`, `position_id`) USING BTREE,
        KEY `FK_order_id` (`order_id`) USING BTREE,
        CONSTRAINT `FK_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        KEY `FK_product_id` (`product_id`) USING BTREE,
        CONSTRAINT `FK_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
      ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ,
    "INSERT INTO `orderpositions` (`order_id`, `position_id`, `product_id`, `quantity`) 
    VALUES
    (1, 1, 1, 1);"
    ,
    "CREATE TABLE IF NOT EXISTS `invoices` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
      `order_id` int(11) NOT NULL,   
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`) USING BTREE,
      KEY `FK_invoice_order_id` (`order_id`) USING BTREE,    
      CONSTRAINT `FK_invoice_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=UTF8MB4_GENERAL_CI;"
];

foreach ($tableSqlList as $tableSql) {
    try {
        if ($connection->query($tableSql) === TRUE) {
            echo "Created table using: " . $tableSql;
        } else {
            echo "Error creating table: " . $connection->error;
        }
    } catch (mysqli_sql_exception $mysqli_sql_exception) {
        echo "Error creating table: " . $mysqli_sql_exception->getMessage();
    }
}