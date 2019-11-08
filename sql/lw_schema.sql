-- --------------------------------------------------------
-- Server version:               5.7.27 - Gentoo Linux mysql-5.7.27-r1
-- Server OS:                    Linux
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table lahwa.lw_events
CREATE TABLE IF NOT EXISTS `lw_events` (
  `sid` int(10) unsigned NOT NULL COMMENT 'Subject numerical ID',
  `commevent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp of commenting event',
  UNIQUE KEY `sid_commevent` (`sid`,`commevent`),
  CONSTRAINT `FK_event_subjects` FOREIGN KEY (`sid`) REFERENCES `lw_subjects` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Events';

-- Data exporting was unselected.

-- Dumping structure for table lahwa.lw_subjects
CREATE TABLE IF NOT EXISTS `lw_subjects` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Subject numerical ID',
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Subject username',
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Subjects of investigation';

-- Data exporting was unselected.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
