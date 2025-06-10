CREATE DATABASE IF NOT EXISTS livestock_db;

USE livestock_db;



CREATE TABLE IF NOT EXISTS users (

    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    role ENUM('customer', 'farmer', 'vet', 'admin') NOT NULL,

    name VARCHAR(50) NOT NULL,

    email VARCHAR(50) NOT NULL UNIQUE,

    phone VARCHAR(20) NOT NULL,

    address TEXT NOT NULL,

    password VARCHAR(255) NOT NULL,

    farm_name VARCHAR(100),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);



