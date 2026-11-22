-- Bright-Market Laravel POS — MySQL / MariaDB Schema & Seed Export
-- Compatible with MySQL 8.0+ / MariaDB 10.4+ (phpMyAdmin & InfinityFree)

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'Staff Member',
  `avatar` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `sold_by` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_product_id_foreign` (`product_id`),
  KEY `sales_customer_id_foreign` (`customer_id`),
  KEY `sales_sold_by_foreign` (`sold_by`),
  CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sales_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `sales_sold_by_foreign` FOREIGN KEY (`sold_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `username`, `full_name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 'admin.reyes', 'Carlos Reyes', 'admin.reyes@brightmarket.test', '2026-01-15 08:00:00', '$2y$12$XYewY6BevthY2OoR3b92FeBE3O6YKpuOhOoAUdbSZ4ak2cwYPT5hK', 'Store Administrator', 'avatars/admin.reyes.jpg', '2026-01-15 08:00:00', '2026-01-15 08:00:00'),
(2, 'mgr.castro', 'Beatriz Castro', 'mgr.castro@brightmarket.test', '2026-01-20 08:30:00', '$2y$12$XYewY6BevthY2OoR3b92FeBE3O6YKpuOhOoAUdbSZ4ak2cwYPT5hK', 'Shift Supervisor', 'avatars/mgr.castro.jpg', '2026-01-20 08:30:00', '2026-01-20 08:30:00'),
(3, 'cashier.valdez', 'Daniel Valdez', 'cashier.valdez@brightmarket.test', '2026-02-01 09:00:00', '$2y$12$XYewY6BevthY2OoR3b92FeBE3O6YKpuOhOoAUdbSZ4ak2cwYPT5hK', 'Cashier', 'avatars/cashier.valdez.jpg', '2026-02-01 09:00:00', '2026-02-01 09:00:00'),
(4, 'cashier.santos', 'Camille Santos', 'cashier.santos@brightmarket.test', '2026-02-01 09:15:00', '$2y$12$XYewY6BevthY2OoR3b92FeBE3O6YKpuOhOoAUdbSZ4ak2cwYPT5hK', 'Cashier', 'avatars/cashier.santos.jpg', '2026-02-01 09:15:00', '2026-02-01 09:15:00'),
(5, 'inv.navarro', 'Leo Navarro', 'inv.navarro@brightmarket.test', '2026-02-10 10:00:00', '$2y$12$XYewY6BevthY2OoR3b92FeBE3O6YKpuOhOoAUdbSZ4ak2cwYPT5hK', 'Inventory Clerk', 'avatars/inv.navarro.jpg', '2026-02-10 10:00:00', '2026-02-10 10:00:00');

INSERT INTO `customers` (`id`, `full_name`, `email`, `phone`, `created_at`, `updated_at`) VALUES
(1, 'Teresa Mendoza', 'teresa.mendoza@gmail.com', '+63 917 482 1930', '2026-03-01 08:30:00', '2026-03-01 08:30:00'),
(2, 'Rafael Villanueva', 'r.villanueva@outlook.ph', '+63 918 634 7205', '2026-03-02 09:15:00', '2026-03-02 09:15:00'),
(3, 'Clarissa Domingo', 'clarissa.domingo@yahoo.com.ph', '+63 927 815 0492', '2026-03-03 10:45:00', '2026-03-03 10:45:00'),
(4, 'Geronimo Pascual', 'gpascual.trade@gmail.com', '+63 908 391 6584', '2026-03-04 14:20:00', '2026-03-04 14:20:00'),
(5, 'Lorna Bautista', 'lorna.bautista@outlook.ph', '+63 919 740 2816', '2026-03-05 16:05:00', '2026-03-05 16:05:00');

INSERT INTO `products` (`id`, `name`, `price`, `stock_quantity`, `image`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Artisan Cold Brew Bottle', 4.50, 24, 'products/cold-brew.jpg', '2026-03-01 07:30:00', '2026-03-01 07:30:00', NULL),
(2, 'Sourdough Country Loaf', 6.75, 14, 'products/sourdough.jpg', '2026-03-01 07:35:00', '2026-03-01 07:35:00', NULL),
(3, 'Organic Whole Milk 1L', 3.80, 18, 'products/whole-milk.jpg', '2026-03-01 07:40:00', '2026-03-01 07:40:00', NULL),
(4, 'Roasted Arabica Beans 250g', 14.50, 9, 'products/arabica-beans.jpg', '2026-03-02 08:00:00', '2026-03-02 08:00:00', NULL),
(5, 'Raw Wildflower Honey 350g', 11.25, 3, 'products/wildflower-honey.jpg', '2026-03-02 08:10:00', '2026-03-02 08:10:00', NULL),
(6, 'Sea Salt Dark Chocolate Bar', 4.25, 22, 'products/dark-chocolate.jpg', '2026-03-03 08:20:00', '2026-03-03 08:20:00', NULL),
(7, 'Extra Virgin Olive Oil 500ml', 16.90, 5, 'products/olive-oil.jpg', '2026-03-03 08:30:00', '2026-03-03 08:30:00', NULL),
(8, 'Heirloom Tomato Crate 1kg', 5.40, 0, 'products/heirloom-tomatoes.jpg', '2026-03-04 08:45:00', '2026-03-04 08:45:00', NULL);

INSERT INTO `sales` (`id`, `product_id`, `customer_id`, `sold_by`, `quantity`, `total_price`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 2, 9.00, '2026-03-10 09:15:00', '2026-03-10 09:15:00'),
(2, 4, 2, 1, 1, 14.50, '2026-03-10 10:40:00', '2026-03-10 10:40:00'),
(3, 2, NULL, 4, 3, 20.25, '2026-03-11 11:05:00', '2026-03-11 11:05:00'),
(4, 6, 3, 3, 4, 17.00, '2026-03-12 14:25:00', '2026-03-12 14:25:00'),
(5, 7, 5, 1, 1, 16.90, '2026-03-13 16:50:00', '2026-03-13 16:50:00');

SET FOREIGN_KEY_CHECKS = 1;
