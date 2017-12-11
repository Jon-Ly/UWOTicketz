-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2017 at 06:37 AM
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
CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `DeleteComputer` (IN `computerId` INT)  NO SQL
DELETE FROM
	computer
WHERE
	Id = computerId$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetAccessLevelByUser` (IN `uname` VARCHAR(100))  NO SQL
SELECT
	AccessLevel
FROM
	user
    INNER JOIN accesslevel ON AccessLevelId = accesslevel.Id
WHERE
	Username = uname$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetAllAccessLevels` ()  NO SQL
SELECT
	Id,
    AccessLevel
FROM 
	accesslevel$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetAllComputers` ()  NO SQL
SELECT
	computer.Id,
    LocationName,
    location.Id AS LocationId
FROM
	computer
    INNER JOIN location ON computer.LocationId = location.Id
ORDER BY
	computer.Id
ASC$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetAllLocations` ()  NO SQL
SELECT
	Id,
    LocationName
FROM
	location
ORDER BY
	LocationName
ASC$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetAllStatuses` ()  NO SQL
SELECT
	Id,
    StatusName
FROM
	ticketstatus$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetAllTickets` ()  READS SQL DATA
SELECT
	Ticket.Id, 
    ComputerId, 
    DateSubmitted, 
    DateCompleted, 
    StatusName, 
    Rating,
    UserAssignedId
FROM 
	Ticket 
    INNER JOIN ticketstatus ON ticketstatus.Id = Ticket.Status
ORDER BY
	DateSubmitted
DESC$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetAllUsers` ()  NO SQL
SELECT
	FirstName,
    LastName,
    Username,
    AccessLevel
FROM User
INNER JOIN accesslevel ON AccessLevelId = accesslevel.Id$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetComputerById` (IN `computerId` INT(11) UNSIGNED)  NO SQL
SELECT
	Id,
    LocationId
FROM
	computer
WHERE
	Id = computerId$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetPassword` (IN `uname` VARCHAR(10))  NO SQL
SELECT
	Password
FROM
	user
WHERE
	Username = uname$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetStatusById` (IN `statusId` INT(11))  NO SQL
SELECT
	Id,
    StatusName
FROM
	ticketstatus
WHERE
	Id = statusId$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetTicketInformationById` (IN `ticketId` INT)  NO SQL
IF EXISTS(
	SELECT
        Description,
        Comment,
        Username,
        comments.DateSubmitted,
    	Status
    FROM
        ticket
        INNER JOIN comments ON ticket.Id = comments.TicketNumber
        INNER JOIN user ON comments.UserId = user.Id
    WHERE
        ticket.Id = ticketId
    ) THEN
   SELECT
        Description,
        Comment,
        Username,
        comments.DateSubmitted
    FROM
        ticket
        INNER JOIN comments ON ticket.Id = comments.TicketNumber
        INNER JOIN user ON comments.UserId = user.Id
    WHERE
        ticket.Id = ticketId;
ELSE
   SELECT
        Description,
        Username,
        Status
    FROM
        ticket
        INNER JOIN user ON ticket.UserId = user.Id
    WHERE
        ticket.Id = ticketId;
END IF$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetTicketsByUserId` (IN `userId` INT(11))  NO SQL
SELECT
	ticket.Id AS TicketId,
    ComputerId,
    LocationName,
    DateSubmitted,
    DateCompleted,
    Status,
    Description,
    Rating,
    Comments,
    UserId
FROM
	ticket 
    INNER JOIN user ON ticket.UserId = user.Id
    INNER JOIN computer ON ComputerId = computer.Id
    INNER JOIN location ON LocationId = location.Id
WHERE
	user.Id = userId
ORDER BY
	ticket.Id ASC$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetTimeStampForTicket` (IN `ticketNumber` INT(11))  NO SQL
SELECT
	DateCompleted
FROM
	ticket
WHERE
	Id = ticketNumber$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetUser` (IN `uname` VARCHAR(100))  NO SQL
SELECT
	Id,
    Password
FROM
	user
