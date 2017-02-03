-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2017 at 12:58 PM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 7.0.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kondoris`
--

-- --------------------------------------------------------

--
-- Table structure for table `acl_privileges`
--

CREATE TABLE `acl_privileges` (
  `name` varchar(50) COLLATE cp1250_czech_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1250 COLLATE=cp1250_czech_cs;

--
-- Dumping data for table `acl_privileges`
--

INSERT INTO `acl_privileges` (`name`) VALUES
('coment'),
('create'),
('hlas'),
('read');

-- --------------------------------------------------------

--
-- Table structure for table `acl_resources`
--

CREATE TABLE `acl_resources` (
  `name` varchar(50) COLLATE cp1250_czech_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1250 COLLATE=cp1250_czech_cs;

--
-- Dumping data for table `acl_resources`
--

INSERT INTO `acl_resources` (`name`) VALUES
('bodOddilovky'),
('oddilovka');

-- --------------------------------------------------------

--
-- Table structure for table `acl_roles`
--

CREATE TABLE `acl_roles` (
  `name` varchar(50) COLLATE cp1250_czech_cs NOT NULL,
  `parent_name` varchar(50) COLLATE cp1250_czech_cs DEFAULT NULL,
  `text` varchar(50) COLLATE cp1250_czech_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1250 COLLATE=cp1250_czech_cs;

--
-- Dumping data for table `acl_roles`
--

INSERT INTO `acl_roles` (`name`, `parent_name`, `text`) VALUES
('admin', NULL, 'Administrátor'),
('clen', NULL, 'člen'),
('guest', NULL, 'host'),
('vudceOddilu', NULL, 'Vůdce oddílu');

-- --------------------------------------------------------

--
-- Table structure for table `acl_table`
--

CREATE TABLE `acl_table` (
  `id` int(11) NOT NULL,
  `role` varchar(50) COLLATE cp1250_czech_cs DEFAULT NULL,
  `resource` varchar(50) COLLATE cp1250_czech_cs DEFAULT NULL,
  `privilage` varchar(50) COLLATE cp1250_czech_cs DEFAULT NULL,
  `allow` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=cp1250 COLLATE=cp1250_czech_cs;

--
-- Dumping data for table `acl_table`
--

INSERT INTO `acl_table` (`id`, `role`, `resource`, `privilage`, `allow`) VALUES
(1, 'admin', NULL, NULL, 1),
(2, 'vudceOddilu', 'bodOddilovky', NULL, 1),
(3, 'vudceOddilu', 'oddilovka', NULL, 1),
(4, 'clen', 'oddilovka', 'read', 1),
(5, 'clen', 'bodOddilovky', 'read', 1),
(6, 'clen', 'bodOddilovky', 'coment', 1),
(7, 'vudceOddilu', 'bodOddilovky', 'create', 1),
(8, 'vudceOddilu', 'oddilovka', 'create', 1);

-- --------------------------------------------------------

--
-- Table structure for table `bod_oddilovky`
--

CREATE TABLE `bod_oddilovky` (
  `id` int(11) NOT NULL,
  `oddilovka_id` int(11) NOT NULL DEFAULT '0',
  `autor` int(11) NOT NULL DEFAULT '0',
  `vytvoreno` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(50) COLLATE cp1250_czech_cs NOT NULL DEFAULT '',
  `popis` varchar(100) COLLATE cp1250_czech_cs DEFAULT NULL,
  `text` text COLLATE cp1250_czech_cs NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1250 COLLATE=cp1250_czech_cs;


-- --------------------------------------------------------

--
-- Table structure for table `hlas`
--

CREATE TABLE `hlas` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE cp1250_czech_cs NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  `color` varchar(50) COLLATE cp1250_czech_cs DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=cp1250 COLLATE=cp1250_czech_cs;

-- --------------------------------------------------------

--
-- Table structure for table `komentare_diskuze`
--

CREATE TABLE `komentare_diskuze` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text COLLATE cp1250_czech_cs NOT NULL,
  `bodOddilovky_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1250 COLLATE=cp1250_czech_cs;

-- --------------------------------------------------------

--
-- Table structure for table `komentare_hlasy`
--

