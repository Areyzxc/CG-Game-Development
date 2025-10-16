-- Create user_topic_progress table
CREATE TABLE IF NOT EXISTS `user_topic_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `topic_id` varchar(100) NOT NULL,
  `status` enum('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_topic` (`user_id`, `topic_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_topic_id` (`topic_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraint if users table exists
-- ALTER TABLE `user_topic_progress`
-- ADD CONSTRAINT `fk_user_topic_progress_user` 
-- FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
