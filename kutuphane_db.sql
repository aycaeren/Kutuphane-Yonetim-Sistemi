-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:8889
-- Üretim Zamanı: 18 Ara 2025, 11:08:44
-- Sunucu sürümü: 8.0.40
-- PHP Sürümü: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `kutuphane_db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `books`
--

CREATE TABLE `books` (
  `id` int NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `year` int DEFAULT NULL,
  `summary` text,
  `stock` int DEFAULT '0',
  `shelf` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `category`, `year`, `summary`, `stock`, `shelf`) VALUES
(1, 'Suç ve Ceza', 'Fyodor Dostoyevski', 'Roman', 1866, 'Bir suçun psikolojik etkileri.', 4, 'A1'),
(2, 'Kürk Mantolu Madonna', 'Sabahattin Ali', 'Roman', 1943, 'Aşk ve yalnızlık üzerine bir roman.', 1, 'A2'),
(3, '1984', 'George Orwell', 'Distopya', 1949, 'Totaliter rejim eleştirisi.', 4, 'B1'),
(4, 'Hayvan Çiftliği', 'George Orwell', 'Siyasi Alegori', 1945, 'Toplum eleştirisi.', 5, 'B2'),
(5, 'a', 'b', 'Felsefi Roman', 1988, 'Kişisel yolculuk hikayesi.', 2, 'C1'),
(6, 'c', 's', NULL, 1234, NULL, 5, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `borrow_requests`
--

CREATE TABLE `borrow_requests` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `book_id` int DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `request_date` date DEFAULT NULL,
  `return_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `borrow_requests`
--

INSERT INTO `borrow_requests` (`id`, `user_id`, `book_id`, `status`, `request_date`, `return_date`) VALUES
(3, 10, 1, 'İade Edildi', '2025-12-17', NULL),
(4, 10, 3, 'İade Edildi', '2025-12-18', NULL),
(5, 10, 2, 'İade Edildi', '2025-12-18', NULL),
(6, 10, 5, 'İade Edildi', '2025-12-18', '2026-01-02 05:05:39'),
(7, 10, 1, 'İade Edildi', '2025-12-18', '2026-01-02 05:05:48'),
(8, 10, 4, 'İade Edildi', '2025-12-18', '2026-01-02 05:09:17'),
(9, 10, 1, 'İade Edildi', '2025-12-18', '2026-01-02 05:20:47'),
(10, 10, 2, 'Reddedildi', '2025-12-18', NULL),
(11, 10, 3, 'Reddedildi', '2025-12-18', NULL),
(12, 10, 1, 'İade Edildi', '2025-12-18', '2026-01-02 05:32:50'),
(13, 10, 2, 'Onaylandı', '2025-12-18', '2026-01-02 05:33:43'),
(14, 10, 1, 'İade Edildi', '2025-12-18', '2026-01-02 13:10:20');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `school_no` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('student','staff','admin') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `school_no`, `phone`, `role`) VALUES
(3, 'Admin', 'Admin@gmail.com', '$2y$10$7mh03HutSmhgVLSlfpwi9OHf0cfX7gz2.JjbS2LlwhpopsMsaHqgK', '1230505058', '0505 555 5555', 'admin'),
(4, 'Ayça Eren', 'ayca@gmail.com', '$2y$10$flNaTYEZQJbXSkHEmM7Tm.i0aiYnGYitZoDvj7lwPfwPFURgrYD.2', '1234', '0505 555 5555', 'staff'),
(10, 'Irmak Kızıl', 'jdsj@mail.com', '$2y$10$2pJ14sT0jKbNGBDi9kQs3efqgSuCwpWjeUNm20fHPLc..PD01d18q', '123', '00000000000', 'student'),
(11, 'kdmmkd', 'dcd@m.com', '$2y$10$YdhSGKzacpsShVgoupCLN./ArZmPDfbMzH7H/DsLP7Vy9axsisAE.', '1232', '00000000000', 'student'),
(13, 'Irmak Kızıl', 'djd@m.com', '$2y$10$u65vj12D8SWzI1IqjgOwm.HhG4vDS/8fJyLLZJEYZF9ibNv0VYIQu', '12345', '34331111111', 'student');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `books`
--
ALTER TABLE `books`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `borrow_requests`
--
ALTER TABLE `borrow_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD CONSTRAINT `borrow_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `borrow_requests_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
