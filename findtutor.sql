-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2025 at 06:31 PM
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
-- Database: `findtutor`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` int(5) NOT NULL,
  `Email` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `Email`) VALUES
(11111, 'admin1@gmail.com'),
(22222, 'admin2@gmail.com'),
(33333, 'admin3@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `apply`
--

CREATE TABLE `apply` (
  `TuitionID` int(10) NOT NULL,
  `TID` int(7) NOT NULL,
  `AppliedStatus` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apply`
--

INSERT INTO `apply` (`TuitionID`, `TID`, `AppliedStatus`) VALUES
(1407689086, 2222002, NULL),
(1407689087, 2222001, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `Certificates` varchar(60) NOT NULL,
  `NID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`Certificates`, `NID`) VALUES
('A-Levels (EdExcel)', 1234567890),
('HSC (BANGLA VERSION)', 1234567891),
('HSC (BANGLA VERSION)', 1234567892),
('O-Levels (EdExcel)', 1234567890);

-- --------------------------------------------------------

--
-- Table structure for table `credentials`
--

CREATE TABLE `credentials` (
  `NID` int(10) NOT NULL,
  `PassportNumber` varchar(15) DEFAULT NULL,
  `AdminID` int(5) DEFAULT NULL,
  `TID` int(7) DEFAULT NULL,
  `VerifiedStatus` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`NID`, `PassportNumber`, `AdminID`, `TID`, `VerifiedStatus`) VALUES
(1234567890, '1a234567b90', 11111, 2222001, NULL),
(1234567891, '1a234567b91', 22222, 2222002, NULL),
(1234567892, '1a234567b92', 33333, 2222003, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `guardian`
--

CREATE TABLE `guardian` (
  `GID` int(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guardian`
--

INSERT INTO `guardian` (`GID`) VALUES
(1111001),
(1111002),
(1111003);

-- --------------------------------------------------------

--
-- Table structure for table `recognition`
--

CREATE TABLE `recognition` (
  `Title` varchar(100) NOT NULL,
  `TID` int(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recognition`
--

INSERT INTO `recognition` (`Title`, `TID`) VALUES
('Tutor of the month for August 2025', 2222002),
('Tutor of the month for July 2025', 2222001);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `SerialNo` int(15) NOT NULL,
  `Description` varchar(200) DEFAULT NULL,
  `Rating` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`SerialNo`, `Description`, `Rating`) VALUES
(100902743, 'He was very cooperative and sincere in his teaching. Always made my son understand his studies.', 5),
(100902744, 'As a guardian, she was very cooperative.', 5);

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `TID` int(7) NOT NULL,
  `PreferredSubject` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`TID`, `PreferredSubject`) VALUES
(2222001, 'Science'),
(2222002, 'Business'),
(2222003, 'Science'),
(2222004, 'Humanities');

-- --------------------------------------------------------

--
-- Table structure for table `tuitionjob`
--

CREATE TABLE `tuitionjob` (
  `TuitionID` int(10) NOT NULL,
  `Salary` int(6) DEFAULT NULL,
  `Timing` varchar(30) DEFAULT NULL,
  `Class` varchar(30) DEFAULT NULL,
  `Days` varchar(30) DEFAULT NULL,
  `Subject` varchar(30) DEFAULT NULL,
  `Preferred_teacher_gender` varchar(6) DEFAULT NULL,
  `GID` int(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tuitionjob`
--

INSERT INTO `tuitionjob` (`TuitionID`, `Salary`, `Timing`, `Class`, `Days`, `Subject`, `Preferred_teacher_gender`, `GID`) VALUES
(1407689085, 3000, 'Morning 8 AM-9 AM', 'Class-7', '2 days', 'English, Bangla', 'Any', 1111003),
(1407689086, 7000, 'Afternoon 3PM-5PM', 'Class-3', '5 days', 'All Subjects', 'Male', 1111002),
(1407689087, 6000, 'Evening 6PM-8PM', 'Class-10', '3 Days', 'Physics, Chemistry, Biology', 'Female', 1111001);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID` int(7) NOT NULL,
  `Name` varchar(30) DEFAULT NULL,
  `Email` varchar(40) DEFAULT NULL,
  `Gender` varchar(6) DEFAULT NULL,
  `Location` varchar(50) DEFAULT NULL,
  `Password` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID`, `Name`, `Email`, `Gender`, `Location`, `Password`) VALUES
(1111001, 'Jasim Uddin', 'jasim@gmail.com', 'Male', 'Banani', 'abccdefgh1'),
(1111002, 'Jesmin Islam', 'jesmin@gmail.com', 'Female', 'Kallyanpur', 'abccdefgh2'),
(1111003, 'Nurul Ahmed', 'nurul@gmail.com', 'Male', 'Gulshan', 'abccdefgh3'),
(2222001, 'Samia Rahman', 'samia@gmail.com', 'Female', 'Bashundhara', 'abccdefgh4'),
(2222002, 'Shamit Islam', 'shamit@gmail.com', 'Male ', 'Rajabazar', 'abccdefgh5'),
(2222003, 'Fahim Hasan ', 'fahim.hasan@gmail.com', 'Male ', 'Niketon', 'abccdefgh6'),
(2222004, 'Asif Azad', 'asif.azad@gmail.com', 'Male ', 'Mohammadpur', 'abccdefgh7');

-- --------------------------------------------------------

--
-- Table structure for table `userreview`
--

CREATE TABLE `userreview` (
  `SerialNo` int(15) NOT NULL,
  `ID` int(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userreview`
--

INSERT INTO `userreview` (`SerialNo`, `ID`) VALUES
(100902743, 2222001),
(100902744, 1111001);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`);

--
-- Indexes for table `apply`
--
ALTER TABLE `apply`
  ADD PRIMARY KEY (`TuitionID`,`TID`),
  ADD KEY `TID` (`TID`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`Certificates`,`NID`);

--
-- Indexes for table `credentials`
--
ALTER TABLE `credentials`
  ADD PRIMARY KEY (`NID`),
  ADD KEY `AdminID` (`AdminID`),
  ADD KEY `TID` (`TID`);

--
-- Indexes for table `guardian`
--
ALTER TABLE `guardian`
  ADD PRIMARY KEY (`GID`);

--
-- Indexes for table `recognition`
--
ALTER TABLE `recognition`
  ADD PRIMARY KEY (`Title`,`TID`),
  ADD KEY `TID` (`TID`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`SerialNo`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`TID`);

--
-- Indexes for table `tuitionjob`
--
ALTER TABLE `tuitionjob`
  ADD PRIMARY KEY (`TuitionID`),
  ADD KEY `GID` (`GID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `userreview`
--
ALTER TABLE `userreview`
  ADD PRIMARY KEY (`SerialNo`,`ID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `apply`
--
ALTER TABLE `apply`
  ADD CONSTRAINT `apply_ibfk_1` FOREIGN KEY (`TuitionID`) REFERENCES `tuitionjob` (`TuitionID`),
  ADD CONSTRAINT `apply_ibfk_2` FOREIGN KEY (`TID`) REFERENCES `teacher` (`TID`);

--
-- Constraints for table `credentials`
--
ALTER TABLE `credentials`
  ADD CONSTRAINT `credentials_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `admin` (`AdminID`),
  ADD CONSTRAINT `credentials_ibfk_2` FOREIGN KEY (`TID`) REFERENCES `teacher` (`TID`);

--
-- Constraints for table `guardian`
--
ALTER TABLE `guardian`
  ADD CONSTRAINT `guardian_ibfk_1` FOREIGN KEY (`GID`) REFERENCES `user` (`ID`);

--
-- Constraints for table `recognition`
--
ALTER TABLE `recognition`
  ADD CONSTRAINT `recognition_ibfk_1` FOREIGN KEY (`TID`) REFERENCES `teacher` (`TID`);

--
-- Constraints for table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`TID`) REFERENCES `user` (`ID`);

--
-- Constraints for table `tuitionjob`
--
ALTER TABLE `tuitionjob`
  ADD CONSTRAINT `tuitionjob_ibfk_1` FOREIGN KEY (`GID`) REFERENCES `guardian` (`GID`);

--
-- Constraints for table `userreview`
--
ALTER TABLE `userreview`
  ADD CONSTRAINT `userreview_ibfk_1` FOREIGN KEY (`SerialNo`) REFERENCES `review` (`SerialNo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
