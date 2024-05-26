<?php
namespace db;

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
    (1, 'maxim', '$hashedMaximPW', 'Herr', 'Max', 'Meier', 'max@meier.at', 0),
    (2, 'admin', '$hashedAdminPW', 'Herr', 'Admin', 'LeBoss', 'chef.admin@mailmail.com', 1);"
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