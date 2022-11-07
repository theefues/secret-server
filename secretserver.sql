SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `secretserver`
--
CREATE DATABASE IF NOT EXISTS `secretserver` DEFAULT CHARACTER SET utf32 COLLATE utf32_hungarian_ci;
USE `secretserver`;

-- --------------------------------------------------------

--
-- Table structure for table `secrets`
--

CREATE TABLE `secrets` (
  `id` int(11) NOT NULL,
  `hash` varchar(255) COLLATE utf32_hungarian_ci NOT NULL,
  `expiresAt` datetime DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `expiresAfterViews` int(11) NOT NULL,
  `currentViews` int(11) NOT NULL DEFAULT 0,
  `secret` varchar(255) COLLATE utf32_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_hungarian_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `secrets`
--
ALTER TABLE `secrets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `secrets`
--
ALTER TABLE `secrets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
