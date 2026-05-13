-- https://www.phpmyadmin.net/
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `absensi` (
  `id_absen` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jenis_absen` enum('Pagi','Siang','Sore') DEFAULT NULL,
  `jam_masuk` time DEFAULT NULL,
  `keterangan` enum('Hadir','Izin','Sakit') DEFAULT 'Hadir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `absensi` (`id_absen`, `username`, `tanggal`, `jenis_absen`, `jam_masuk`, `keterangan`) VALUES
(1, 'Anas', '2026-05-13', 'Pagi', '09:15:00', 'Hadir'),
(2, 'Nisa', '2026-05-13', 'Pagi', '09:20:00', 'Hadir'),
(3, 'Anas', '2026-05-13', 'Siang', '12:05:00', 'Hadir');


CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','HR','Karyawan','Pendiri') NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `divisi` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama_lengkap`, `divisi`) VALUES
(1, 'jaloeeyyy', 'jaloe0085', 'Admin', 'Administrator Utama', 'Dev'),
(2, 'bagas', 'bos123', 'Pendiri', 'Bagas Nasution', '-'),
(3, 'Nisa', 'user123', 'HR', 'Nisa Lubis', 'HRD'),
(4, 'Anas', 'cs123', 'Karyawan', 'Anas', 'CS');

ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absen`),
  ADD KEY `fk_user_absen` (`username`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `absensi`
  MODIFY `id_absen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_user_absen` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
