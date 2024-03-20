<?php
require_once "dbaccess.php";

if (
    !isset($dbHost)
    || !isset($dbUsername)
    || !isset($dbPassword)
    || !isset($dbName)
) {
    return;
}

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

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
        `email` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
        `is_admin` TINYINT(1) NOT NULL DEFAULT '0',
        `is_user_inactive` TINYINT(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`) USING BTREE,
        UNIQUE INDEX `idx_username` (`username`) USING BTREE
    )
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;"
    ,
    "INSERT INTO `users` (`id`, `username`, `password`, `sex`, `firstname`, `lastname`, `email`, `is_admin`) 
    VALUES 
    (1, 'maxim', '$2y$10$5gbnNwXMXJgVNjgB8M14beW.QfGgiMl7/8MvrNJePhuA.WEdPwozS', 'Herr', 'Max', 'Meier', 'max@meier.at', 0),
    (2, 'admin', '$hashedAdminPW', 'Herr', 'Admin', 'LeBoss', 'chef.admin@mailmail.com', 1);"
    ,
    "CREATE TABLE `news_articles` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `headline` VARCHAR(80) NOT NULL COLLATE 'utf8mb4_general_ci',
        `description` TEXT NOT NULL COLLATE 'utf8mb4_general_ci',
        `image_path` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
        `thumbnail_path` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
        `created_at` DATETIME NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`) USING BTREE,
        INDEX `FK_news_user_id` (`user_id`) USING BTREE,
        CONSTRAINT `FK_news_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
    )
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;"
    ,
    "INSERT INTO `news_articles` (`id`, `user_id`, `headline`, `description`, `image_path`, `thumbnail_path`, `created_at`)
    VALUES 
        (1, 2, 'Frohe Weihnachten!', 'Wir wünschen schöne Feiertage!', 'uploads/christmas_tree.jpg', 'thumbnails/thumbnail_christmas_tree.jpg', '2023-12-21 23:19:52'),
        (2, 2, 'Die Ballsaison ist eröffnet!', 'Die Wiener Bälle laden zum Tanz ein!', 'uploads/taenzer.jpg', 'thumbnails/thumbnail_taenzer.jpg', '2024-01-09 00:17:01');"
    ,
    "CREATE TABLE `bed_types` (
        `id` INT(11) NOT NULL,
        `bed_type_name` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
        `width_cm` INT(11) NOT NULL,
        `length_cm` INT(11) NOT NULL,
        PRIMARY KEY (`id`) USING BTREE
    )
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;"
    ,
    "INSERT INTO `bed_types` (`id`, `bed_type_name`, `width_cm`, `length_cm`) 
    VALUES 
    (1, 'comfort', 140, 200),
    (2, 'queen-size', 160, 200),
    (3, 'king-size', 200, 220);"
    ,
    "CREATE TABLE `room_types` (
        `id` INT(11) NOT NULL,
        `room_type_name` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
        `pic_filepath_name` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
        `room_size_range_square_meters` VARCHAR(10) NOT NULL COLLATE 'utf8mb4_general_ci',
        `bed_type` INT(11) NOT NULL,
        `has_minibar` TINYINT(1) NOT NULL,
        `price_per_person_per_night_eur` FLOAT NOT NULL,
        PRIMARY KEY (`id`) USING BTREE,
        INDEX `FK_bed_type` (`bed_type`) USING BTREE,
        CONSTRAINT `FK_bed_type` FOREIGN KEY (`bed_type`) REFERENCES `bed_types` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT
    )
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;"
    ,
    "INSERT INTO `room_types` (`id`, `room_type_name`, `pic_filepath_name`, `room_size_range_square_meters`, `bed_type`, `has_minibar`, `price_per_person_per_night_eur`) 
    VALUES 
    (1, 'Deluxe Zimmer', 'deluxe_room', '27-39', 1, 0, 300),
    (2, 'Junior Suite', 'junior_suite', '40-55', 2, 1, 550),
    (3, 'Signature Suite', 'signature_suite', '50-65', 3, 1, 800),
    (4, 'Grand Suite', 'grand_suite', '> 95', 3, 1, 1400);"
    ,
    "CREATE TABLE `rooms` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `room_number` INT(11) NOT NULL,
        `room_type` INT(11) NOT NULL,
        `capacity` INT(11) NOT NULL,
        PRIMARY KEY (`id`) USING BTREE,
        INDEX `FK_room_type` (`room_type`) USING BTREE,
        CONSTRAINT `FK_room_type` FOREIGN KEY (`room_type`) REFERENCES `room_types` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT
    )
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;"
    ,
    "INSERT INTO `rooms` (`id`, `room_number`, `room_type`, `capacity`)
    VALUES 
    (1, 101, 1, 1), (2, 102, 1, 2), (3, 103, 1, 2), (4, 104, 1, 2), (5, 105, 1, 2), (6, 106, 1, 3), (7, 107, 1, 3), (8, 108, 2, 2), (9, 109, 2, 3), (10, 110, 2, 3), (11, 111, 2, 4), (12, 112, 3, 3), (13, 113, 3, 4),
    (14, 201, 1, 1), (15, 202, 1, 2), (16, 203, 1, 2), (17, 204, 1, 2), (18, 205, 1, 2), (19, 206, 1, 3), (20, 207, 1, 3), (21, 208, 2, 2), (22, 209, 2, 3), (23, 210, 2, 3), (24, 211, 2, 4), (25, 212, 3, 2), (26, 213, 3, 3),
    (27, 301, 1, 1), (28, 302, 1, 2), (29, 303, 1, 2), (30, 304, 1, 2), (31, 305, 1, 2), (32, 306, 1, 3), (33, 307, 1, 3), (34, 308, 2, 2), (35, 309, 2, 3), (36, 310, 2, 3), (37, 311, 2, 4), (38, 312, 3, 3), (39, 313, 3, 5),
    (40, 401, 4, 3), (41, 402, 4, 3), (42, 403, 4, 4), (43, 404, 4, 6);"
    ,
    "CREATE TABLE `reservations` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `room_id` INT(11) NOT NULL,
        `start_date` DATE NOT NULL,
        `end_date` DATE NOT NULL,
        `number_of_persons` INT(11) NOT NULL,
        `has_breakfast` TINYINT(1) NOT NULL,
        `number_of_parking_lots` INT(11) NOT NULL,
        `number_of_pets` INT(11) NOT NULL,
        `comment` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
        `state` ENUM('new','confirmed','cancelled') NOT NULL DEFAULT 'new' COLLATE 'utf8mb4_general_ci',
        `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`) USING BTREE,
        INDEX `FK_user_id` (`user_id`) USING BTREE,
        INDEX `FK_room_id` (`room_id`) USING BTREE,
        CONSTRAINT `FK_room_id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT,
        CONSTRAINT `FK_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `chk_end_date` CHECK (`end_date` >= `start_date`)
    )
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;"
    ,
    "INSERT INTO `reservations` (`id`, `user_id`, `room_id`, `start_date`, `end_date`, `number_of_persons`, `has_breakfast`, `number_of_parking_lots`, `number_of_pets`, `comment`, `state`, `created_at`) 
    VALUES 
    (1, 1, 39, '2024-01-20', '2024-01-27', 5, 1, 1, 0, NULL, 'confirmed', '2024-01-13 20:25:23'),
    (2, 1, 43, '2024-01-14', '2024-01-19', 5, 1, 1, 0, 'Sehr geehrte Damen und Herren! Ich bitte täglich um Punkt 19 Uhr eine Flasche Single Malt Scotch Whiskey auf mein Zimmer bringen zu lassen. Herzlichen Dank! Mit besten Grüßen, Prof. Dr. Max Meier', 'cancelled', '2024-01-14 16:47:57'),
    (3, 1, 6, '2024-01-28', '2024-02-03', 3, 1, 0, 0, '', 'new', '2024-01-14 16:55:51');"
    ,
    "CREATE TABLE `charged_options` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `option_name` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
        `price_per_unit_eur` FLOAT NOT NULL,
        PRIMARY KEY (`id`) USING BTREE
    )
    COLLATE='utf8mb4_general_ci'
    ENGINE=InnoDB;"
    ,
    "INSERT INTO `charged_options` (`id`, `option_name`, `price_per_unit_eur`) 
    VALUES 
    (1, 'beer_from_minibar_0.33l', 8),
    (2, 'pet', 50),
    (3, 'breakfast', 22.4),
    (4, 'parking lot', 75);"
    
];

foreach ($tableSqlList as $tableSql) {
    try {
        if ($connection->query($tableSql) === TRUE) {
            echo "<script>console.log('Created table using:" . $tableSql . "');</script>";
        } else {
            echo '<script>console.log("Error creating table: '
                . $connection->error
                . '");</script>';
        }
    } catch (mysqli_sql_exception $mysqli_sql_exception) {
        echo '<script>console.log("Error creating table: '
            . $mysqli_sql_exception->getMessage()
            . '");</script>';
    }
}

?>