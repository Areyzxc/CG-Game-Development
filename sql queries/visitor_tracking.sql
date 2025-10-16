-- Create visitor_tracking table
CREATE TABLE IF NOT EXISTS `visitor_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `page_visited` varchar(255) NOT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `visit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_unique` tinyint(1) DEFAULT '0',
  `session_id` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `is_bot` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ip_address` (`ip_address`),
  KEY `session_id` (`session_id`),
  KEY `visit_time` (`visit_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create visitor_stats table for aggregated data
CREATE TABLE IF NOT EXISTS `visitor_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_date` date NOT NULL,
  `total_visits` int(11) NOT NULL DEFAULT '0',
  `unique_visits` int(11) NOT NULL DEFAULT '0',
  `page_views` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `visit_date` (`visit_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
