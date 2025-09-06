-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2025 at 08:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `Id` int(11) NOT NULL,
  `Name` varchar(222) NOT NULL,
  `Capacity` int(11) NOT NULL,
  `Code` varchar(222) NOT NULL,
  `semester_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`Id`, `Name`, `Capacity`, `Code`, `semester_id`) VALUES
(1, 'Mathematics 1', 140, 'MTH 011', 2),
(2, 'Intro to BioInformatics', 50, 'CSE 224', 2);

-- --------------------------------------------------------

--
-- Table structure for table `regdata`
--

CREATE TABLE `regdata` (
  `Id` int(11) NOT NULL,
  `RegId` int(11) NOT NULL,
  `NumberOfCourses` int(11) NOT NULL,
  `RegisteredAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `course_id` int(11) NOT NULL,
  `grade` varchar(222) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `regdata`
--

INSERT INTO `regdata` (`Id`, `RegId`, `NumberOfCourses`, `RegisteredAt`, `course_id`, `grade`) VALUES
(7, 10, 1, '2025-09-06 16:54:28', 0, ''),
(8, 11, 1, '2025-09-06 17:28:30', 0, ''),
(9, 12, 1, '2025-09-06 17:39:33', 0, ''),
(10, 13, 1, '2025-09-06 17:39:33', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`id`, `semester_id`, `student_id`) VALUES
(10, 2, 2),
(11, 2, 2),
(12, 2, 1),
(13, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `id` int(11) NOT NULL,
  `Season` varchar(222) NOT NULL,
  `Year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`id`, `Season`, `Year`) VALUES
(1, 'Fall', '2024'),
(2, 'Spring', '2025'),
(3, 'Fall', '2025'),
(4, 'Spring', '2026');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `Id` int(11) NOT NULL,
  `FirstName` varchar(244) NOT NULL,
  `LastName` varchar(244) NOT NULL,
  `Email` varchar(222) NOT NULL,
  `NationalId` varchar(17) NOT NULL,
  `Age` int(3) NOT NULL,
  `PhonNumber` bigint(11) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`Id`, `FirstName`, `LastName`, `Email`, `NationalId`, `Age`, `PhonNumber`, `password_hash`, `created_at`) VALUES
(1, 'Hamada', 'Ezzo', 'hamadaezzo@school.edu', '3061123559147', 14, 1234513992, '$2y$10$y/PwsRFKEcW6j.smlVV8JueVUjg.n.mXb3ZV05vsyhyQ0Rz9d6AqW', '2025-09-06 12:14:32'),
(2, 'Sarah', 'Ahmed', 'sara@email.com', '30649721945777', 17, 6645157946, '$2y$10$Xv.PdYkQ9rVQxbSLXcb0CejDh0TLLecbt..fv0/qwdEPPmVlC9/bu', '2025-09-06 16:47:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `regdata`
--
ALTER TABLE `regdata`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `RegId` (`RegId`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `nid` (`NationalId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `regdata`
--
ALTER TABLE `regdata`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `regdata`
--
ALTER TABLE `regdata`
  ADD CONSTRAINT `regdata_ibfk_1` FOREIGN KEY (`RegId`) REFERENCES `registration` (`id`);

--
-- Constraints for table `registration`
--
ALTER TABLE `registration`
  ADD CONSTRAINT `registration_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
