-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2017 at 12:58 AM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `theschool`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrators`
--

CREATE TABLE `administrators` (
  `Administrator_ID` int(16) NOT NULL,
  `Email` varchar(64) COLLATE utf8_bin NOT NULL,
  `Name` varchar(32) COLLATE utf8_bin NOT NULL,
  `Role` varchar(12) COLLATE utf8_bin NOT NULL,
  `Phone` varchar(16) COLLATE utf8_bin NOT NULL,
  `Password` mediumtext COLLATE utf8_bin NOT NULL,
  `Image` varchar(32) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `administrators`
--

INSERT INTO `administrators` (`Administrator_ID`, `Email`, `Name`, `Role`, `Phone`, `Password`, `Image`) VALUES
(16, 'joe@mail.com', 'Joe Doe', 'owner', '0546000001', '$2y$12$ArP9SWz2ccjvI00j4s91Zu5vJHH9JvRDOu5yuKqrPBzDes78rZxmG', '/upload/91eb47d4694cb047.jpg'),
(17, 'john@mail.com', 'John Black', 'manager', '0546000002', '$2y$12$ArP9SWz2ccjvI00j4s91Zu5vJHH9JvRDOu5yuKqrPBzDes78rZxmG', '/upload/91eb47d4694cb048.jpg'),
(18, 'smith@mail.com', 'Mr Smith', 'sales', '0542999999', '$2y$12$ArP9SWz2ccjvI00j4s91Zu5vJHH9JvRDOu5yuKqrPBzDes78rZxmG', '/upload/721cc3d567bbc1f6.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `Course_ID` int(16) NOT NULL,
  `Name` varchar(32) COLLATE utf8_bin NOT NULL,
  `Description` varchar(500) COLLATE utf8_bin NOT NULL,
  `Image` varchar(32) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`Course_ID`, `Name`, `Description`, `Image`) VALUES
(19, 'Course A', 'Course A', '/upload/2bf7bc9bcd04e4a5.png'),
(20, 'Course B', 'Course B', '/upload/36c53dbafbb0d7f7.png'),
(21, 'Course C', 'Course C', '/upload/37f668bb3b379f27.png'),
(22, 'Course D', 'Course D', '/upload/75458c37547059ce.png');

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

CREATE TABLE `school` (
  `ID` int(16) NOT NULL,
  `Student_ID` int(16) NOT NULL,
  `Course_ID` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `school`
--

INSERT INTO `school` (`ID`, `Student_ID`, `Course_ID`) VALUES
(21, 28, 19),
(22, 28, 20),
(23, 28, 21),
(24, 28, 22);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `Student_ID` int(16) NOT NULL,
  `Email` varchar(64) COLLATE utf8_bin NOT NULL,
  `Name` varchar(32) COLLATE utf8_bin NOT NULL,
  `Phone` varchar(16) COLLATE utf8_bin NOT NULL,
  `Image` varchar(32) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`Student_ID`, `Email`, `Name`, `Phone`, `Image`) VALUES
(28, 'yossi@mail.com', 'Yossi Cohen', '054-9999999', '/upload/3f6d762afeb9f79d.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`Administrator_ID`),
  ADD UNIQUE KEY `email` (`Email`) USING BTREE;

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`Course_ID`);

--
-- Indexes for table `school`
--
ALTER TABLE `school`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Student_ID` (`Student_ID`),
  ADD KEY `Course_ID` (`Course_ID`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`Student_ID`),
  ADD UNIQUE KEY `Email` (`Email`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrators`
--
ALTER TABLE `administrators`
  MODIFY `Administrator_ID` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `Course_ID` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `school`
--
ALTER TABLE `school`
  MODIFY `ID` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `Student_ID` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `school`
--
ALTER TABLE `school`
  ADD CONSTRAINT `school_ibfk_1` FOREIGN KEY (`Student_ID`) REFERENCES `students` (`Student_ID`),
  ADD CONSTRAINT `school_ibfk_2` FOREIGN KEY (`Course_ID`) REFERENCES `courses` (`Course_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