WHERE
	Username = uname$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetUserByTicket` (IN `ticketId` INT)  NO SQL
SELECT
	Username
FROM
	ticket
    INNER JOIN user ON ticket.UserId = user.Id
WHERE
	ticket.Id = ticketId$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `GetUsernameById` (IN `userId` INT(11))  NO SQL
SELECT
	user.Username
FROM
	user
WHERE
	user.Id = userId$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `InsertComment` (IN `userId` INT(11), IN `ticketId` INT(11), IN `comment` TEXT)  NO SQL
INSERT INTO comments
(UserId, TicketNumber, Comment, DateSubmitted)
VALUES
(userId, ticketId, comment, CURRENT_TIMESTAMP)$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `InsertComputer` (IN `computerId` INT(11), IN `location` INT(11))  NO SQL
INSERT INTO computer
(Id, LocationId)
VALUES
(computerId, location)$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `InsertPassword` (IN `pword` VARCHAR(100), IN `userId` INT)  NO SQL
UPDATE
	user
SET
	Password = pword
WHERE
	Id = userId$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `InsertRating` (IN `ticketId` INT, IN `ratingId` INT)  NO SQL
UPDATE
	ticket
SET
	Rating = ratingId
WHERE
	ticket.Id = ticketId$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `InsertTicket` (IN `computerId` INT(11) UNSIGNED, IN `description` VARCHAR(500), IN `userId` INT(11) UNSIGNED)  MODIFIES SQL DATA
INSERT INTO ticket
	(ComputerId, UserId, DateSubmitted, Description, Status)
VALUES
	(computerId, userId, CURRENT_TIMESTAMP, description, 1)$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `InsertUser` (IN `firstName` VARCHAR(25), IN `lastName` VARCHAR(25), IN `username` VARCHAR(10), IN `accessLevel` INT(11))  NO SQL
INSERT INTO user
(FirstName, LastName, Username, AccessLevelId, Archived)
VALUES
(firstName, lastName, username, accessLevel, 0)$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `UpdateComputer` (IN `newId` INT, IN `location` INT, IN `oldId` INT)  NO SQL
BEGIN
	INSERT INTO computer (Id, LocationId)
    VALUES(newId, location);
    
	UPDATE
    	ticket
    SET
    	ticket.ComputerId = newId
    WHERE
    	ticket.ComputerId = oldId;
    DELETE FROM
        computer
    WHERE
        Id = oldId;
END$$

CREATE DEFINER=`lyj47`@`localhost` PROCEDURE `UpdateTicketStatus` (IN `ticketNumber` INT, IN `statusId` INT, IN `name` VARCHAR(25), IN `userId` INT)  NO SQL
IF name = 'Completed' THEN
    UPDATE ticket SET
        Status = statusId,
        DateCompleted = CURRENT_TIMESTAMP
    WHERE
        Id = ticketNumber;
ELSEIF name = 'In Progress' THEN
	UPDATE ticket SET
        Status = statusId,
       	UserAssignedId = userId
    WHERE
        Id = ticketNumber;
ELSEIF name = 'Open' THEN
	UPDATE ticket SET
        Status = statusId,
       	UserAssignedId = NULL
    WHERE
        Id = ticketNumber;
ELSE
	UPDATE ticket SET
        Status = statusId
    WHERE
        Id = ticketNumber;
END IF$$

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
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `Id` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `TicketNumber` int(11) NOT NULL,
  `Comment` text NOT NULL,
  `DateSubmitted` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(111, 4),
(112, 4),
(1001, 4),
(1015, 4),
(1022, 4),
(1033, 4),
(1077, 4),
(1088, 4),
(1099, 4),
(114, 7),
(117, 7),
(118, 8),
(116, 11),
(115, 14),
(113, 18);

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
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `Id` int(11) NOT NULL,
  `ComputerId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `DateSubmitted` datetime NOT NULL,
  `Comments` text,
  `Rating` smallint(2) DEFAULT NULL,
  `DateCompleted` datetime DEFAULT NULL,
  `Description` varchar(500) NOT NULL,
  `Status` int(11) NOT NULL,
  `UserAssignedId` int(11) NOT NULL
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
  `Password` char(60) DEFAULT NULL,
  `Username` varchar(10) NOT NULL,
  `Archived` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`Id`, `FirstName`, `LastName`, `AccessLevelId`, `Password`, `Username`, `Archived`) VALUES
(11, 'Admin', 'Admin', 1, '$2y$10$nrObYbZser5/5DWwuYaZBO1M/29my2MFJoQuhSXy9HHDde7pPFXle', 'Admin', 0),
(14, 'Auditor', 'Auditor', 2, '$2y$10$oue6gqqXh.YMcnVWLxFXxuOGyiupPA8kwCGUeL5Nt2fXDYz29RDqO', 'Auditor', 0),
(15, 'User', 'User', 0, '$2y$10$wcojZntBdu268rVAH6dHJOB5NFVMPbVwrkrgbPoywcW7GrfPWphse', 'User', 0),
(16, 'Test', 'Test', 0, '$2y$10$aPY5ZzMrtCmkvgZ0zjbJjOtla0D9AwyXiVZc5KYwGLoe//Bx1fdJC', 'Test', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accesslevel`
--
ALTER TABLE `accesslevel`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `TicketNumber` (`TicketNumber`);

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
  ADD UNIQUE KEY `Username` (`Username`),
  ADD KEY `AccessLevelId` (`AccessLevelId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`TicketNumber`) REFERENCES `ticket` (`Id`);

--
-- Constraints for table `computer`
--
ALTER TABLE `computer`
  ADD CONSTRAINT `Computer_ibfk_1` FOREIGN KEY (`LocationId`) REFERENCES `location` (`Id`);

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
