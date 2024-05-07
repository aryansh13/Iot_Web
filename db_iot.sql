-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Bulan Mei 2024 pada 01.50
-- Versi server: 10.4.24-MariaDB
-- Versi PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_iot`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `devices`
--

CREATE TABLE `devices` (
  `id` int(100) NOT NULL,
  `device_name` varchar(255) NOT NULL,
  `device_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `devices`
--

INSERT INTO `devices` (`id`, `device_name`, `device_description`) VALUES
(1, 'iot hidroponik', 'alat untuk mengukur hidroponik'),
(2, 'sensor DHT', 'alat untuk mengukur suhu');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` int(2) NOT NULL,
  `access` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `access`) VALUES
(1, 'admin', 'admin@gmail.com', 'admin123', 0, 0),
(2, 'ifka', 'ifka@gmail.com', '$2y$10$01aLIQGEjqigR1OCWzBRcu4Zp3f0ZFOuBG/QDY/3ZwVsYSub7qcZ6', 1, 1),
(3, 'arya', 'arya@gmail.com', 'arya1234', 1, 1),
(4, 'dani', 'dani@gmail.com', 'dani1234', 1, 1),
(5, 'daniar', 'daniar@gmail.com', '873184171438daf222acec9d3590b355', 1, 0),
(6, 'hanif', 'hanif@gmail.com', 'cdad01acf7d7bd74097c9414c9b926be', 1, 0),
(7, 'alif', 'alif@gmail.com', '$2y$10$IE8PQOjFANk9cC0/zRts6.XgBU5YyUY5nIO/E75vA8VaL3mtkYmyy', 1, 0),
(8, 'arya', 'arya1@gmail.com', '$2y$10$zXjua22b9C3oIRiiD7qnW.bu12ZCzGGkhouTCNY5UggqGaGblzg0m', 1, 0),
(9, 'jodo', 'jodo@gmail.com', 'a545e725f6064d37b5b31823ea42c884', 1, 0),
(10, 'dono', 'dono@gmail.com', '', 1, 0),
(14, 'aku', 'aku@gmail.com', 'aku123', 1, 0),
(15, 'aku', 'aku@gmail.com', 'aku123', 1, 0),
(16, 'akuadalah', 'akujuga@gmail.com', 'akujuga', 1, 0),
(17, 'darjo', 'darjo12@gmail.com', 'darjo123', 1, 0),
(18, 'rizki', 'rizki12@gmail.com', 'rizki1234', 1, 1),
(20, 'firza', 'firza12@gmail.com', 'firza123', 1, 1),
(21, '', '', '', 0, 0),
(22, 'aryanto', 'aryanto@gmail.com', 'aryanto1234', 0, 0),
(23, 'aryantoAndri', 'aryanto12@gmail.com', 'aryanto4312', 1, 1),
(24, 'rizal', 'rizal@gmail.com', 'rizal1234', 1, 1),
(25, 'donoWarkop', 'donowarkop@gmail.com', 'dono4321', 1, 0),
(26, 'Dono Warkop', 'donowarkop@gmail.com', 'dono4321', 1, 1),
(27, 'Jauhari', 'jahjah@gmail.com', 'jauhari1234', 1, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_devices`
--

CREATE TABLE `user_devices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `deviceName` varchar(255) NOT NULL,
  `device_requirements1` text NOT NULL,
  `device_requirements2` text DEFAULT NULL,
  `device_requirements3` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user_devices`
--

INSERT INTO `user_devices` (`id`, `user_id`, `deviceName`, `device_requirements1`, `device_requirements2`, `device_requirements3`) VALUES
(1, 2, 'Iot Hidroponik', 'suhu, kelembapan, status', '', ''),
(2, 4, 'sensor DHT', 'suhu', '', ''),
(3, 5, 'sennsor biasa', 'suhu', '', ''),
(4, 6, 'sesnor sensor', 'suhu, kelembapan', '', ''),
(5, 7, 'sensor212', 'suhu suhu', '', ''),
(6, 8, 'sesnor juha', 'suhu suhu suhu', '', ''),
(7, 9, 'arduino uno', 'suhu suhusuhu', '', ''),
(8, 10, 'esp32', 'alat alat', '', ''),
(12, 17, 'iot hidroPONIK', 'suhu', 'kelembapan', 'normal'),
(13, 18, 'iot sensor', 'jarak', 'gerak', NULL),
(14, 20, 'iot hidroponik', 'jembut', 'jancuk', NULL),
(15, 21, '', '', '', ''),
(16, 22, 'iya itu', 'asjdasd', 'lklksksk', 'klalskslk'),
(17, 23, 'iya iya', 'kksksks', 'lask', 'ncjsuuas'),
(18, 24, 'barang murah', 'panas', 'dingin', 'hangat'),
(19, 25, 'alat masak', 'panas', 'hangat', ''),
(20, 26, 'alat masak', 'panas', 'hangat', ''),
(21, 27, 'alat perang', 'bazoka', 'm416', '');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `user_devices`
--
ALTER TABLE `user_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `user_devices`
--
ALTER TABLE `user_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `user_devices`
--
ALTER TABLE `user_devices`
  ADD CONSTRAINT `user_devices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
