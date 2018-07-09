-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2018 at 04:23 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `monitoring_inspeksi`
--

-- --------------------------------------------------------

--
-- Table structure for table `form_teknisi`
--

CREATE TABLE `form_teknisi` (
  `no_form` varchar(30) NOT NULL,
  `jenis` varchar(30) NOT NULL,
  `no_tangki` varchar(30) NOT NULL,
  `tanggal` date NOT NULL,
  `nip` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gambar_teknisi`
--

CREATE TABLE `gambar_teknisi` (
  `kd_gmbar` varchar(30) NOT NULL,
  `gambar` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hasil_form_teknisi`
--

CREATE TABLE `hasil_form_teknisi` (
  `no_pertanyaan` varchar(30) NOT NULL,
  `no_form` varchar(30) NOT NULL,
  `kd_gambar` varchar(30) DEFAULT NULL,
  `jawaban` text NOT NULL,
  `kondisi` text NOT NULL,
  `keterangan` text NOT NULL,
  `rekomendasi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tangki`
--

CREATE TABLE `tangki` (
  `no_tangki` varchar(30) NOT NULL,
  `uk_tangki` varchar(30) NOT NULL,
  `jenis_tangki` varchar(11) NOT NULL,
  `tahun_tangki` int(11) NOT NULL,
  `lok_tangki` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tangki`
--

INSERT INTO `tangki` (`no_tangki`, `uk_tangki`, `jenis_tangki`, `tahun_tangki`, `lok_tangki`) VALUES
('BP.01', 'd: 5000 mm, t: 8390 mm', 'Evaporator', 2013, '-'),
('BP.02', 'd: 4100 mm, t: 8640 mm', 'Evaporator', 2013, '-'),
('BP.03', 'd: 3300 mm, t: 5750 mm', 'Evaporator', 0, '-'),
('BP.04', 'd: 3300 mm, t: 5300 mm', 'Evaporator', 0, '-'),
('BP.05', 'd: 3600 mm, t: 5100 mm', 'Evaporator', 0, '-'),
('BP.06', 'd: 3300 mm, t: 5200 mm', 'Evaporator', 0, '-'),
('BP.07', 'd: 3300 mm, t: 5200 mm', 'Evaporator', 0, '-');

-- --------------------------------------------------------

--
-- Table structure for table `tekanan`
--

CREATE TABLE `tekanan` (
  `id_tekanan` int(11) NOT NULL,
  `tekanan` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tekanan`
--

INSERT INTO `tekanan` (`id_tekanan`, `tekanan`) VALUES
(1, 1),
(2, 3),
(3, 8),
(4, 2),
(5, 10),
(6, 4),
(7, 12),
(8, 10),
(9, 4),
(10, 12),
(11, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `nip` varchar(30) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `divisi` varchar(30) NOT NULL,
  `jabatan` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`nip`, `nama`, `password`, `divisi`, `jabatan`) VALUES
('1101', 'Habib', '1101', 'IT', 'Admin'),
('1102', 'Rifai', '1102', 'IT', 'Inspektor'),
('1103', 'Ahmad', '1103', 'IT', 'Monitoring');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `form_teknisi`
--
ALTER TABLE `form_teknisi`
  ADD PRIMARY KEY (`no_form`),
  ADD KEY `nip` (`nip`),
  ADD KEY `no_tangki` (`no_tangki`);

--
-- Indexes for table `gambar_teknisi`
--
ALTER TABLE `gambar_teknisi`
  ADD PRIMARY KEY (`kd_gmbar`);

--
-- Indexes for table `hasil_form_teknisi`
--
ALTER TABLE `hasil_form_teknisi`
  ADD KEY `no_form` (`no_form`),
  ADD KEY `kd_gambar` (`kd_gambar`),
  ADD KEY `no_pertanyaan` (`no_pertanyaan`);

--
-- Indexes for table `tangki`
--
ALTER TABLE `tangki`
  ADD PRIMARY KEY (`no_tangki`);

--
-- Indexes for table `tekanan`
--
ALTER TABLE `tekanan`
  ADD PRIMARY KEY (`id_tekanan`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`nip`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tekanan`
--
ALTER TABLE `tekanan`
  MODIFY `id_tekanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `form_teknisi`
--
ALTER TABLE `form_teknisi`
  ADD CONSTRAINT `form_teknisi_ibfk_2` FOREIGN KEY (`nip`) REFERENCES `user` (`nip`) ON UPDATE CASCADE,
  ADD CONSTRAINT `form_teknisi_ibfk_3` FOREIGN KEY (`no_tangki`) REFERENCES `tangki` (`no_tangki`) ON UPDATE CASCADE;

--
-- Constraints for table `hasil_form_teknisi`
--
ALTER TABLE `hasil_form_teknisi`
  ADD CONSTRAINT `hasil_form_teknisi_ibfk_3` FOREIGN KEY (`no_form`) REFERENCES `form_teknisi` (`no_form`) ON UPDATE CASCADE,
  ADD CONSTRAINT `hasil_form_teknisi_ibfk_4` FOREIGN KEY (`kd_gambar`) REFERENCES `gambar_teknisi` (`kd_gmbar`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
