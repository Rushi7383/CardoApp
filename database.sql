-- Cardo - Business Card Making App
-- Database Schema

-- Drop tables if they exist to ensure a clean slate on re-runs
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `queries`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `templates`;
DROP TABLE IF EXISTS `template_categories`;
DROP TABLE IF EXISTS `banners`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `admin`;

-- Admin Table
-- Stores login credentials for the admin panel
CREATE TABLE `admin` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `last_login` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pre-populating the admin user as per the requirements
-- The password is '$$$Rushi@12#'
INSERT INTO `admin` (`username`, `password`) VALUES ('rushikeshmurhekar2@gmail.com', '$2y$10$.eR61BHJ9T7mhC8Yk6hp2OyosmxuCpoWNZu6Flhtn5lKz1DgfZhg.');

-- Users Table
-- Stores information about registered users
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `last_login` DATETIME NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Banners Table
-- Stores sliding banners for the home page
CREATE TABLE `banners` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `image_path` VARCHAR(255) NOT NULL,
  `text` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Template Categories Table
-- To categorize the business card templates
CREATE TABLE `template_categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Templates Table
-- Stores the business card templates
CREATE TABLE `templates` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `category_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `template_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders Table
-- Stores user orders for business cards
CREATE TABLE `orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `template_id` INT NOT NULL,
  `owner_name` VARCHAR(255) NOT NULL,
  `business_name` VARCHAR(255) NOT NULL,
  `address` TEXT NOT NULL,
  `mobile` VARCHAR(20) NOT NULL,
  `status` ENUM('Pending', 'Order Submitted', 'Order Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `final_card_path` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`template_id`) REFERENCES `templates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payments Table
-- Stores payment information related to orders
CREATE TABLE `payments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `utr_number` VARCHAR(255) NOT NULL,
  `screenshot_path` VARCHAR(255) NOT NULL,
  `status` ENUM('Pending', 'Payment Done') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Queries Table
-- For the user support/chat system
CREATE TABLE `queries` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `message` TEXT NOT NULL,
  `reply` TEXT NULL,
  `status` ENUM('Open', 'Resolved', 'Live', 'Online') NOT NULL DEFAULT 'Open',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
