-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2026 at 03:52 AM
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
-- Database: `real_estate_portal_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddOrUpdateUser` (IN `p_userId` INT, IN `p_userName` VARCHAR(50), IN `p_contactInfo` VARCHAR(200), IN `p_passwordHash` VARCHAR(255), IN `p_userType` VARCHAR(20))   BEGIN

    IF p_userId IS NULL OR p_userId = 0 THEN
        INSERT INTO Users(userName, contactInfo, passwordHash, userType)
        VALUES (p_userName, p_contactInfo, p_passwordHash, p_userType);
    ELSE
        UPDATE Users
        SET userName = p_userName,
            contactInfo = p_contactInfo,
            passwordHash = p_passwordHash,
            userType = p_userType
        WHERE userId = p_userId;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ProcessTransaction` (IN `p_propertyId` INT, IN `p_userId` INT, IN `p_transactionType` VARCHAR(10), IN `p_amount` DECIMAL(12,2))   BEGIN
    INSERT INTO Transactions(propertyId, userId, transactionType, transactionDate, amount)
    VALUES (p_propertyId, p_userId, p_transactionType, NOW(), p_amount);

    IF p_transactionType = "sale" THEN
        UPDATE Properties
        SET status = "sold"
        WHERE propertyId = p_propertyId;

    ELSEIF p_transactionType = "rental" THEN
        UPDATE Properties
        SET status = "rented"
        WHERE propertyId = p_propertyId;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `favoriteId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `savedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`favoriteId`, `userId`, `propertyId`, `savedDate`) VALUES
(3, 2, 3, '2026-04-11 23:30:39'),
(4, 3, 1, '2026-04-26 20:44:26');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `inquiryId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `inquiryDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`inquiryId`, `userId`, `propertyId`, `message`, `inquiryDate`) VALUES
(1, 2, 1, 'Is this still available?', '2026-04-11 23:30:39'),
(2, 3, 2, 'Can I schedule a visit?', '2026-04-11 23:30:39'),
(3, 2, 3, 'What is the neighborhood like?', '2026-04-11 23:30:39'),
(4, 2, 1, 'How much for one week stay?', '2026-04-29 14:20:59');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `propertyId` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `propertyType` varchar(50) NOT NULL,
  `address` varchar(200) NOT NULL,
  `city` varchar(100) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `status` enum('available','sold','rented') NOT NULL DEFAULT 'available',
  `agentId` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_url2` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`propertyId`, `title`, `propertyType`, `address`, `city`, `price`, `status`, `agentId`, `image_url`, `image_url2`) VALUES
(1, 'Luxury Apartment', 'Apartment', '250 Bedford Park Boulevard', 'Bronx', 5000.00, 'available', 1, 'https://i.pinimg.com/736x/e0/90/d2/e090d2ac93aef1d63078ef6d74dec872--luxury-apartments-car-garage.jpg', 'https://iconiclife.com/wp-content/uploads/2020/10/Nashville-high-rise-luxury-apartment-by-Jules-Wilson-Design-Studio.jpg'),
(2, 'Family House', 'House', '456 Oak Ave', 'Los Angeles', 750000.00, 'sold', 1, 'https://housiey.com/blogs/wp-content/uploads/2025/01/Peach-and-Olive-Green.jpg', 'https://st.hzcdn.com/simgs/pictures/family-rooms/cozy-family-room-garrison-hullinger-interior-design-inc-img~20b17993043c5c81_9-4733-1-6645c62.jpg'),
(3, 'Modern 2026 Condo', 'Condo', '789 Pine Rd', 'Chicago', 3000000.00, 'rented', 1, 'https://prop.sg/wp-content/uploads/2026/01/Ardor_Residence_featured_opm-768x768.jpg', 'https://images.squarespace-cdn.com/content/v1/5d6d67f2387da800015dc00e/cbeb723d-24f1-4ece-9cf6-8fbc19b7dce1/shangrila_sansainteriors.jpg?format=2500w');

-- --------------------------------------------------------