CREATE TABLE `komentare_hlasy` (
  `id` int(11) NOT NULL,
  `hlas_id` int(11) NOT NULL,
  `create` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text COLLATE cp1250_czech_cs NOT NULL,
  `bod_oddilovky_id` int(11) NOT NULL,
  `update` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1250 COLLATE=cp1250_czech_cs;

-- --------------------------------------------------------

--
-- Table structure for table `oddilovky`
--

CREATE TABLE `oddilovky` (
  `id` int(11) NOT NULL,
  `aktivni_od` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aktivni_do` datetime NOT NULL,
  `popis_short` varchar(75) COLLATE cp1250_czech_cs DEFAULT NULL,
  `popis_long` text COLLATE cp1250_czech_cs,
  `autor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1250 COLLATE=cp1250_czech_cs;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) COLLATE cp1250_czech_cs NOT NULL,
  `password` varchar(60) COLLATE cp1250_czech_cs NOT NULL,
  `email` varchar(50) COLLATE cp1250_czech_cs NOT NULL,
  `name` varchar(50) COLLATE cp1250_czech_cs NOT NULL,
  `role` varchar(50) COLLATE cp1250_czech_cs NOT NULL DEFAULT 'guest',
  `hlas` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1250 COLLATE=cp1250_czech_cs;

-- --------------------------------------------------------

--
-- Table structure for table `user_hlas`
--

CREATE TABLE `user_hlas` (
  `id` int(11) NOT NULL,
  `hlas_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=cp1250 COLLATE=cp1250_czech_cs;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `acl_privileges`
--
ALTER TABLE `acl_privileges`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `acl_resources`
--
ALTER TABLE `acl_resources`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `acl_roles`
--
ALTER TABLE `acl_roles`
  ADD PRIMARY KEY (`name`),
  ADD KEY `parent_name` (`parent_name`);

--
-- Indexes for table `acl_table`
--
ALTER TABLE `acl_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role` (`role`),
  ADD KEY `resource` (`resource`),
  ADD KEY `privilage` (`privilage`);

--
-- Indexes for table `bod_oddilovky`
--
ALTER TABLE `bod_oddilovky`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autor` (`autor`),
  ADD KEY `oddilovka_id` (`oddilovka_id`);

--
-- Indexes for table `hlas`
--
ALTER TABLE `hlas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `komentare_diskuze`
--
ALTER TABLE `komentare_diskuze`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `bodOddilovky_id` (`bodOddilovky_id`);

--
-- Indexes for table `komentare_hlasy`
--
ALTER TABLE `komentare_hlasy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hlas_id` (`hlas_id`),
  ADD KEY `bod_oddilovky_id` (`bod_oddilovky_id`),
  ADD KEY `update` (`update`);

--
-- Indexes for table `oddilovky`
--
ALTER TABLE `oddilovky`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autor` (`autor`),
  ADD KEY `autor_2` (`autor`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `role` (`role`),
  ADD KEY `hlas` (`hlas`);

--
-- Indexes for table `user_hlas`
--
ALTER TABLE `user_hlas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hlas_id` (`hlas_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acl_table`
--
ALTER TABLE `acl_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `bod_oddilovky`
--
ALTER TABLE `bod_oddilovky`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `hlas`
--
ALTER TABLE `hlas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `komentare_diskuze`
--
ALTER TABLE `komentare_diskuze`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `komentare_hlasy`
--
ALTER TABLE `komentare_hlasy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `oddilovky`
--
ALTER TABLE `oddilovky`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `user_hlas`
--
ALTER TABLE `user_hlas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `acl_roles`
--
ALTER TABLE `acl_roles`
  ADD CONSTRAINT `acl_roles_ibfk_1` FOREIGN KEY (`parent_name`) REFERENCES `acl_roles` (`name`);

--
-- Constraints for table `acl_table`
--
ALTER TABLE `acl_table`
  ADD CONSTRAINT `acl_table_ibfk_1` FOREIGN KEY (`role`) REFERENCES `acl_roles` (`name`),
  ADD CONSTRAINT `acl_table_ibfk_2` FOREIGN KEY (`resource`) REFERENCES `acl_resources` (`name`),
  ADD CONSTRAINT `acl_table_ibfk_3` FOREIGN KEY (`privilage`) REFERENCES `acl_privileges` (`name`);

--
-- Constraints for table `bod_oddilovky`
--
ALTER TABLE `bod_oddilovky`
  ADD CONSTRAINT `bod_oddilovky_ibfk_1` FOREIGN KEY (`autor`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bod_oddilovky_ibfk_2` FOREIGN KEY (`oddilovka_id`) REFERENCES `oddilovky` (`id`);

--
-- Constraints for table `komentare_diskuze`
--
ALTER TABLE `komentare_diskuze`
  ADD CONSTRAINT `komentare_diskuze_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `komentare_diskuze_ibfk_3` FOREIGN KEY (`bodOddilovky_id`) REFERENCES `bod_oddilovky` (`id`);

--
-- Constraints for table `komentare_hlasy`
--
ALTER TABLE `komentare_hlasy`
  ADD CONSTRAINT `komentare_hlasy_ibfk_1` FOREIGN KEY (`hlas_id`) REFERENCES `hlas` (`id`),
  ADD CONSTRAINT `komentare_hlasy_ibfk_2` FOREIGN KEY (`bod_oddilovky_id`) REFERENCES `bod_oddilovky` (`id`);

--
-- Constraints for table `oddilovky`
--
ALTER TABLE `oddilovky`
  ADD CONSTRAINT `oddilovky_ibfk_1` FOREIGN KEY (`autor`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role`) REFERENCES `acl_roles` (`name`);

--
-- Constraints for table `user_hlas`
--
ALTER TABLE `user_hlas`
  ADD CONSTRAINT `user_hlas_ibfk_1` FOREIGN KEY (`hlas_id`) REFERENCES `hlas` (`id`),
  ADD CONSTRAINT `user_hlas_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
