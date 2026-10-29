
DROP DATABASE IF EXISTS sarenh;
CREATE DATABASE sarenh;
USE sarenh;

CREATE TABLE User (
    user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(50) NOT NULL,
    user_password VARCHAR(255) NOT NULL,
    user_email VARCHAR(150) NOT NULL UNIQUE,
    user_category VARCHAR(50) NOT NULL,
    user_subject VARCHAR(50)
);

CREATE TABLE slot (
    slot_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    slot_name VARCHAR(100) NOT NULL,
    slot_institutional_name VARCHAR(150),
    slot_description VARCHAR(500)
);

CREATE TABLE reservation (
    reserved_slot_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    reserved_slot_user_id INT NOT NULL,
    reserved_slot_slot_id INT NOT NULL,
    reserved_slot_topic VARCHAR(255) NOT NULL,
    reserved_slot_start_time TIME NOT NULL,
    reserved_slot_end_time TIME NOT NULL,
    reserved_slot_date DATE NOT NULL,
    reserved_slot_reason VARCHAR(255),
    reserved_slot_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reserved_slot_status ENUM('pending', 'confirmed', 'rejected', 'cancelled')
        NOT NULL DEFAULT 'pending',
    FOREIGN KEY (reserved_slot_user_id)
        REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (reserved_slot_slot_id)
        REFERENCES slot(slot_id) ON DELETE CASCADE,
    CONSTRAINT chk_reservation_time
        CHECK (reserved_slot_end_time > reserved_slot_start_time)
);