--
-- Stand-in structure for view `propertylistingview`
-- (See below for the actual view)
--
CREATE TABLE `propertylistingview` (
`propertyId` int(11)
,`title` varchar(100)
,`propertyType` varchar(50)
,`address` varchar(200)
,`city` varchar(100)
,`price` decimal(12,2)
,`status` enum('available','sold','rented')
,`image_url` varchar(255)
,`image_url2` varchar(255)
,`agentName` varchar(50)
);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transactionId` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `transactionType` enum('sale','rental') NOT NULL,
  `transactionDate` datetime NOT NULL,
  `amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transactionId`, `propertyId`, `userId`, `transactionType`, `transactionDate`, `amount`) VALUES
(1, 1, 2, 'sale', '2026-04-11 23:30:39', 50000.00),
(2, 2, 3, 'rental', '2026-04-11 23:30:39', 2500.00),
(3, 3, 2, 'sale', '2026-04-11 23:30:39', 3000000.00),
(4, 3, 3, 'sale', '2026-04-26 21:04:19', 3000000.00),
(5, 3, 3, 'rental', '2026-04-26 21:04:26', 3000000.00),
(6, 2, 2, 'sale', '2026-04-26 21:06:40', 750000.00);

--
-- Triggers `transactions`
--
DELIMITER $$
CREATE TRIGGER `AfterTransactionInsert` AFTER INSERT ON `transactions` FOR EACH ROW BEGIN

    IF NEW.transactionType = "sale" THEN         # so if a property have been bough, itll show sold or rented
        UPDATE Properties
        SET status = "sold"
        WHERE propertyId = NEW.propertyId;

    ELSEIF NEW.transactionType = "rental" THEN
        UPDATE Properties
        SET status = "rented"
        WHERE propertyId = NEW.propertyId;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `userName` varchar(50) NOT NULL,
  `contactInfo` varchar(200) DEFAULT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `userType` enum('agent','buyer','renter') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `userName`, `contactInfo`, `passwordHash`, `userType`) VALUES
(1, 'agent_Nasir', 'agent_Nasir1@gmail.com', '$2y$10$.jxqRMQjOCRWiNOV/NI72uRQtU/O4dZiDLva9wy4kymRhNPM0/Scy', 'agent'),
(2, 'buyer_Naz', 'buyer_Naz1@gmail.com', '$2y$10$kk/UqxXH1G25VMJy1JRI7.MGvMKfIryYos5KSMVkyjtepa3Ew8uBy', 'buyer'),
(3, 'renter_Ahmed', 'renter_Ahmed1@gmail.com', '$2y$10$6n8h1dRMRxkVcKMhvfyzhuw4dJSmwsJckPOs1QiLKTBRHV50UGmaS', 'renter'),
(4, 'agent_abu', 'agentgenz@gmail.com', '$2y$10$X/o.Wuqcjjs3C3Z4cTSIrehhTpkhS39tWNSaHI.lok17ELvIE60J6', 'agent');

-- --------------------------------------------------------

--
-- Structure for view `propertylistingview`
--
DROP TABLE IF EXISTS `propertylistingview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `propertylistingview`  AS SELECT `properties`.`propertyId` AS `propertyId`, `properties`.`title` AS `title`, `properties`.`propertyType` AS `propertyType`, `properties`.`address` AS `address`, `properties`.`city` AS `city`, `properties`.`price` AS `price`, `properties`.`status` AS `status`, `properties`.`image_url` AS `image_url`, `properties`.`image_url2` AS `image_url2`, `users`.`userName` AS `agentName` FROM (`properties` join `users` on(`properties`.`agentId` = `users`.`userId`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favoriteId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `propertyId` (`propertyId`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`inquiryId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `propertyId` (`propertyId`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`propertyId`),
  ADD KEY `agentId` (`agentId`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transactionId`),
  ADD KEY `propertyId` (`propertyId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `userName` (`userName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favoriteId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `inquiryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `propertyId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transactionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`propertyId`) REFERENCES `properties` (`propertyId`);

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`),
  ADD CONSTRAINT `inquiries_ibfk_2` FOREIGN KEY (`propertyId`) REFERENCES `properties` (`propertyId`);

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`agentId`) REFERENCES `users` (`userId`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`propertyId`) REFERENCES `properties` (`propertyId`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
