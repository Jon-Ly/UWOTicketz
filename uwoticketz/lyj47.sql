-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2017 at 03:08 PM
-- Server version: 10.1.26-MariaDB
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lyj47`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetAllAccessLevels` ()  NO SQL
SELECT * FROM accesslevel$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetAllComputers` ()  NO SQL
SELECT * FROM Computer$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetAllLogs` ()  NO SQL
SELECT * FROM Log$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetAllTickets` ()  READS SQL DATA
SELECT
Ticket.Id, ComputerId, DateSubmitted, DateCompleted, StatusName, Rating
FROM Ticket 
INNER JOIN ticketstatus ON ticketstatus.Id = Ticket.Status$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetAllUsers` ()  NO SQL
SELECT * FROM User$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetComputerById` (IN `computerId` INT(11) UNSIGNED)  NO SQL
SELECT
	Id,
    LocationId
FROM
	computer
WHERE
	Id = computerId$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `InsertTicket` (IN `computerId` INT(11) UNSIGNED, IN `description` VARCHAR(500), IN `userId` INT(11) UNSIGNED)  MODIFIES SQL DATA
INSERT INTO
ticket(ComputerId, UserId, DateSubmitted, Description, Status)
VALUES(computerId, userId, CURRENT_TIMESTAMP, description, 1)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `accesslevel`
--

CREATE TABLE `accesslevel` (
  `Id` int(11) NOT NULL,
  `AccessLevel` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `accesslevel`
--

INSERT INTO `accesslevel` (`Id`, `AccessLevel`) VALUES
(0, 'User'),
(1, 'IT'),
(2, 'Auditor');

-- --------------------------------------------------------

--
-- Table structure for table `computer`
--

CREATE TABLE `computer` (
  `Id` int(11) NOT NULL,
  `LocationId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `computer`
--

INSERT INTO `computer` (`Id`, `LocationId`) VALUES
(100, 4),
(101, 4),
(102, 4),
(103, 4),
(104, 4),
(105, 4),
(106, 4),
(107, 4),
(108, 4),
(109, 4),
(110, 4);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `Id` int(11) NOT NULL,
  `LocationName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`Id`, `LocationName`) VALUES
(2, 'Swart'),
(3, 'Scotts'),
(4, 'Gruenhagen'),
(5, 'Taylor'),
(6, 'Halsey'),
(7, 'Dempsey'),
(8, 'Harrington'),
(9, 'Polk'),
(10, 'Sage'),
(11, 'Clow'),
(12, 'Evans'),
(13, 'Stewart'),
(14, 'Fletcher'),
(15, 'Horizon'),
(16, 'Reeve Union'),
(17, 'Health Center'),
(18, 'Donner');

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `Id` int(11) NOT NULL,
  `LogText` text NOT NULL,
  `Date` date NOT NULL,
  `UserId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `Id` int(11) NOT NULL,
  `ComputerId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `DateSubmitted` datetime NOT NULL,
  `Comments` text,
  `Rating` bit(1) DEFAULT NULL,
  `DateCompleted` datetime DEFAULT NULL,
  `Description` varchar(500) NOT NULL,
  `Status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ticketstatus`
--

CREATE TABLE `ticketstatus` (
  `Id` int(11) NOT NULL,
  `StatusName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ticketstatus`
--

INSERT INTO `ticketstatus` (`Id`, `StatusName`) VALUES
(1, ' Open'),
(2, 'In Progress'),
(3, 'Completed'),
(4, 'Ignored');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `Id` int(11) NOT NULL,
  `FirstName` varchar(25) NOT NULL,
  `LastName` varchar(25) NOT NULL,
  `AccessLevelId` int(11) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Username` varchar(25) NOT NULL,
  `Archived` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`Id`, `FirstName`, `LastName`, `AccessLevelId`, `Password`, `Username`, `Archived`) VALUES
(1, 'Joe', 'Joe', 1, 'password', 'password', b'1111111111111111111111111111111');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accesslevel`
--
ALTER TABLE `accesslevel`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `computer`
--
ALTER TABLE `computer`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Id` (`Id`),
  ADD KEY `LocationId` (`LocationId`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `UserId` (`UserId`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Status` (`Status`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `ComputerId` (`ComputerId`);

--
-- Indexes for table `ticketstatus`
--
ALTER TABLE `ticketstatus`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `AccessLevelId` (`AccessLevelId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `computer`
--
ALTER TABLE `computer`
  ADD CONSTRAINT `Computer_ibfk_1` FOREIGN KEY (`LocationId`) REFERENCES `location` (`Id`);

--
-- Constraints for table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `Log_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `user` (`Id`);

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `Ticket_ibfk_2` FOREIGN KEY (`ComputerId`) REFERENCES `computer` (`Id`),
  ADD CONSTRAINT `Ticket_ibfk_3` FOREIGN KEY (`UserId`) REFERENCES `user` (`Id`),
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`Status`) REFERENCES `ticketstatus` (`Id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `User_ibfk_1` FOREIGN KEY (`AccessLevelId`) REFERENCES `accesslevel` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
