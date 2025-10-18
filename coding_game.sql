-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3327
-- Generation Time: Oct 07, 2025 at 03:40 PM
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
-- Database: `coding_game`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_actions_log`
--
-- Creation: Jul 02, 2025 at 02:00 PM
--

CREATE TABLE `admin_actions_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `target_type` enum('user','admin') NOT NULL,
  `target_id` int(11) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `admin_actions_log`:
--   `admin_id`
--       `admin_users` -> `admin_id`
--

--
-- Dumping data for table `admin_actions_log`
--

INSERT INTO `admin_actions_log` (`id`, `admin_id`, `action_type`, `target_type`, `target_id`, `details`, `created_at`) VALUES
(1, 1, 'unban', 'user', 4, NULL, '2025-07-02 14:00:28'),
(2, 1, 'ban', 'user', 7, NULL, '2025-07-24 06:51:11'),
(3, 1, 'unban', 'user', 7, NULL, '2025-07-24 06:51:30'),
(4, 1, 'ban', 'admin', 2, NULL, '2025-07-24 06:52:28'),
(5, 1, 'unban', 'admin', 2, NULL, '2025-07-24 06:52:45'),
(6, 1, 'ban', 'user', 7, NULL, '2025-07-24 07:53:12'),
(7, 1, 'unban', 'user', 7, NULL, '2025-07-24 07:53:17'),
(8, 1, 'ban', 'user', 1, NULL, '2025-07-24 07:56:33'),
(9, 1, 'unban', 'user', 1, NULL, '2025-07-24 07:56:38'),
(10, 6, 'ban', 'user', 5, NULL, '2025-10-02 14:01:37'),
(11, 6, 'unban', 'user', 5, NULL, '2025-10-02 14:21:50');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--
-- Creation: Sep 27, 2025 at 02:57 PM
--

CREATE TABLE `admin_users` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','super_admin') NOT NULL DEFAULT 'admin',
  `profile_picture` varchar(255) DEFAULT NULL,
  `is_banned` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen` timestamp NULL DEFAULT NULL,
  `first_visit` tinyint(1) DEFAULT 1 COMMENT 'Tracks if admin has seen the welcome modal on first visit',
  `welcome_dont_show` tinyint(1) DEFAULT 0 COMMENT 'Admin preference to not show welcome modal again'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `admin_users`:
--

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`admin_id`, `username`, `email`, `password_hash`, `role`, `profile_picture`, `is_banned`, `created_at`, `last_seen`, `first_visit`, `welcome_dont_show`) VALUES
(1, 'admin', 'admin@codegame.dev', '$2y$10$D78Au.rfIe/XyWwfvVzb/eIP0qC7FSxlvXQjP2VfSkYNQDcuwEp7e', 'super_admin', NULL, 0, '2025-06-29 03:02:00', '2025-09-19 12:44:07', 1, 0),
(2, 'Amogus', 'admin@yahoo.com', '$2y$10$ekWoB4fuJZUXJ0dv2Z0r8OjZF7swOFD9876s9F4FGdI3U/tIYSeKK', 'admin', NULL, 0, '2025-06-29 04:12:42', '2025-09-12 23:52:34', 1, 0),
(4, 'Areys27', 'aries@codegame.dev', '$2y$10$l2VoDSdJOGQTkHTEa1EYXuvSuGPd0LffAp0VrHJjvd1yZDECtaFIa', 'admin', NULL, 0, '2025-07-04 11:17:42', '2025-10-06 13:06:34', 1, 0),
(6, 'TheGoattt', 'jags@gmail.com', '$2y$10$6YHoxG.ERMRhRk50zmEMAu5Am9rdVqErgIP7DFS.jZ7UCugrL4ZRW', 'admin', 'admin_6_1759419660.PNG', 0, '2025-10-02 11:29:32', '2025-10-02 16:03:08', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--
-- Creation: Jul 02, 2025 at 04:10 PM
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(50) DEFAULT 'general',
  `status` enum('published','draft') DEFAULT 'published',
  `is_pinned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `announcements`:
--   `created_by`
--       `admin_users` -> `admin_id`
--

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `category`, `status`, `is_pinned`, `created_at`, `created_by`, `is_active`) VALUES
(1, 'Welcome to Code Gaming!', 'We are excited to launch our new learning platform. Start your coding journey today!', 'system', 'published', 1, '2025-06-29 03:02:00', 1, 1),
(2, 'New Features Available', 'Check out our latest tutorials and interactive quizzes. More content coming soon!', 'general', 'published', 0, '2025-06-29 03:02:00', 1, 1),
(3, 'System Maintenance', 'Scheduled maintenance on July 1st from 2-4 AM. We apologize for any inconvenience.', 'general', 'published', 0, '2025-06-29 03:02:00', 1, 1),
(4, 'Welcome to Code Gaming! :)', 'Let me know your thoughts!', 'update', 'published', 1, '2025-07-04 11:38:20', 4, 1),
(6, 'Almost There', 'Stay patient y\'all :)', 'system', 'published', 0, '2025-10-02 15:41:59', 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `challenge_answers`
--
-- Creation: Sep 27, 2025 at 03:46 PM
--

CREATE TABLE `challenge_answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 1,
  `explanation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `challenge_answers`:
--   `question_id`
--       `challenge_questions` -> `id`
--

--
-- Dumping data for table `challenge_answers`
--

INSERT INTO `challenge_answers` (`id`, `question_id`, `answer_text`, `is_correct`, `explanation`, `created_at`) VALUES
(1, 1, 'i++', 1, 'Correct! i++ increments the loop counter by 1 each iteration.', '2025-09-27 15:46:38'),
(2, 1, '++i', 1, 'Correct! ++i also increments the loop counter by 1 each iteration.', '2025-09-27 15:46:38'),
(3, 1, 'i--', 0, 'Incorrect. i-- would decrement the counter, creating an infinite loop.', '2025-09-27 15:46:38'),
(4, 1, 'i+1', 0, 'Incorrect. i+1 doesn\'t modify the variable i, creating an infinite loop.', '2025-09-27 15:46:38'),
(5, 2, '[1, 2, 3, 4]', 1, 'Correct! y and x reference the same list object, so modifying y also modifies x.', '2025-09-27 15:46:38'),
(6, 2, '[1, 2, 3]', 0, 'Incorrect. Since y = x creates a reference, not a copy, both variables point to the same list.', '2025-09-27 15:46:38'),
(7, 2, '[4]', 0, 'Incorrect. The append method adds to the existing list, it doesn\'t replace it.', '2025-09-27 15:46:38'),
(8, 2, 'Error', 0, 'Incorrect. This code runs without errors.', '2025-09-27 15:46:38'),
(9, 3, 'function reverseString(str) {\\n    let result = \"\";\\n    for (let i = str.length - 1; i >= 0; i--) {\\n        result += str[i];\\n    }\\n    return result;\\n}', 1, 'Excellent! This solution iterates backwards through the string and builds the reversed result.', '2025-09-27 15:46:38'),
(10, 3, 'function reverseString(str) {\\n    return str.split(\"\").reverse().join(\"\");\\n}', 0, 'This works but uses the built-in reverse() method, which was not allowed in the problem.', '2025-09-27 15:46:38'),
(11, 3, 'function reverseString(str) {\\n    if (str.length <= 1) return str;\\n    return reverseString(str.slice(1)) + str[0];\\n}', 1, 'Great! This is a recursive solution that works correctly.', '2025-09-27 15:46:38'),
(12, 4, 'Add indexes on user_id and created_date columns', 1, 'Correct! Indexes on frequently queried columns dramatically improve SELECT performance.', '2025-09-27 15:46:38'),
(13, 4, 'Add composite index on (user_id, created_date)', 1, 'Excellent! A composite index is even better for queries filtering on both columns.', '2025-09-27 15:46:38'),
(14, 4, 'Increase server RAM', 0, 'While more RAM can help, adding proper indexes is more cost-effective and targeted.', '2025-09-27 15:46:38'),
(15, 4, 'Partition the table', 0, 'Partitioning can help, but indexes should be tried first as they\'re simpler to implement.', '2025-09-27 15:46:38'),
(16, 5, 'filter', 1, 'Correct! The filter() method creates a new array with elements that pass the test function.', '2025-09-27 15:46:38'),
(17, 5, 'map', 0, 'Incorrect. map() transforms each element but doesn\'t filter them out.', '2025-09-27 15:46:38'),
(18, 5, 'forEach', 0, 'Incorrect. forEach() executes a function for each element but doesn\'t return a new array.', '2025-09-27 15:46:38'),
(19, 5, 'reduce', 0, 'Incorrect. While reduce() could be used for filtering, filter() is the correct method here.', '2025-09-27 15:46:38'),
(20, 6, 'Child', 1, 'Correct! This demonstrates polymorphism - the overridden method in Child is called.', '2025-09-27 15:46:38'),
(21, 6, 'Parent', 0, 'Incorrect. Even though p is declared as Parent, it points to a Child object, so Child\'s method is called.', '2025-09-27 15:46:38'),
(22, 6, 'Compilation Error', 0, 'Incorrect. This code compiles and runs successfully.', '2025-09-27 15:46:38'),
(23, 6, 'Runtime Error', 0, 'Incorrect. This code runs without errors.', '2025-09-27 15:46:38'),
(24, 7, 'function isPalindrome(str) {\\n    const cleaned = str.toLowerCase().replace(/[^a-z0-9]/g, \"\");\\n    return cleaned === cleaned.split(\"\").reverse().join(\"\");\\n}', 1, 'Great! This solution cleans the string and compares it with its reverse.', '2025-09-27 15:46:38'),
(25, 7, 'function isPalindrome(str) {\\n    const cleaned = str.toLowerCase().replace(/\\s/g, \"\");\\n    let left = 0, right = cleaned.length - 1;\\n    while (left < right) {\\n        if (cleaned[left] !== cleaned[right]) return false;\\n        left++; right--;\\n    }\\n    return true;\\n}', 1, 'Excellent! This two-pointer approach is efficient and handles the requirements well.', '2025-09-27 15:46:38'),
(26, 8, 'Event listeners not being removed, causing memory leaks', 1, 'Correct! Unremoved event listeners are a common cause of memory leaks in Node.js.', '2025-09-27 15:46:38'),
(27, 8, 'Circular references preventing garbage collection', 1, 'Correct! Circular references can prevent objects from being garbage collected.', '2025-09-27 15:46:38'),
(28, 8, 'Too many global variables', 0, 'While global variables use memory, they typically don\'t cause growing leaks during idle periods.', '2025-09-27 15:46:38'),
(29, 8, 'Insufficient server RAM', 0, 'RAM amount doesn\'t cause memory leaks - the issue is with code not releasing memory properly.', '2025-09-27 15:46:38'),
(30, 9, 'center center', 1, 'Correct! justify-content: center centers horizontally, align-items: center centers vertically.', '2025-09-27 15:46:38'),
(31, 9, 'center, center', 1, 'Correct! This is another way to express the same answer.', '2025-09-27 15:46:38'),
(32, 9, 'middle middle', 0, 'Incorrect. The correct values are \"center\", not \"middle\".', '2025-09-27 15:46:38'),
(33, 9, 'auto auto', 0, 'Incorrect. \"auto\" doesn\'t center the content in flexbox.', '2025-09-27 15:46:38'),
(34, 10, '3', 1, 'Correct! Alice (25), Bob (30), and Charlie (25) match the condition age >= 25 AND age < 35.', '2025-09-27 15:46:38'),
(35, 10, '2', 0, 'Incorrect. Don\'t forget that both Alice and Charlie have age 25, which satisfies age >= 25.', '2025-09-27 15:46:38'),
(36, 10, '4', 0, 'Incorrect. Diana (35) doesn\'t match because 35 is not less than 35.', '2025-09-27 15:46:38'),
(37, 10, '1', 0, 'Incorrect. Multiple users match the age criteria.', '2025-09-27 15:46:38'),
(38, 11, 'function binarySearch(arr, target) {\\n    let left = 0, right = arr.length - 1;\\n    while (left <= right) {\\n        const mid = Math.floor((left + right) / 2);\\n        if (arr[mid] === target) return mid;\\n        if (arr[mid] < target) left = mid + 1;\\n        else right = mid - 1;\\n    }\\n    return -1;\\n}', 1, 'Perfect! This is the classic iterative binary search implementation with O(log n) time complexity.', '2025-09-27 15:46:38'),
(39, 11, 'function binarySearch(arr, target) {\\n    function search(left, right) {\\n        if (left > right) return -1;\\n        const mid = Math.floor((left + right) / 2);\\n        if (arr[mid] === target) return mid;\\n        if (arr[mid] < target) return search(mid + 1, right);\\n        return search(left, mid - 1);\\n    }\\n    return search(0, arr.length - 1);\\n}', 1, 'Excellent! This is the recursive binary search implementation, also correct.', '2025-09-27 15:46:38'),
(40, 12, 'Implement proper authentication and authorization', 1, 'Correct! Authentication verifies who the user is, and authorization ensures they can only access their own data.', '2025-09-27 15:46:38'),
(41, 12, 'Add rate limiting', 0, 'Rate limiting helps prevent abuse but doesn\'t solve the authorization problem described.', '2025-09-27 15:46:38'),
(42, 12, 'Use HTTPS encryption', 0, 'HTTPS is important but doesn\'t prevent authorized users from accessing unauthorized data.', '2025-09-27 15:46:38'),
(43, 12, 'Input validation', 0, 'Input validation is important but doesn\'t address the core authorization issue.', '2025-09-27 15:46:38'),
(44, 13, 'x % 2 == 0', 1, 'Correct! x % 2 == 0 checks if a number is even (remainder is 0 when divided by 2).', '2025-09-27 15:46:38'),
(45, 13, 'x % 2 != 1', 1, 'Correct! This is another way to check for even numbers.', '2025-09-27 15:46:38'),
(46, 13, 'x % 2 == 1', 0, 'Incorrect. This condition checks for odd numbers, not even numbers.', '2025-09-27 15:46:38'),
(47, 13, 'x / 2 == 0', 0, 'Incorrect. This would only be true for x = 0, and uses division instead of modulo.', '2025-09-27 15:46:38'),
(48, 14, '10', 1, 'Correct! ptr points to x, so *ptr = 10 changes the value of x to 10.', '2025-09-27 15:46:38'),
(49, 14, '5', 0, 'Incorrect. The value of x is changed through the pointer dereference *ptr = 10.', '2025-09-27 15:46:38'),
(50, 14, 'Compilation Error', 0, 'Incorrect. This code compiles successfully.', '2025-09-27 15:46:38'),
(51, 14, 'Undefined Behavior', 0, 'Incorrect. This is well-defined behavior in C++.', '2025-09-27 15:46:38'),
(52, 15, 'function fibonacci(n) {\\n    if (n <= 1) return n;\\n    let a = 0, b = 1;\\n    for (let i = 2; i <= n; i++) {\\n        [a, b] = [b, a + b];\\n    }\\n    return b;\\n}', 1, 'Excellent! This iterative solution has O(n) time and O(1) space complexity.', '2025-09-27 15:46:38'),
(53, 15, 'function fibonacci(n) {\\n    if (n <= 1) return n;\\n    const dp = [0, 1];\\n    for (let i = 2; i <= n; i++) {\\n        dp[i] = dp[i-1] + dp[i-2];\\n    }\\n    return dp[n];\\n}', 1, 'Great! This dynamic programming solution with memoization is also correct.', '2025-09-27 15:46:38'),
(54, 16, 'Implement horizontal scaling with load balancers', 1, 'Correct! Horizontal scaling (adding more servers) with load balancing distributes traffic effectively.', '2025-09-27 15:46:38'),
(55, 16, 'Upgrade to a more powerful server (vertical scaling)', 0, 'Vertical scaling has limits and a single point of failure. Horizontal scaling is better for this scale.', '2025-09-27 15:46:38'),
(56, 16, 'Add more RAM to the existing server', 0, 'More RAM alone won\'t handle 100,000 concurrent users on a single server.', '2025-09-27 15:46:38'),
(57, 16, 'Optimize database queries only', 0, 'While database optimization helps, the scale requires architectural changes.', '2025-09-27 15:46:38'),
(58, 17, 'setCount setCount', 1, 'Correct! useState returns an array with the state value and its setter function.', '2025-09-27 15:46:38'),
(59, 17, 'setCount, setCount', 1, 'Correct! This is another way to express the same answer.', '2025-09-27 15:46:38'),
(60, 17, 'updateCount updateCount', 0, 'Incorrect. The convention is to name the setter function \"set\" + state variable name.', '2025-09-27 15:46:38'),
(61, 17, 'changeCount changeCount', 0, 'Incorrect. The setter function should be named setCount by convention.', '2025-09-27 15:46:38'),
(62, 18, 'Reset to the previous commit and discard all changes', 1, 'Correct! --hard resets the working directory and index, HEAD~1 means one commit before current.', '2025-09-27 15:46:38'),
(63, 18, 'Create a new branch from the previous commit', 0, 'Incorrect. This command doesn\'t create a branch, it moves the current branch pointer.', '2025-09-27 15:46:38'),
(64, 18, 'Merge the previous commit into current branch', 0, 'Incorrect. This is a reset operation, not a merge.', '2025-09-27 15:46:38'),
(65, 18, 'Delete the previous commit permanently', 0, 'Incorrect. The commit still exists in Git\'s history, just not in the current branch.', '2025-09-27 15:46:38'),
(66, 19, 'function bubbleSort(arr) {\\n    const n = arr.length;\\n    for (let i = 0; i < n - 1; i++) {\\n        for (let j = 0; j < n - i - 1; j++) {\\n            if (arr[j] > arr[j + 1]) {\\n                [arr[j], arr[j + 1]] = [arr[j + 1], arr[j]];\\n            }\\n        }\\n    }\\n    return arr;\\n}', 1, 'Perfect! This is the classic bubble sort implementation with proper optimization.', '2025-09-27 15:46:38'),
(67, 19, 'function bubbleSort(arr) {\\n    let swapped;\\n    do {\\n        swapped = false;\\n        for (let i = 0; i < arr.length - 1; i++) {\\n            if (arr[i] > arr[i + 1]) {\\n                [arr[i], arr[i + 1]] = [arr[i + 1], arr[i]];\\n                swapped = true;\\n            }\\n        }\\n    } while (swapped);\\n    return arr;\\n}', 1, 'Excellent! This optimized version stops early when no swaps are needed.', '2025-09-27 15:46:38'),
(68, 20, 'Implement distributed transaction management and eventual consistency', 1, 'Correct! Data consistency across distributed services is indeed the biggest challenge in microservices.', '2025-09-27 15:46:38'),
(69, 20, 'Set up service discovery and load balancing', 0, 'While important, service discovery is easier to solve than distributed data consistency.', '2025-09-27 15:46:38'),
(70, 20, 'Implement API gateways', 0, 'API gateways are important but don\'t address the core data consistency challenge.', '2025-09-27 15:46:38'),
(71, 20, 'Containerize all services', 0, 'Containerization is a deployment concern, not the main challenge with data consistency.', '2025-09-27 15:46:38');

-- --------------------------------------------------------

--
-- Table structure for table `challenge_leaderboard`
--
-- Creation: Jul 18, 2025 at 05:10 AM
--

CREATE TABLE `challenge_leaderboard` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_session_id` int(11) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `total_score` int(11) NOT NULL,
  `total_time` float DEFAULT NULL,
  `questions_attempted` int(11) DEFAULT 0,
  `questions_correct` int(11) DEFAULT 0,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `challenge_leaderboard`:
--   `user_id`
--       `users` -> `id`
--   `guest_session_id`
--       `guest_sessions` -> `id`
--

--
-- Dumping data for table `challenge_leaderboard`
--

INSERT INTO `challenge_leaderboard` (`id`, `user_id`, `guest_session_id`, `nickname`, `total_score`, `total_time`, `questions_attempted`, `questions_correct`, `completed_at`) VALUES
(1, 5, NULL, 'James Aries', 0, 198, 20, 0, '2025-07-18 07:13:58'),
(2, 5, NULL, 'James Aries', 0, 184, 20, 0, '2025-07-18 07:17:42'),
(3, 6, NULL, 'areys2003', 0, 119, 20, 0, '2025-07-18 08:16:17'),
(4, NULL, 10, 'Areyszxcv', 0, 150, 20, 0, '2025-07-20 09:15:18'),
(5, 8, NULL, 'Amogus27', 0, 355, 20, 0, '2025-09-13 00:02:05'),
(6, 4, NULL, 'Areyszxc', 90, 150, 20, 3, '2025-09-27 15:52:42'),
(7, NULL, 11, 'Areyszxc36', 0, 205, 20, 0, '2025-09-29 09:42:10'),
(8, NULL, 12, 'Areyszxc36', 0, 150, 20, 0, '2025-09-29 09:45:33');

-- --------------------------------------------------------

--
-- Table structure for table `challenge_questions`
--
-- Creation: Sep 27, 2025 at 03:46 PM
--

CREATE TABLE `challenge_questions` (
  `id` int(11) NOT NULL,
  `type` enum('fill_blank','output','case_study','code') NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `starter_code` text DEFAULT NULL,
  `expected_output` text DEFAULT NULL,
  `difficulty` enum('expert') NOT NULL DEFAULT 'expert',
  `points` int(11) DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `challenge_questions`:
--

--
-- Dumping data for table `challenge_questions`
--

INSERT INTO `challenge_questions` (`id`, `type`, `title`, `description`, `starter_code`, `expected_output`, `difficulty`, `points`, `created_at`) VALUES
(1, 'fill_blank', 'Complete the Loop', 'Fill in the blank to complete this for loop that prints numbers 1 to 10:\\n\\nfor (int i = 1; i <= 10; ___) {\\n    System.out.println(i);\\n}', '', '', 'expert', 30, '2025-09-27 15:46:38'),
(2, 'output', 'Predict the Output', 'What will be the output of this Python code?\\n\\nx = [1, 2, 3]\\ny = x\\ny.append(4)\\nprint(x)', '', '[1, 2, 3, 4]', 'expert', 30, '2025-09-27 15:46:38'),
(3, 'code', 'Reverse String', 'Write a function that reverses a string without using any built-in reverse methods.', 'function reverseString(str) {\\n    // Your code here\\n    return \"\";\\n}', '', 'expert', 30, '2025-09-27 15:46:38'),
(4, 'case_study', 'Database Optimization', 'A web application is experiencing slow query performance. The main table has 1 million records and queries often filter by user_id and created_date. What would be the most effective first step to optimize performance?', '', '', 'expert', 30, '2025-09-27 15:46:38'),
(5, 'fill_blank', 'JavaScript Array Method', 'Complete this JavaScript code to filter out even numbers:\\n\\nconst numbers = [1, 2, 3, 4, 5, 6];\\nconst oddNumbers = numbers.____(num => num % 2 !== 0);', '', '', 'expert', 30, '2025-09-27 15:46:38'),
(6, 'output', 'Java Inheritance', 'What will be printed?\\n\\nclass Parent {\\n    void show() { System.out.println(\"Parent\"); }\\n}\\nclass Child extends Parent {\\n    void show() { System.out.println(\"Child\"); }\\n}\\nParent p = new Child();\\np.show();', '', 'Child', 'expert', 30, '2025-09-27 15:46:38'),
(7, 'code', 'Palindrome Check', 'Write a function that checks if a string is a palindrome (reads the same forwards and backwards). Ignore case and spaces.', 'function isPalindrome(str) {\\n    // Your code here\\n    return false;\\n}', '', 'expert', 30, '2025-09-27 15:46:38'),
(8, 'case_study', 'Memory Leak Investigation', 'A Node.js application is experiencing memory leaks. The heap usage keeps growing over time, even during idle periods. What is the most likely cause and solution?', '', '', 'expert', 30, '2025-09-27 15:46:38'),
(9, 'fill_blank', 'CSS Flexbox', 'Complete this CSS to center an element both horizontally and vertically using flexbox:\\n\\n.container {\\n    display: flex;\\n    justify-content: ____;\\n    align-items: ____;\\n}', '', '', 'expert', 30, '2025-09-27 15:46:38'),
(10, 'output', 'SQL Query Result', 'Given this table:\\nUsers(id, name, age)\\n1, \"Alice\", 25\\n2, \"Bob\", 30\\n3, \"Charlie\", 25\\n4, \"Diana\", 35\\n\\nWhat will this query return?\\nSELECT COUNT(*) FROM Users WHERE age >= 25 AND age < 35;', '', '3', 'expert', 30, '2025-09-27 15:46:38'),
(11, 'code', 'Binary Search', 'Implement a binary search algorithm to find a target value in a sorted array. Return the index if found, -1 if not found.', 'function binarySearch(arr, target) {\\n    // Your code here\\n    return -1;\\n}', '', 'expert', 30, '2025-09-27 15:46:38'),
(12, 'case_study', 'API Security', 'A REST API is vulnerable to unauthorized access and data breaches. Users can access other users\' data by changing ID parameters in URLs. What is the most critical security measure to implement first?', '', '', 'expert', 30, '2025-09-27 15:46:38'),
(13, 'fill_blank', 'Python List Comprehension', 'Complete this Python list comprehension to create a list of squares for even numbers only:\\n\\nnumbers = [1, 2, 3, 4, 5, 6]\\nsquares = [x**2 for x in numbers if ____]', '', '', 'expert', 30, '2025-09-27 15:46:38'),
(14, 'output', 'C++ Pointer', 'What will be the output?\\n\\nint x = 5;\\nint *ptr = &x;\\n*ptr = 10;\\ncout << x << endl;', '', '10', 'expert', 30, '2025-09-27 15:46:38'),
(15, 'code', 'Fibonacci Sequence', 'Write a function to generate the nth Fibonacci number using dynamic programming (not recursion) for efficiency.', 'function fibonacci(n) {\\n    // Your code here\\n    return 0;\\n}', '', 'expert', 30, '2025-09-27 15:46:38'),
(16, 'case_study', 'Load Balancing', 'A web application needs to handle 100,000 concurrent users during peak hours. The current single server setup is failing. What is the most effective scaling strategy?', '', '', 'expert', 30, '2025-09-27 15:46:38'),
(17, 'fill_blank', 'React Hook', 'Complete this React hook to manage state:\\n\\nconst [count, ____] = useState(0);\\n\\nfunction increment() {\\n    ____(count + 1);\\n}', '', '', 'expert', 30, '2025-09-27 15:46:38'),
(18, 'output', 'Git Command', 'What will this Git command do?\\n\\ngit reset --hard HEAD~1', '', '', 'expert', 30, '2025-09-27 15:46:38'),
(19, 'code', 'Sort Algorithm', 'Implement a bubble sort algorithm to sort an array in ascending order.', 'function bubbleSort(arr) {\\n    // Your code here\\n    return arr;\\n}', '', 'expert', 30, '2025-09-27 15:46:38'),
(20, 'case_study', 'Microservices Migration', 'A large monolithic e-commerce application is being migrated to microservices architecture. The team is struggling with data consistency across services. What is the biggest challenge to address first?', '', '', 'expert', 30, '2025-09-27 15:46:38');

-- --------------------------------------------------------

--
-- Table structure for table `challenge_test_cases`
--
-- Creation: Jul 18, 2025 at 05:09 AM
--

CREATE TABLE `challenge_test_cases` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `input` text DEFAULT NULL,
  `expected_output` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `challenge_test_cases`:
--   `question_id`
--       `challenge_questions` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `code_challenges`
--
-- Creation: Jul 18, 2025 at 05:07 AM
--

CREATE TABLE `code_challenges` (
  `id` int(11) NOT NULL,
  `topic_id` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `starter_code` text DEFAULT NULL,
  `test_cases` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`test_cases`)),
  `difficulty` enum('beginner','intermediate','expert') NOT NULL DEFAULT 'expert',
  `points` int(11) DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `code_challenges`:
--

-- --------------------------------------------------------

--
-- Table structure for table `coding_playlist`
--
-- Creation: Sep 29, 2025 at 06:58 AM
--

CREATE TABLE `coding_playlist` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `external_url` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `play_count` int(11) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `coding_playlist`:
--

--
-- Dumping data for table `coding_playlist`
--

INSERT INTO `coding_playlist` (`id`, `title`, `artist`, `file_path`, `external_url`, `duration`, `genre`, `is_featured`, `play_count`, `display_order`, `created_at`) VALUES
(1, 'Andromeda Sunsets', 'Starjunk 95', 'audio/Andromeda_Sunsets.mp3', NULL, NULL, 'Synthwave, EDM', 1, 0, 1, '2025-10-04 00:47:51');

-- --------------------------------------------------------

--
-- Table structure for table `faq_items`
--
-- Creation: Sep 29, 2025 at 06:58 AM
--

CREATE TABLE `faq_items` (
  `id` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `answer` text NOT NULL,
  `category` enum('project','technology','team','general') DEFAULT 'general',
  `tags` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `faq_items`:
--

--
-- Dumping data for table `faq_items`
--

INSERT INTO `faq_items` (`id`, `question`, `answer`, `category`, `tags`, `is_featured`, `view_count`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'What is Code Gaming?', 'Code Gaming is an innovative educational platform that teaches programming through interactive games, quizzes, and challenges. We make learning to code fun and engaging for students of all levels.', 'project', 'about,platform,education', 1, 0, 1, '2025-09-29 07:21:28', '2025-09-29 07:21:28'),
(2, 'What technologies do you use?', 'Our platform is built using modern web technologies including PHP, MySQL, JavaScript, HTML5, CSS3, Bootstrap 5, and various libraries like Three.js and ScrollReveal.js for enhanced user experience.', 'technology', 'tech,stack,php,javascript,mysql', 1, 0, 2, '2025-09-29 07:21:28', '2025-09-29 07:21:28'),
(3, 'Who can use this platform?', 'Code Gaming is designed for students, educators, and anyone interested in learning programming. Whether you are a complete beginner or looking to enhance your coding skills, our platform adapts to your learning pace.', 'general', 'users,students,beginners,experts', 1, 0, 3, '2025-09-29 07:21:28', '2025-09-29 07:21:28'),
(4, 'How does the gamification work?', 'We use points, achievements, leaderboards, and interactive challenges to make learning programming feel like playing a game. Users earn rewards for completing tutorials, solving challenges, and participating in quizzes.', 'project', 'gamification,points,achievements', 0, 0, 4, '2025-09-29 07:21:28', '2025-09-29 07:21:28'),
(5, 'Is the platform free to use?', 'Yes! Code Gaming is completely free to use. We believe in making quality programming education accessible to everyone.', 'general', 'free,cost,pricing', 1, 0, 5, '2025-09-29 07:21:28', '2025-09-29 07:21:28'),
(6, 'What programming languages are supported?', 'Currently, we focus on web development technologies including HTML, CSS, JavaScript, Bootstrap, AJAX, and PHP. We plan to expand to other languages based on user feedback and demand.', 'technology', 'languages,html,css,javascript,bootsrap,php', 0, 0, 6, '2025-09-29 07:21:28', '2025-09-29 07:21:28');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_likes`
--
-- Creation: Sep 29, 2025 at 06:58 AM
--

CREATE TABLE `feedback_likes` (
  `id` int(11) NOT NULL,
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `liked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `feedback_likes`:
--   `feedback_id`
--       `feedback_messages` -> `id`
--   `user_id`
--       `users` -> `id`
--

--
-- Dumping data for table `feedback_likes`
--

INSERT INTO `feedback_likes` (`id`, `feedback_id`, `user_id`, `ip_address`, `liked_at`) VALUES
(1, 1, NULL, '::1', '2025-09-29 07:44:34'),
(2, 2, NULL, '::1', '2025-10-04 00:39:15');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_messages`
--
-- Creation: Jun 25, 2025 at 02:37 PM
--

CREATE TABLE `feedback_messages` (
  `id` int(11) NOT NULL,
  `sender_name` varchar(100) NOT NULL,
  `sender_email` varchar(100) NOT NULL,
  `proponent_email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `feedback_messages`:
--

--
-- Dumping data for table `feedback_messages`
--

INSERT INTO `feedback_messages` (`id`, `sender_name`, `sender_email`, `proponent_email`, `message`, `sent_at`, `likes`) VALUES
(1, 'Unknown User', 'jamesariess76@gmail.com', 'jibelza@paterostechnologicalcollege.edu.ph', 'Looks good! ^^', '2025-06-27 13:58:35', 5),
(2, 'User', 'jamesariess76@gmail.com', 'about-page-feedback', 'Very cool!', '2025-10-02 11:28:13', 0);

-- --------------------------------------------------------

--
-- Table structure for table `guest_challenge_attempts`
--
-- Creation: Jul 18, 2025 at 05:10 AM
--

CREATE TABLE `guest_challenge_attempts` (
  `id` int(11) NOT NULL,
  `guest_session_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `submitted_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `points_earned` int(11) DEFAULT 0,
  `time_taken` float DEFAULT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `guest_challenge_attempts`:
--   `guest_session_id`
--       `guest_sessions` -> `id`
--   `question_id`
--       `challenge_questions` -> `id`
--

--
-- Dumping data for table `guest_challenge_attempts`
--

INSERT INTO `guest_challenge_attempts` (`id`, `guest_session_id`, `question_id`, `submitted_answer`, `is_correct`, `points_earned`, `time_taken`, `attempted_at`) VALUES
(2, 12, 5, '[1,2,3,4]', 0, 0, 54, '2025-09-29 09:43:57'),
(3, 12, 20, 'Child', 0, 0, 77, '2025-09-29 09:44:21'),
(4, 13, 12, 'Filter', 0, 0, 49, '2025-09-29 09:47:02');

-- --------------------------------------------------------

--
-- Table structure for table `guest_quiz_attempts`
--
-- Creation: Jul 10, 2025 at 04:22 AM
--

CREATE TABLE `guest_quiz_attempts` (
  `id` int(11) NOT NULL,
  `guest_session_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer_id` int(11) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `points_earned` int(11) DEFAULT 0,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `guest_quiz_attempts`:
--   `guest_session_id`
--       `guest_sessions` -> `id`
--   `question_id`
--       `quiz_questions` -> `id`
--   `selected_answer_id`
--       `quiz_answers` -> `id`
--

--
-- Dumping data for table `guest_quiz_attempts`
--

INSERT INTO `guest_quiz_attempts` (`id`, `guest_session_id`, `question_id`, `selected_answer_id`, `is_correct`, `points_earned`, `attempted_at`) VALUES
(1, 1, 85, 313, 1, 1, '2025-07-14 06:28:58'),
(2, 1, 105, 377, 1, 1, '2025-07-14 06:29:07'),
(3, 1, 103, 372, 0, 0, '2025-07-14 06:29:15'),
(4, 1, 86, 316, 1, 1, '2025-07-14 06:29:24'),
(5, 1, 98, 356, 1, 1, '2025-07-14 06:29:33'),
(6, 1, 88, 323, 1, 1, '2025-07-14 06:29:42'),
(7, 1, 108, 391, 1, 1, '2025-07-14 06:30:01'),
(8, 1, 101, 368, 1, 1, '2025-07-14 06:30:22'),
(9, 1, 104, 376, 1, 1, '2025-07-14 06:30:32'),
(10, 1, 97, 353, 1, 1, '2025-07-14 06:30:40'),
(11, 1, 89, 325, 1, 1, '2025-07-14 06:30:49'),
(12, 1, 81, 298, 1, 1, '2025-07-14 06:31:08'),
(13, 1, 87, 321, 1, 1, '2025-07-14 06:31:22'),
(14, 1, 111, 398, 1, 1, '2025-07-14 06:31:40'),
(15, 1, 93, 340, 1, 1, '2025-07-14 06:31:49'),
(16, 1, 115, 414, 1, 1, '2025-07-14 06:31:58'),
(17, 1, 118, 425, 1, 1, '2025-07-14 06:32:14'),
(18, 1, 90, 328, 1, 1, '2025-07-14 06:32:34'),
(19, 1, 106, 382, 1, 1, '2025-07-14 06:32:46'),
(20, 1, 113, 408, 0, 0, '2025-07-14 06:33:04'),
(21, 1, 99, 360, 0, 0, '2025-07-14 06:33:28'),
(22, 1, 116, 420, 0, 0, '2025-07-14 06:33:45'),
(23, 1, 114, 411, 0, 0, '2025-07-14 06:34:02'),
(24, 1, 95, 347, 1, 1, '2025-07-14 06:34:17'),
(25, 1, 84, 311, 0, 0, '2025-07-14 06:34:44'),
(26, 1, 117, 423, 1, 1, '2025-07-14 06:34:57'),
(27, 1, 109, 394, 1, 1, '2025-07-14 06:35:06'),
(28, 1, 91, 333, 1, 1, '2025-07-14 06:35:19'),
(29, 1, 102, 369, 1, 1, '2025-07-14 06:35:30'),
(30, 1, 107, 386, 1, 1, '2025-07-14 06:35:42'),
(31, 1, 82, 301, 1, 1, '2025-07-14 06:36:00'),
(32, 1, 100, 364, 1, 1, '2025-07-14 06:36:14'),
(33, 1, 83, 307, 1, 1, '2025-07-14 06:36:30'),
(34, 1, 112, 404, 0, 0, '2025-07-14 06:36:50');

-- --------------------------------------------------------

--
-- Table structure for table `guest_sessions`
--
-- Creation: Jul 10, 2025 at 03:38 AM
--

CREATE TABLE `guest_sessions` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `nickname` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `guest_sessions`:
--

--
-- Dumping data for table `guest_sessions`
--

INSERT INTO `guest_sessions` (`id`, `session_id`, `ip_address`, `user_agent`, `nickname`, `created_at`) VALUES
(1, 'gs_00g8h8699wwb1752471914613', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Areyszxc', '2025-07-14 06:28:28'),
(2, 'cs_ljrfnzq84cl1752818938745', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Areyszxc', '2025-07-18 06:08:58'),
(3, 'cs_ljrfnzq84cl1752818938745', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Areyszxcx', '2025-07-18 06:09:06'),
(4, 'cs_ljrfnzq84cl1752818938745', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Areyszxcx', '2025-07-18 06:09:06'),
(5, 'cs_ljrfnzq84cl1752818938745', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Areyszxcx', '2025-07-18 06:09:11'),
(6, 'cs_ljrfnzq84cl1752818938745', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Areyszxcc', '2025-07-18 06:18:00'),
(7, 'cs_ljrfnzq84cl1752818938745', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Areyszxcc', '2025-07-18 06:18:00'),
(8, 'cs_ljrfnzq84cl1752818938745', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Areyszxcxc', '2025-07-18 06:36:07'),
(9, 'cs_ljrfnzq84cl1752818938745', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', 'Areyszxcxc', '2025-07-18 06:36:08'),
(10, 'cs_pgrpr4hdi0j1753002767974', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Areyszxcv', '2025-07-20 09:12:47'),
(11, 'cs_2vukp53pgdr1759138724949', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', 'Areyszxc36', '2025-09-29 09:38:44'),
(12, 'cs_2vukp53pgdr1759138724949', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', 'Areyszxc36', '2025-09-29 09:43:03'),
(13, 'cs_2vukp53pgdr1759138724949', '0.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', 'Areyszxc36', '2025-09-29 09:46:13');

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--
-- Creation: Jun 25, 2025 at 02:35 PM
--

CREATE TABLE `login_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role` varchar(20) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `login_logs`:
--   `user_id`
--       `users` -> `id`
--

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`log_id`, `user_id`, `role`, `ip_address`, `session_id`, `login_time`) VALUES
(3, 3, 'user', '192.168.1.66', NULL, '2025-06-24 21:02:00'),
(4, 2, 'user', '192.168.1.135', NULL, '2025-06-21 21:02:00'),
(5, 3, 'user', '192.168.1.133', NULL, '2025-06-27 21:02:00'),
(6, 1, 'user', '192.168.1.145', NULL, '2025-06-23 21:02:00'),
(7, 2, 'user', '192.168.1.58', NULL, '2025-06-27 21:02:00'),
(8, 2, 'user', '192.168.1.148', NULL, '2025-06-26 21:02:00'),
(9, 1, 'user', '192.168.1.75', NULL, '2025-06-21 21:02:00'),
(10, 1, 'user', '192.168.1.37', NULL, '2025-06-24 21:02:00'),
(11, 3, 'user', '192.168.1.16', NULL, '2025-06-25 21:02:00'),
(12, 2, 'user', '192.168.1.111', NULL, '2025-06-26 21:02:00'),
(13, 1, 'super_admin', '::1', '2eqnq9bquejn9ribqe5a5sc864', '2025-06-29 03:05:45'),
(14, 1, 'super_admin', '::1', '2eqnq9bquejn9ribqe5a5sc864', '2025-06-29 03:31:46'),
(15, 1, 'super_admin', '::1', '2eqnq9bquejn9ribqe5a5sc864', '2025-06-29 03:31:46'),
(16, 3, 'visitor', '::1', 'a14bush1s3q32972e1vsddoh38', '2025-06-29 03:31:46'),
(17, 1, 'player', '::1', 'a14bush1s3q32972e1vsddoh38', '2025-06-29 03:43:56'),
(18, 1, 'player', '::1', 'a14bush1s3q32972e1vsddoh38', '2025-06-29 03:43:56'),
(21, 4, 'player', '::1', 'heqefe01h8dnfic4jhpk3e79a6', '2025-06-29 03:50:06'),
(22, 4, 'player', '::1', 'heqefe01h8dnfic4jhpk3e79a6', '2025-06-29 03:50:06'),
(24, 4, 'player', '::1', '7s1moc1aaktp8joq40gas5tk49', '2025-06-29 03:55:26'),
(25, 4, 'player', '::1', '7s1moc1aaktp8joq40gas5tk49', '2025-06-29 03:55:26'),
(27, 4, 'player', '::1', 'drssss0kjtmchn84ok6qig3nep', '2025-06-29 04:09:31'),
(28, 4, 'player', '::1', 'drssss0kjtmchn84ok6qig3nep', '2025-06-29 04:09:31'),
(31, 2, 'admin', '::1', 'kgflqg3on3kpr5erc0lo3lo6ek', '2025-06-29 04:23:01'),
(32, 2, 'admin', '::1', 'kgflqg3on3kpr5erc0lo3lo6ek', '2025-06-29 04:23:01'),
(36, 4, 'password_reset', '::1', NULL, '2025-06-28 22:29:17'),
(37, 4, 'password_reset', '::1', NULL, '2025-06-28 22:29:17'),
(40, 5, 'player', '::1', 'lsmoblbaijidlapjfhq3h9d39n', '2025-06-30 12:22:22'),
(41, 5, 'player', '::1', 'lsmoblbaijidlapjfhq3h9d39n', '2025-06-30 12:22:22'),
(43, 1, 'super_admin', '::1', 'hidn8s8dakp4bjp4k40cn9tmli', '2025-06-30 12:31:33'),
(44, 1, 'super_admin', '::1', 'hidn8s8dakp4bjp4k40cn9tmli', '2025-06-30 12:31:33'),
(47, 1, 'super_admin', '::1', '8pqbgjbj21f7a15kji85ssv75v', '2025-06-30 12:45:51'),
(48, 1, 'super_admin', '::1', '8pqbgjbj21f7a15kji85ssv75v', '2025-06-30 12:45:51'),
(50, 1, 'super_admin', '::1', 'b90g2rpk8kuo42c957nu78hjdf', '2025-06-30 12:55:18'),
(51, 1, 'super_admin', '::1', 'b90g2rpk8kuo42c957nu78hjdf', '2025-06-30 12:55:18'),
(53, 5, 'user', '::1', 'd7p5tf5pnhrs7m6qhctsndfrlj', '2025-06-30 12:56:36'),
(54, 5, 'user', '::1', 'd7p5tf5pnhrs7m6qhctsndfrlj', '2025-06-30 12:56:36'),
(56, 1, 'super_admin', '::1', '2nkb8hddirm2loh5c5pj4qj7a7', '2025-06-30 13:06:14'),
(57, 1, 'super_admin', '::1', '2nkb8hddirm2loh5c5pj4qj7a7', '2025-06-30 13:06:14'),
(59, 1, 'super_admin', '::1', 's497gmc65v4h8n3i20lsqb9b52', '2025-06-30 15:34:36'),
(60, 1, 'super_admin', '::1', 's497gmc65v4h8n3i20lsqb9b52', '2025-06-30 15:34:36'),
(63, 1, 'super_admin', '::1', 'airtoh5t1kstpn74273tsspvr4', '2025-07-02 13:30:37'),
(64, 1, 'super_admin', '::1', 'airtoh5t1kstpn74273tsspvr4', '2025-07-02 13:30:37'),
(66, 4, 'user', '::1', 'm2d5smah2h9lu8os3pulfa5g17', '2025-07-02 13:45:43'),
(67, 4, 'user', '::1', 'm2d5smah2h9lu8os3pulfa5g17', '2025-07-02 13:45:43'),
(70, 4, 'admin', '::1', 'ebfevgsi5nedgrvangjpkfnf3u', '2025-07-04 13:59:45'),
(71, 4, 'admin', '::1', 'ebfevgsi5nedgrvangjpkfnf3u', '2025-07-04 13:59:45'),
(76, 5, 'user', '::1', 'diss1ob5gbke0jrrbe16g6553c', '2025-07-10 04:52:20'),
(77, 5, 'user', '::1', 'diss1ob5gbke0jrrbe16g6553c', '2025-07-10 04:52:20'),
(79, 1, 'super_admin', '::1', 'bjhilrchefmsuhd3jusm3vk741', '2025-07-10 05:11:32'),
(80, 1, 'super_admin', '::1', 'bjhilrchefmsuhd3jusm3vk741', '2025-07-10 05:11:32'),
(84, 1, 'super_admin', '::1', 'jiq98dd7j3mem6qp2628n714sp', '2025-07-14 05:16:34'),
(85, 1, 'super_admin', '::1', 'jiq98dd7j3mem6qp2628n714sp', '2025-07-14 05:16:34'),
(89, 5, 'user', '::1', 'pb2gi01974852peuqmlmqq3okf', '2025-07-16 03:25:25'),
(90, 5, 'user', '::1', 'pb2gi01974852peuqmlmqq3okf', '2025-07-16 03:25:25'),
(93, 5, 'user', '::1', '1cjou979pbo72qst221csjfkte', '2025-07-18 06:38:34'),
(94, 5, 'user', '::1', '1cjou979pbo72qst221csjfkte', '2025-07-18 06:38:34'),
(99, 1, 'super_admin', '::1', 'tfn697oaitb90g8lfhnh0nkl1s', '2025-07-24 09:16:59'),
(100, 1, 'super_admin', '::1', 'tfn697oaitb90g8lfhnh0nkl1s', '2025-07-24 09:16:59'),
(103, 1, 'super_admin', '::1', '8lkiemkdh7plo9vcg1epu1k9tu', '2025-09-12 23:51:51'),
(104, 1, 'super_admin', '::1', '8lkiemkdh7plo9vcg1epu1k9tu', '2025-09-12 23:51:51'),
(106, 2, 'admin', '::1', '27hv8lignjtjkbdc7pei8icjv9', '2025-09-12 23:52:34'),
(107, 2, 'admin', '::1', '27hv8lignjtjkbdc7pei8icjv9', '2025-09-12 23:52:34'),
(109, 4, 'admin', '::1', '67onedh6ntfl2r8d2tg7cdnnfq', '2025-09-12 23:53:22'),
(110, 4, 'admin', '::1', '67onedh6ntfl2r8d2tg7cdnnfq', '2025-09-12 23:53:22'),
(113, 4, 'user', '::1', 'ge5m3lmq6ld4s4usglm0elfn3d', '2025-09-19 09:35:31'),
(114, 4, 'user', '::1', 'ge5m3lmq6ld4s4usglm0elfn3d', '2025-09-19 09:35:31'),
(120, 9, 'user', '::1', '2s0k6lnp55ulr4c92to3o3ak5u', '2025-09-29 09:32:58'),
(121, 9, 'user', '::1', '2s0k6lnp55ulr4c92to3o3ak5u', '2025-09-29 09:32:58'),
(123, 4, 'user', '::1', '9b15bl23lkgkbpn1a3nfg5ujkm', '2025-09-29 13:40:01'),
(124, 4, 'user', '::1', '9b15bl23lkgkbpn1a3nfg5ujkm', '2025-09-29 13:40:01'),
(135, 6, 'admin', '::1', 'uj9lki3lfo5cada7vt2r7nm6l8', '2025-10-02 16:03:08'),
(136, 6, 'admin', '::1', 'uj9lki3lfo5cada7vt2r7nm6l8', '2025-10-02 16:03:08'),
(189, 4, 'admin', '::1', '6028risvuc1tp1i65af4317ph5', '2025-10-06 13:06:34'),
(190, 4, 'admin', '::1', '6028risvuc1tp1i65af4317ph5', '2025-10-06 13:06:34');

-- --------------------------------------------------------

--
-- Table structure for table `mini_game_modes`
--
-- Creation: Sep 29, 2025 at 08:16 AM
--

CREATE TABLE `mini_game_modes` (
  `id` int(11) NOT NULL,
  `mode_key` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `instructions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`instructions`)),
  `icon` varchar(50) DEFAULT 'fas fa-code',
  `difficulty_levels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`difficulty_levels`)),
  `supported_languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`supported_languages`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `mini_game_modes`:
--

--
-- Dumping data for table `mini_game_modes`
--

INSERT INTO `mini_game_modes` (`id`, `mode_key`, `name`, `description`, `instructions`, `icon`, `difficulty_levels`, `supported_languages`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'guess', 'Guess the Output', 'Test your code comprehension by predicting what code snippets will output. Perfect for understanding language syntax and behavior!', '[\"1. A code snippet will be displayed\", \"2. Analyze the code carefully\", \"3. Type what you think the output will be\", \"4. Submit your answer to see if you\'re correct\", \"5. Learn from explanations for each answer\"]', 'fas fa-search', '[\"beginner\", \"intermediate\", \"expert\"]', '[\"javascript\", \"python\", \"java\", \"cpp\", \"html\", \"css\", \"bootstrap\"]', 1, '2025-09-29 08:35:21', '2025-09-29 08:35:21'),
(2, 'typing', 'Fast Code Typing', 'Improve your coding speed and accuracy by typing code snippets as fast as possible. Great for muscle memory and syntax familiarity!', '[\"1. A code snippet will appear on screen\", \"2. Click \'Start Challenge\' to begin\", \"3. Type the code exactly as shown\", \"4. Complete before time runs out\", \"5. Achieve high WPM (Words Per Minute) scores\"]', 'fas fa-keyboard', '[\"beginner\", \"intermediate\", \"expert\"]', '[\"javascript\", \"python\", \"java\", \"cpp\", \"html\", \"css\", \"bootstrap\"]', 1, '2025-09-29 08:35:21', '2025-09-29 08:35:21');

-- --------------------------------------------------------

--
-- Table structure for table `mini_game_results`
--
-- Creation: Jun 25, 2025 at 02:39 PM
--

CREATE TABLE `mini_game_results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `game_type` varchar(50) NOT NULL,
  `score` int(11) NOT NULL,
  `time_taken` float DEFAULT NULL,
  `details` text DEFAULT NULL,
  `played_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `mini_game_results`:
--   `user_id`
--       `users` -> `id`
--

--
-- Dumping data for table `mini_game_results`
--

INSERT INTO `mini_game_results` (`id`, `user_id`, `game_type`, `score`, `time_taken`, `details`, `played_at`) VALUES
(1, 1, 'guess', 850, NULL, '{\"language\": \"javascript\", \"difficulty\": \"intermediate\", \"correct_answers\": 8, \"total_questions\": 10}', '2025-09-29 08:35:35'),
(2, 1, 'typing', 65, 45.2, '{\"language\": \"javascript\", \"difficulty\": \"beginner\", \"wpm\": 65, \"accuracy\": 95}', '2025-09-29 08:35:35'),
(3, 2, 'guess', 720, NULL, '{\"language\": \"python\", \"difficulty\": \"beginner\", \"correct_answers\": 7, \"total_questions\": 10}', '2025-09-29 08:35:35'),
(4, 2, 'typing', 58, 52.1, '{\"language\": \"python\", \"difficulty\": \"beginner\", \"wpm\": 58, \"accuracy\": 92}', '2025-09-29 08:35:35'),
(5, 4, 'guess', 0, 10.839, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 14:35:07\",\"details\":{\"correct\":false,\"user_answer\":\"47\",\"correct_answer\":\"5\",\"streak\":0,\"timestamp\":\"2025-09-29T12:35:07.540Z\"}}', '2025-09-29 12:35:07'),
(6, 4, 'guess', 0, 28.373, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 14:35:25\",\"details\":{\"correct\":false,\"user_answer\":\"hello\",\"correct_answer\":\"string\",\"streak\":0,\"timestamp\":\"2025-09-29T12:35:25.074Z\"}}', '2025-09-29 12:35:25'),
(7, 4, 'guess', 0, 36.284, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 14:35:33\",\"details\":{\"correct\":false,\"user_answer\":\"123\",\"correct_answer\":\"3\",\"streak\":0,\"timestamp\":\"2025-09-29T12:35:32.985Z\"}}', '2025-09-29 12:35:33'),
(8, 4, 'guess', 0, 49.742, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 14:35:46\",\"details\":{\"correct\":false,\"user_answer\":\"3\",\"correct_answer\":\"5\",\"streak\":0,\"timestamp\":\"2025-09-29T12:35:46.443Z\"}}', '2025-09-29 12:35:46'),
(9, 4, 'guess', 0, 57.331, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 14:35:54\",\"details\":{\"correct\":false,\"user_answer\":\"6\",\"correct_answer\":\"0\",\"streak\":0,\"timestamp\":\"2025-09-29T12:35:54.032Z\"}}', '2025-09-29 12:35:54'),
(10, 4, 'guess', 0, 63.634, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 14:36:00\",\"details\":{\"correct\":false,\"user_answer\":\"3\",\"correct_answer\":\"5\",\"streak\":0,\"timestamp\":\"2025-09-29T12:36:00.336Z\"}}', '2025-09-29 12:36:00'),
(11, 4, 'guess', 100, 71.986, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 14:36:08\",\"details\":{\"correct\":true,\"user_answer\":\"String\",\"correct_answer\":\"string\",\"streak\":1,\"timestamp\":\"2025-09-29T12:36:08.687Z\"}}', '2025-09-29 12:36:08'),
(12, 4, 'guess', 100, 4.905, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 14:46:34\",\"details\":{\"correct\":true,\"user_answer\":\"22\",\"correct_answer\":\"22\",\"streak\":1,\"timestamp\":\"2025-09-29T12:46:34.699Z\"}}', '2025-09-29 12:46:34'),
(13, 4, 'guess', 0, 57.788, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 14:47:27\",\"details\":{\"correct\":false,\"user_answer\":\"5\",\"correct_answer\":\"2\",\"streak\":0,\"timestamp\":\"2025-09-29T12:47:27.582Z\"}}', '2025-09-29 12:47:27'),
(14, 4, 'guess', 100, 65.043, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 14:47:34\",\"details\":{\"correct\":true,\"user_answer\":\"22\",\"correct_answer\":\"22\",\"streak\":1,\"timestamp\":\"2025-09-29T12:47:34.837Z\"}}', '2025-09-29 12:47:34'),
(15, 4, 'guess', 100, 116.376, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 14:48:26\",\"details\":{\"correct\":true,\"user_answer\":\"False\",\"correct_answer\":\"false\",\"streak\":2,\"timestamp\":\"2025-09-29T12:48:26.170Z\"}}', '2025-09-29 12:48:26'),
(16, 4, 'guess', 100, 122.903, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 14:48:32\",\"details\":{\"correct\":true,\"user_answer\":\"2\",\"correct_answer\":\"2\",\"streak\":3,\"timestamp\":\"2025-09-29T12:48:32.697Z\"}}', '2025-09-29 12:48:32'),
(17, 4, 'guess', 200, 53.396, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 15:27:18\",\"details\":{\"total_questions\":3,\"correct_answers\":2,\"incorrect_answers\":1,\"accuracy\":67,\"session_duration\":53.396,\"timestamp\":\"2025-09-29T13:27:18.769Z\"}}', '2025-09-29 13:27:18'),
(18, 4, 'typing', 56, 110.984, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-09-29 15:29:28\",\"details\":{\"total_questions\":2,\"correct_answers\":2,\"incorrect_answers\":0,\"accuracy\":100,\"session_duration\":110.984,\"timestamp\":\"2025-09-29T13:29:28.511Z\"}}', '2025-09-29 13:29:28'),
(19, 10, 'guess', 200, 43.757, '{\"language\":\"python\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-10-01 12:43:01\",\"details\":{\"total_questions\":4,\"correct_answers\":2,\"incorrect_answers\":2,\"accuracy\":50,\"session_duration\":43.757,\"timestamp\":\"2025-10-01T10:43:01.737Z\"}}', '2025-10-01 10:43:01'),
(20, 4, 'guess', 1100, 176.62, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-10-01 14:31:33\",\"details\":{\"total_questions\":20,\"correct_answers\":11,\"incorrect_answers\":9,\"accuracy\":55,\"session_duration\":176.62,\"timestamp\":\"2025-10-01T12:31:33.488Z\"}}', '2025-10-01 12:31:33'),
(21, 4, 'guess', 100, 116.176, '{\"language\":\"javascript\",\"difficulty\":\"intermediate\",\"timestamp\":\"2025-10-01 14:34:12\",\"details\":{\"total_questions\":9,\"correct_answers\":1,\"incorrect_answers\":8,\"accuracy\":11,\"session_duration\":116.176,\"timestamp\":\"2025-10-01T12:34:12.412Z\"}}', '2025-10-01 12:34:12'),
(22, 4, 'guess', 1100, 159.211, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-10-01 14:48:29\",\"details\":{\"total_questions\":16,\"correct_answers\":11,\"incorrect_answers\":5,\"accuracy\":69,\"session_duration\":159.211,\"timestamp\":\"2025-10-01T12:48:29.313Z\"}}', '2025-10-01 12:48:29'),
(23, 4, 'guess', 100, 15.975, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-10-01 15:02:36\",\"details\":{\"total_questions\":1,\"correct_answers\":1,\"incorrect_answers\":0,\"accuracy\":100,\"session_duration\":15.975,\"timestamp\":\"2025-10-01T13:02:36.693Z\"}}', '2025-10-01 13:02:36'),
(24, 4, 'guess', 700, 77.899, '{\"language\":\"java\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-10-01 15:06:11\",\"details\":{\"total_questions\":9,\"correct_answers\":7,\"incorrect_answers\":2,\"accuracy\":78,\"session_duration\":77.899,\"timestamp\":\"2025-10-01T13:06:11.270Z\"}}', '2025-10-01 13:06:11'),
(25, 4, 'guess', 1100, 90.288, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-10-01 15:19:00\",\"details\":{\"total_questions\":11,\"correct_answers\":11,\"incorrect_answers\":0,\"accuracy\":100,\"session_duration\":90.288,\"timestamp\":\"2025-10-01T13:19:00.166Z\"}}', '2025-10-01 13:19:00'),
(26, 4, 'guess', 200, 21.644, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-10-01 15:32:57\",\"details\":{\"total_questions\":2,\"correct_answers\":2,\"incorrect_answers\":0,\"accuracy\":100,\"session_duration\":21.644,\"timestamp\":\"2025-10-01T13:32:57.611Z\"}}', '2025-10-01 13:32:57'),
(27, 4, 'typing', 12, 68.165, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-10-01 15:34:14\",\"details\":{\"total_questions\":1,\"correct_answers\":1,\"incorrect_answers\":0,\"accuracy\":100,\"session_duration\":68.165,\"timestamp\":\"2025-10-01T13:34:14.150Z\"}}', '2025-10-01 13:34:14'),
(28, 12, 'guess', 300, 74.075, '{\"language\":\"javascript\",\"difficulty\":\"beginner\",\"timestamp\":\"2025-10-06 16:28:46\",\"details\":{\"total_questions\":7,\"correct_answers\":3,\"incorrect_answers\":4,\"accuracy\":43,\"session_duration\":74.075,\"timestamp\":\"2025-10-06T14:28:46.694Z\"}}', '2025-10-06 14:28:46');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_requests`
--
-- Creation: Jun 25, 2025 at 02:36 PM
--

CREATE TABLE `password_reset_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reset_token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `password_reset_requests`:
--   `user_id`
--       `users` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `programming_languages`
--
-- Creation: Jun 23, 2025 at 02:10 PM
--

CREATE TABLE `programming_languages` (
  `id` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon` varchar(10) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `programming_languages`:
--

--
-- Dumping data for table `programming_languages`
--

INSERT INTO `programming_languages` (`id`, `name`, `icon`, `description`, `created_at`) VALUES
('bootstrap', 'Bootstrap', '🎯', 'A powerful front-end framework for faster and responsive web development.', '2025-06-23 14:11:20'),
('cpp', 'C++', '⚡', 'A powerful programming language for system and application development.', '2025-06-23 14:11:20'),
('css', 'CSS', '🎨', 'Cascading Style Sheets - the language that styles web content.', '2025-06-23 14:11:20'),
('html', 'HTML', '🌐', 'The standard markup language for creating web pages and web applications.', '2025-06-23 14:11:20'),
('java', 'Java', '☕', 'A robust, object-oriented programming language used for enterprise applications.', '2025-06-23 14:11:20'),
('javascript', 'JavaScript', '📜', 'The programming language of the web, essential for frontend development.', '2025-06-23 14:11:20'),
('python', 'Python', '🐍', 'A versatile programming language known for its simplicity and readability.', '2025-06-23 14:11:20');

-- --------------------------------------------------------

--
-- Table structure for table `project_statistics`
--
-- Creation: Sep 29, 2025 at 06:58 AM
--

CREATE TABLE `project_statistics` (
  `id` int(11) NOT NULL,
  `stat_name` varchar(100) NOT NULL,
  `stat_value` int(11) NOT NULL,
  `stat_label` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-chart-line',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `project_statistics`:
--

--
-- Dumping data for table `project_statistics`
--

INSERT INTO `project_statistics` (`id`, `stat_name`, `stat_value`, `stat_label`, `icon`, `description`, `is_active`, `display_order`, `updated_at`) VALUES
(1, 'total_users', 500, 'Active Users', 'fas fa-users', 'Total number of registered and active users on the platform', 1, 1, '2025-09-29 07:23:31'),
(2, 'challenges_completed', 1250, 'Challenges Solved', 'fas fa-trophy', 'Total number of coding challenges completed by all users', 1, 2, '2025-09-29 07:23:31'),
(3, 'lines_of_code', 15000, 'Lines of Code', 'fas fa-code', 'Total lines of code written for the platform', 1, 3, '2025-09-29 07:23:31'),
(4, 'quiz_attempts', 3500, 'Quiz Attempts', 'fas fa-question-circle', 'Total number of quiz questions attempted by users', 1, 4, '2025-09-29 07:23:31'),
(5, 'feedback_received', 89, 'Feedback Messages', 'fas fa-comments', 'Total feedback messages received from users', 1, 5, '2025-09-29 07:23:31'),
(6, 'uptime_percentage', 99, 'Platform Uptime', 'fas fa-server', 'Platform availability and uptime percentage', 1, 6, '2025-09-29 07:23:31');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_answers`
--
-- Creation: Jun 25, 2025 at 02:37 PM
--

CREATE TABLE `quiz_answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `explanation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `quiz_answers`:
--   `question_id`
--       `quiz_questions` -> `id`
--

--
-- Dumping data for table `quiz_answers`
--

INSERT INTO `quiz_answers` (`id`, `question_id`, `answer`, `is_correct`, `explanation`, `created_at`) VALUES
(1, 1, 'HyperText Markup Language', 1, NULL, '2025-07-10 05:06:39'),
(2, 1, 'Home Tool Markup Language', 0, NULL, '2025-07-10 05:06:39'),
(3, 1, 'Hyperlinks and Text Markup Language', 0, NULL, '2025-07-10 05:06:39'),
(4, 1, 'Hyperlinking Text Management Language', 0, NULL, '2025-07-10 05:06:39'),
(5, 2, '.html', 1, NULL, '2025-07-10 05:06:39'),
(6, 2, '.htnl', 0, NULL, '2025-07-10 05:06:39'),
(7, 2, '.htmll', 0, NULL, '2025-07-10 05:06:39'),
(8, 2, '.htm', 0, NULL, '2025-07-10 05:06:39'),
(9, 3, 'True', 1, NULL, '2025-07-10 05:06:39'),
(10, 3, 'False', 0, NULL, '2025-07-10 05:06:39'),
(11, 4, 'color', 1, NULL, '2025-07-10 05:06:39'),
(12, 4, 'background-color', 0, NULL, '2025-07-10 05:06:39'),
(13, 4, 'font-color', 0, NULL, '2025-07-10 05:06:39'),
(14, 4, 'text-style', 0, NULL, '2025-07-10 05:06:39'),
(15, 5, 'let x = 5;', 1, NULL, '2025-07-10 05:06:39'),
(16, 5, 'var x == 5;', 0, NULL, '2025-07-10 05:06:39'),
(17, 5, 'int x = 5;', 0, NULL, '2025-07-10 05:06:39'),
(18, 5, 'x := 5;', 0, NULL, '2025-07-10 05:06:39'),
(19, 6, 'True', 1, NULL, '2025-07-10 05:06:39'),
(20, 6, 'False', 0, NULL, '2025-07-10 05:06:39'),
(21, 7, '<link>', 0, NULL, '2025-07-14 05:14:23'),
(22, 7, '<a>', 1, NULL, '2025-07-14 05:14:23'),
(23, 7, '<ul>', 0, NULL, '2025-07-14 05:14:23'),
(24, 7, '<div>', 0, NULL, '2025-07-14 05:14:23'),
(25, 8, '<h1>', 1, NULL, '2025-07-14 05:14:23'),
(26, 8, '<h6>', 0, NULL, '2025-07-14 05:14:23'),
(27, 8, '<head>', 0, NULL, '2025-07-14 05:14:23'),
(28, 8, '<b>', 0, NULL, '2025-07-14 05:14:23'),
(29, 9, '<pic>', 0, NULL, '2025-07-14 05:14:23'),
(30, 9, '<figure>', 0, NULL, '2025-07-14 05:14:23'),
(31, 9, '<img>', 1, NULL, '2025-07-14 05:14:23'),
(32, 9, '<src>', 0, NULL, '2025-07-14 05:14:23'),
(33, 10, '<ol>', 0, NULL, '2025-07-14 05:14:24'),
(34, 10, '<li>', 0, NULL, '2025-07-14 05:14:24'),
(35, 10, '<list>', 0, NULL, '2025-07-14 05:14:24'),
(36, 10, '<ul>', 1, NULL, '2025-07-14 05:14:24'),
(37, 11, 'Cascading Style Sheets', 1, NULL, '2025-07-14 05:14:24'),
(38, 11, 'Computer Style Sheets', 0, NULL, '2025-07-14 05:14:24'),
(39, 11, 'Creative Style System', 0, NULL, '2025-07-14 05:14:24'),
(40, 11, 'Compact Style Syntax', 0, NULL, '2025-07-14 05:14:24'),
(41, 12, 'font-color', 0, NULL, '2025-07-14 05:14:24'),
(42, 12, 'style-color', 0, NULL, '2025-07-14 05:14:24'),
(43, 12, 'color', 1, NULL, '2025-07-14 05:14:24'),
(44, 12, 'text-color', 0, NULL, '2025-07-14 05:14:24'),
(45, 13, 'background-color', 1, NULL, '2025-07-14 05:14:24'),
(46, 13, 'bgcolor', 0, NULL, '2025-07-14 05:14:24'),
(47, 13, 'color', 0, NULL, '2025-07-14 05:14:24'),
(48, 13, 'color-background', 0, NULL, '2025-07-14 05:14:24'),
(49, 14, 'header', 0, NULL, '2025-07-14 05:14:24'),
(50, 14, '#header', 0, NULL, '2025-07-14 05:14:24'),
(51, 14, '.header', 1, NULL, '2025-07-14 05:14:24'),
(52, 14, '/header', 0, NULL, '2025-07-14 05:14:24'),
(53, 15, 'Displays a dialog box with a message', 0, NULL, '2025-07-14 05:14:24'),
(54, 15, 'Writes text to the HTML page', 0, NULL, '2025-07-14 05:14:24'),
(55, 15, 'Writes a message to the browser console', 1, NULL, '2025-07-14 05:14:24'),
(56, 15, 'Saves data to a file', 0, NULL, '2025-07-14 05:14:24'),
(57, 16, '==', 1, NULL, '2025-07-14 05:14:24'),
(58, 16, '!=', 0, NULL, '2025-07-14 05:14:24'),
(59, 16, '<=', 0, NULL, '2025-07-14 05:14:24'),
(60, 16, '===', 0, NULL, '2025-07-14 05:14:24'),
(61, 17, 'int', 0, NULL, '2025-07-14 05:14:24'),
(62, 17, 'float', 0, NULL, '2025-07-14 05:14:24'),
(63, 17, 'var', 1, NULL, '2025-07-14 05:14:24'),
(64, 17, 'number', 0, NULL, '2025-07-14 05:14:24'),
(65, 18, 'onmouseover', 0, NULL, '2025-07-14 05:14:24'),
(66, 18, 'onclick', 1, NULL, '2025-07-14 05:14:24'),
(67, 18, 'onsubmit', 0, NULL, '2025-07-14 05:14:24'),
(68, 18, 'oncommence', 0, NULL, '2025-07-14 05:14:24'),
(69, 19, '4', 1, NULL, '2025-07-14 05:14:24'),
(70, 19, '\"22\" (string)', 0, NULL, '2025-07-14 05:14:24'),
(71, 19, '2', 0, NULL, '2025-07-14 05:14:24'),
(72, 19, '4', 0, NULL, '2025-07-14 05:14:24'),
(73, 20, 'True', 1, NULL, '2025-07-14 05:14:24'),
(74, 20, 'False', 0, NULL, '2025-07-14 05:14:24'),
(75, 21, 'printf()', 0, NULL, '2025-07-14 05:14:24'),
(76, 21, 'echo()', 0, NULL, '2025-07-14 05:14:24'),
(77, 21, 'print()', 1, NULL, '2025-07-14 05:14:24'),
(78, 21, 'cout', 0, NULL, '2025-07-14 05:14:24'),
(79, 22, '//', 0, NULL, '2025-07-14 05:14:24'),
(80, 22, '#', 1, NULL, '2025-07-14 05:14:24'),
(81, 22, '<!--', 0, NULL, '2025-07-14 05:14:24'),
(82, 22, '||', 0, NULL, '2025-07-14 05:14:24'),
(83, 23, 'True', 0, NULL, '2025-07-14 05:14:24'),
(84, 23, 'False', 1, NULL, '2025-07-14 05:14:24'),
(85, 24, '.py', 1, NULL, '2025-07-14 05:14:24'),
(86, 24, '.python', 0, NULL, '2025-07-14 05:14:24'),
(87, 24, '.pyt', 0, NULL, '2025-07-14 05:14:24'),
(88, 24, '.thon', 0, NULL, '2025-07-14 05:14:24'),
(89, 25, 'function', 0, NULL, '2025-07-14 05:14:24'),
(90, 25, 'def', 1, NULL, '2025-07-14 05:14:24'),
(91, 25, 'fun', 0, NULL, '2025-07-14 05:14:24'),
(92, 25, 'descript', 0, NULL, '2025-07-14 05:14:24'),
(93, 26, 'True', 1, NULL, '2025-07-14 05:14:24'),
(94, 26, 'False', 0, NULL, '2025-07-14 05:14:24'),
(95, 27, 'A JavaScript library', 0, NULL, '2025-07-14 05:14:24'),
(96, 27, 'A database management system', 0, NULL, '2025-07-14 05:14:24'),
(97, 27, 'A CSS framework for building responsive websites', 1, NULL, '2025-07-14 05:14:24'),
(98, 27, 'A version control tool', 0, NULL, '2025-07-14 05:14:24'),
(99, 28, '6', 0, NULL, '2025-07-14 05:14:24'),
(100, 28, '10', 0, NULL, '2025-07-14 05:14:24'),
(101, 28, '12', 1, NULL, '2025-07-14 05:14:24'),
(102, 28, '15', 0, NULL, '2025-07-14 05:14:24'),
(103, 29, '.row', 0, NULL, '2025-07-14 05:14:24'),
(104, 29, '.container', 1, NULL, '2025-07-14 05:14:24'),
(105, 29, '.fixed', 0, NULL, '2025-07-14 05:14:24'),
(106, 29, 'wrapper', 0, NULL, '2025-07-14 05:14:24'),
(107, 30, '.container', 0, NULL, '2025-07-14 05:14:24'),
(108, 30, '.row', 1, NULL, '2025-07-14 05:14:24'),
(109, 30, '.rows', 0, NULL, '2025-07-14 05:14:24'),
(110, 30, '.grid', 0, NULL, '2025-07-14 05:14:24'),
(111, 31, 'img-responsive', 0, NULL, '2025-07-14 05:14:24'),
(112, 31, 'img-fluid', 1, NULL, '2025-07-14 05:14:24'),
(113, 31, 'img-scalable', 0, NULL, '2025-07-14 05:14:24'),
(114, 31, 'responsive-img', 0, NULL, '2025-07-14 05:14:24'),
(115, 32, 'True', 1, NULL, '2025-07-14 05:14:24'),
(116, 32, 'False', 0, NULL, '2025-07-14 05:14:24'),
(117, 33, '#include <iostream>', 1, NULL, '2025-07-14 05:14:24'),
(118, 33, 'include iostream', 0, NULL, '2025-07-14 05:14:24'),
(119, 33, '#import <iostream>', 0, NULL, '2025-07-14 05:14:24'),
(120, 33, '#include \"iostream\"', 0, NULL, '2025-07-14 05:14:24'),
(121, 34, '>>', 0, NULL, '2025-07-14 05:14:24'),
(122, 34, '<=', 0, NULL, '2025-07-14 05:14:24'),
(123, 34, '+=', 0, NULL, '2025-07-14 05:14:24'),
(124, 34, '<<', 1, NULL, '2025-07-14 05:14:24'),
(125, 35, 'int main()', 1, NULL, '2025-07-14 05:14:24'),
(126, 35, 'void main()', 0, NULL, '2025-07-14 05:14:24'),
(127, 35, 'int Main()', 0, NULL, '2025-07-14 05:14:24'),
(128, 35, 'main()', 0, NULL, '2025-07-14 05:14:24'),
(129, 36, 'integer x;', 0, NULL, '2025-07-14 05:14:24'),
(130, 36, 'int x;', 1, NULL, '2025-07-14 05:14:24'),
(131, 36, 'num x;', 0, NULL, '2025-07-14 05:14:24'),
(132, 36, 'var x;', 0, NULL, '2025-07-14 05:14:24'),
(133, 37, 'True', 0, NULL, '2025-07-14 05:14:24'),
(134, 37, 'False', 1, NULL, '2025-07-14 05:14:24'),
(135, 38, '.javascript', 0, NULL, '2025-07-14 05:14:24'),
(136, 38, '.class', 0, NULL, '2025-07-14 05:14:24'),
(137, 38, '.jdk', 0, NULL, '2025-07-14 05:14:24'),
(138, 38, '.java', 1, NULL, '2025-07-14 05:14:24'),
(139, 39, 'class', 1, NULL, '2025-07-14 05:14:24'),
(140, 39, 'define', 0, NULL, '2025-07-14 05:14:24'),
(141, 39, 'classes', 0, NULL, '2025-07-14 05:14:24'),
(142, 39, 'object', 0, NULL, '2025-07-14 05:14:24'),
(143, 40, 'int', 0, NULL, '2025-07-14 05:14:24'),
(144, 40, 'double', 0, NULL, '2025-07-14 05:14:24'),
(145, 40, 'char', 0, NULL, '2025-07-14 05:14:24'),
(146, 40, 'String', 1, NULL, '2025-07-14 05:14:24'),
(147, 41, 'Java Document Key', 0, NULL, '2025-07-14 05:14:24'),
(148, 41, 'Java Driver Kit', 0, NULL, '2025-07-14 05:14:24'),
(149, 41, 'Java Distribution Kit', 0, NULL, '2025-07-14 05:14:24'),
(150, 41, 'Java Development Kit', 1, NULL, '2025-07-14 05:14:24'),
(151, 42, 'void main(String args)', 0, NULL, '2025-07-14 05:14:24'),
(152, 42, 'static public main(String[] args)', 0, NULL, '2025-07-14 05:14:24'),
(153, 42, 'public void main(String[] args)', 0, NULL, '2025-07-14 05:14:24'),
(154, 42, 'public static void main(String[] args)', 1, NULL, '2025-07-14 05:14:24'),
(155, 43, '<article>', 0, NULL, '2025-07-14 05:14:24'),
(156, 43, '<div>', 1, NULL, '2025-07-14 05:14:24'),
(157, 43, '<section>', 0, NULL, '2025-07-14 05:14:24'),
(158, 43, '<header>', 0, NULL, '2025-07-14 05:14:24'),
(159, 44, '<svg>', 0, NULL, '2025-07-14 05:14:24'),
(160, 44, '<canvas>', 1, NULL, '2025-07-14 05:14:24'),
(161, 44, '<graphics>', 0, NULL, '2025-07-14 05:14:24'),
(162, 44, '<image>', 0, NULL, '2025-07-14 05:14:24'),
(163, 45, 'action', 0, NULL, '2025-07-14 05:14:24'),
(164, 45, 'name', 0, NULL, '2025-07-14 05:14:24'),
(165, 45, 'method', 1, NULL, '2025-07-14 05:14:24'),
(166, 45, 'enctype', 0, NULL, '2025-07-14 05:14:24'),
(167, 46, '<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\">', 0, NULL, '2025-07-14 05:14:24'),
(168, 46, '<DOCTYPE html>', 0, NULL, '2025-07-14 05:14:24'),
(169, 46, '<!doctype HTML5>', 0, NULL, '2025-07-14 05:14:24'),
(170, 46, '<!DOCTYPE html>', 1, NULL, '2025-07-14 05:14:24'),
(171, 47, 'Defines the main content area', 0, NULL, '2025-07-14 05:14:24'),
(172, 47, 'Defines the footer of a document', 0, NULL, '2025-07-14 05:14:24'),
(173, 47, 'Defines content tangentially related to the main content (like a sidebar)', 1, NULL, '2025-07-14 05:14:24'),
(174, 47, 'Defines the header of a document', 0, NULL, '2025-07-14 05:14:24'),
(175, 48, '#menu', 1, NULL, '2025-07-14 05:14:24'),
(176, 48, '.header .nav li', 0, NULL, '2025-07-14 05:14:24'),
(177, 48, 'nav ul li', 0, NULL, '2025-07-14 05:14:24'),
(178, 48, 'body .container .item', 0, NULL, '2025-07-14 05:14:24'),
(179, 49, ':nth-child', 0, NULL, '2025-07-14 05:14:24'),
(180, 49, '::after', 1, NULL, '2025-07-14 05:14:24'),
(181, 49, ':hover', 0, NULL, '2025-07-14 05:14:24'),
(182, 49, ':focus', 0, NULL, '2025-07-14 05:14:24'),
(183, 50, 'justify-content', 0, NULL, '2025-07-14 05:14:24'),
(184, 50, 'align-content', 0, NULL, '2025-07-14 05:14:24'),
(185, 50, 'flex-direction', 0, NULL, '2025-07-14 05:14:24'),
(186, 50, 'align-items', 1, NULL, '2025-07-14 05:14:24'),
(187, 51, 'gap', 1, NULL, '2025-07-14 05:14:24'),
(188, 51, 'margin', 0, NULL, '2025-07-14 05:14:24'),
(189, 51, 'padding', 0, NULL, '2025-07-14 05:14:24'),
(190, 51, 'spacing', 0, NULL, '2025-07-14 05:14:24'),
(191, 52, 'True', 1, NULL, '2025-07-14 05:14:24'),
(192, 52, 'False', 0, NULL, '2025-07-14 05:14:24'),
(193, 53, '\"null\"', 0, NULL, '2025-07-14 05:14:24'),
(194, 53, '\"object\"', 1, NULL, '2025-07-14 05:14:24'),
(195, 53, '\"undefined\"', 0, NULL, '2025-07-14 05:14:24'),
(196, 53, '\"number\"', 0, NULL, '2025-07-14 05:14:24'),
(197, 54, 'They are identical and behave the same.', 0, NULL, '2025-07-14 05:14:24'),
(198, 54, '=== also performs type conversion.', 0, NULL, '2025-07-14 05:14:24'),
(199, 54, '== compares only value, while === compares both value and type.', 1, NULL, '2025-07-14 05:14:24'),
(200, 54, '== compares type only, === compares value.', 0, NULL, '2025-07-14 05:14:24'),
(201, 55, 'let variables cannot be redeclared in the same scope.', 1, NULL, '2025-07-14 05:14:24'),
(202, 55, 'var variables are block-scoped.', 0, NULL, '2025-07-14 05:14:24'),
(203, 55, 'const variables can be reassigned.', 0, NULL, '2025-07-14 05:14:24'),
(204, 55, 'var variables must be declared before use (they are hoisted by default).', 0, NULL, '2025-07-14 05:14:24'),
(205, 56, 'A private function inside a class.', 0, NULL, '2025-07-14 05:14:24'),
(206, 56, 'A function bundled together with its lexical environment (it retains access to variables from its defining scope).', 1, NULL, '2025-07-14 05:14:24'),
(207, 56, 'A function that prevents access to the global scope.', 0, NULL, '2025-07-14 05:14:24'),
(208, 56, 'A self-invoking anonymous function.', 0, NULL, '2025-07-14 05:14:24'),
(209, 57, 'pending', 0, NULL, '2025-07-14 05:14:24'),
(210, 57, 'fulfilled', 0, NULL, '2025-07-14 05:14:24'),
(211, 57, 'rejected', 0, NULL, '2025-07-14 05:14:24'),
(212, 57, 'cancelled', 1, NULL, '2025-07-14 05:14:24'),
(213, 58, '64', 0, NULL, '2025-07-14 05:14:24'),
(214, 58, '1000', 0, NULL, '2025-07-14 05:14:24'),
(215, 58, '512', 1, NULL, '2025-07-14 05:14:24'),
(216, 58, 'Error', 0, NULL, '2025-07-14 05:14:24'),
(217, 59, 'Flags for static or class methods.', 0, NULL, '2025-07-14 05:14:24'),
(218, 59, 'Operators for exponentiation and modulo.', 0, NULL, '2025-07-14 05:14:24'),
(219, 59, 'Variables for default and named arguments.', 0, NULL, '2025-07-14 05:14:24'),
(220, 59, 'A tuple of extra positional arguments (*args) and a dict of extra keyword arguments (**kwargs).', 1, NULL, '2025-07-14 05:14:24'),
(221, 60, 'Hello Done Error', 0, NULL, '2025-07-14 05:14:24'),
(222, 60, 'Hello Error Done', 1, NULL, '2025-07-14 05:14:24'),
(223, 60, 'Hello Hello Done', 0, NULL, '2025-07-14 05:14:24'),
(224, 60, 'Error Done', 0, NULL, '2025-07-14 05:14:24'),
(225, 61, '{x*2 for x in range(3)}', 0, NULL, '2025-07-14 05:14:24'),
(226, 61, '[x: x*2 for x in range(3)]', 0, NULL, '2025-07-14 05:14:24'),
(227, 61, '{x: x*2 for x in range(3)}', 1, NULL, '2025-07-14 05:14:24'),
(228, 61, '(x: x*2 for x in range(3))', 0, NULL, '2025-07-14 05:14:24'),
(229, 62, 'True', 1, NULL, '2025-07-14 05:14:24'),
(230, 62, 'False', 0, NULL, '2025-07-14 05:14:24'),
(231, 63, '.container', 0, NULL, '2025-07-14 05:14:24'),
(232, 63, '.container-fluid', 1, NULL, '2025-07-14 05:14:24'),
(233, 63, '.container-full', 0, NULL, '2025-07-14 05:14:24'),
(234, 63, '.container-responsive', 0, NULL, '2025-07-14 05:14:24'),
(235, 64, '768px', 1, NULL, '2025-07-14 05:14:24'),
(236, 64, '200px', 0, NULL, '2025-07-14 05:14:24'),
(237, 64, '576px', 0, NULL, '2025-07-14 05:14:24'),
(238, 64, '992px', 0, NULL, '2025-07-14 05:14:24'),
(239, 65, 'True', 0, NULL, '2025-07-14 05:14:24'),
(240, 65, 'False', 1, NULL, '2025-07-14 05:14:24'),
(241, 66, '.carousel-indication', 0, NULL, '2025-07-14 05:14:24'),
(242, 66, 'carousel-dash', 0, NULL, '2025-07-14 05:14:24'),
(243, 66, 'carousel-dots', 0, NULL, '2025-07-14 05:14:24'),
(244, 66, '.carousel-indicators', 1, NULL, '2025-07-14 05:14:24'),
(245, 67, 'Support for dropdown menus inside pagination items.', 0, NULL, '2025-07-14 05:14:24'),
(246, 67, 'Vertical pagination layout by default.', 0, NULL, '2025-07-14 05:14:24'),
(247, 67, 'CSS transitions on pagination links', 1, NULL, '2025-07-14 05:14:24'),
(248, 67, 'First/last page buttons enabled by default.', 0, NULL, '2025-07-14 05:14:24'),
(249, 68, 'delete[]', 1, NULL, '2025-07-14 05:14:24'),
(250, 68, 'delete', 0, NULL, '2025-07-14 05:14:24'),
(251, 68, 'free()', 0, NULL, '2025-07-14 05:14:24'),
(252, 68, 'deallocate', 0, NULL, '2025-07-14 05:14:24'),
(253, 69, 'True', 1, NULL, '2025-07-14 05:14:24'),
(254, 69, 'False', 0, NULL, '2025-07-14 05:14:24'),
(255, 70, 'Compile-time function binding.', 0, NULL, '2025-07-14 05:14:24'),
(256, 70, 'Enabling runtime polymorphism (allowing derived classes to override methods).', 1, NULL, '2025-07-14 05:14:24'),
(257, 70, 'A static function with no implementation.', 0, NULL, '2025-07-14 05:14:24'),
(258, 70, 'Memory management.', 0, NULL, '2025-07-14 05:14:24'),
(259, 71, 'std::vector', 0, NULL, '2025-07-14 05:14:24'),
(260, 71, 'std::stack', 0, NULL, '2025-07-14 05:14:24'),
(261, 71, 'hash_table', 1, NULL, '2025-07-14 05:14:24'),
(262, 71, 'std::map', 0, NULL, '2025-07-14 05:14:24'),
(263, 72, 'Indicates a loop will run automatically.', 0, NULL, '2025-07-14 05:14:24'),
(264, 72, 'Deduces the variable’s type from its initializer.', 1, NULL, '2025-07-14 05:14:24'),
(265, 72, 'Automatically allocates memory.', 0, NULL, '2025-07-14 05:14:24'),
(266, 72, 'Declares a variable with automatic storage duration.', 0, NULL, '2025-07-14 05:14:24'),
(267, 73, 'They must be manually deleted.', 0, NULL, '2025-07-14 05:14:24'),
(268, 73, 'They provide automatic memory management and can prevent memory leaks.', 1, NULL, '2025-07-14 05:14:24'),
(269, 73, '', 0, NULL, '2025-07-14 05:14:24'),
(270, 73, '', 0, NULL, '2025-07-14 05:14:24'),
(271, 74, '== checks reference equality, while .equals() checks value/content equality.', 1, NULL, '2025-07-14 05:14:24'),
(272, 74, '== checks only the hash code.', 0, NULL, '2025-07-14 05:14:24'),
(273, 74, '.equals() cannot be overridden.', 0, NULL, '2025-07-14 05:14:24'),
(274, 74, 'They are identical for object comparison.', 0, NULL, '2025-07-14 05:14:24'),
(275, 75, 'Interfaces can have instance (non-static) fields.', 0, NULL, '2025-07-14 05:14:24'),
(276, 75, 'A class can extend multiple interfaces.', 0, NULL, '2025-07-14 05:14:24'),
(277, 75, 'Interface methods can only be private.', 0, NULL, '2025-07-14 05:14:24'),
(278, 75, 'A class can implement multiple interfaces.', 1, NULL, '2025-07-14 05:14:24'),
(279, 76, 'run()', 0, NULL, '2025-07-14 05:14:24'),
(280, 76, 'execute()', 0, NULL, '2025-07-14 05:14:24'),
(281, 76, 'commence()', 0, NULL, '2025-07-14 05:14:24'),
(282, 76, 'start()', 1, NULL, '2025-07-14 05:14:24'),
(283, 77, 'cannot be overridden by subclasses.', 1, NULL, '2025-07-14 05:14:24'),
(284, 77, 'cannot be overloaded.', 0, NULL, '2025-07-14 05:14:24'),
(285, 77, 'cannot be inherited.', 0, NULL, '2025-07-14 05:14:24'),
(286, 77, 'cannot be static.', 0, NULL, '2025-07-14 05:14:24'),
(287, 78, 'Improved runtime performance.', 0, NULL, '2025-07-14 05:14:24'),
(288, 78, 'Smaller bytecode size.', 0, NULL, '2025-07-14 05:14:24'),
(289, 78, 'Faster development time without testing.', 0, NULL, '2025-07-14 05:14:24'),
(290, 78, 'Compile-time type safety (fewer type-casting errors).', 1, NULL, '2025-07-14 05:14:24'),
(291, 79, 'True', 1, NULL, '2025-07-14 05:14:24'),
(292, 79, 'False', 0, NULL, '2025-07-14 05:14:24'),
(293, 80, 'Conditions inside switch statements.', 0, NULL, '2025-07-14 05:14:24'),
(294, 80, 'A technique of making a function call itself.', 1, NULL, '2025-07-14 05:14:24'),
(295, 80, 'Constant variables that have the possibility to be free.', 0, NULL, '2025-07-14 05:14:24'),
(296, 80, 'For-each loops with superpowers.', 0, NULL, '2025-07-14 05:14:24'),
(297, 81, '<header>', 0, NULL, '2025-07-14 05:14:24'),
(298, 81, '<nav>', 1, NULL, '2025-07-14 05:14:24'),
(299, 81, '<section>', 0, NULL, '2025-07-14 05:14:24'),
(300, 81, '<menu>', 0, NULL, '2025-07-14 05:14:24'),
(301, 82, '<button> provides native keyboard accessibility and semantics for user agents.', 1, NULL, '2025-07-14 05:14:24'),
(302, 82, '<button> elements are automatically focusable only in old browsers.', 0, NULL, '2025-07-14 05:14:24'),
(303, 82, '<button> has built-in form styling, while <div> does not.', 0, NULL, '2025-07-14 05:14:24'),
(304, 82, '<button> cannot be styled with CSS, making it safer.', 0, NULL, '2025-07-14 05:14:24'),
(305, 83, 'To automatically validate the form control’s input.', 0, NULL, '2025-07-14 05:14:24'),
(306, 83, 'To provide an alternative submission button.', 0, NULL, '2025-07-14 05:14:24'),
(307, 83, 'To associate descriptive text with a form control for users and assistive technologies.', 1, NULL, '2025-07-14 05:14:24'),
(308, 83, 'To provide an alternative submission button.', 0, NULL, '2025-07-14 05:14:24'),
(309, 84, '<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">', 1, NULL, '2025-07-14 05:14:24'),
(310, 84, '<meta charset=\"utf-8\">', 0, NULL, '2025-07-14 05:14:24'),
(311, 84, '<meta name=\"mobile\" content=\"device-width\">', 0, NULL, '2025-07-14 05:14:24'),
(312, 84, '<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">', 0, NULL, '2025-07-14 05:14:24'),
(313, 85, 'True', 1, NULL, '2025-07-14 05:14:24'),
(314, 85, 'False', 0, NULL, '2025-07-14 05:14:24'),
(315, 86, 'title', 0, NULL, '2025-07-14 05:14:24'),
(316, 86, 'alt', 1, NULL, '2025-07-14 05:14:24'),
(317, 86, 'aria-role', 0, NULL, '2025-07-14 05:14:24'),
(318, 86, 'longdesc', 0, NULL, '2025-07-14 05:14:24'),
(319, 87, 'ul li.nav-item a (specificity 0-1-3)', 0, NULL, '2025-07-14 05:14:24'),
(320, 87, 'They have equal specificity.', 0, NULL, '2025-07-14 05:14:24'),
(321, 87, '#header .nav-item a:hover', 1, NULL, '2025-07-14 05:14:24'),
(322, 87, 'Specificity cannot be compared without more context.', 0, NULL, '2025-07-14 05:14:24'),
(323, 88, 'True', 1, NULL, '2025-07-14 05:14:24'),
(324, 88, 'False', 0, NULL, '2025-07-14 05:14:24'),
(325, 89, 'True', 1, NULL, '2025-07-14 05:14:24'),
(326, 89, 'False', 0, NULL, '2025-07-14 05:14:24'),
(327, 90, 'Flexbox (display: flex)', 0, NULL, '2025-07-14 05:14:24'),
(328, 90, 'Grid (display: grid)', 1, NULL, '2025-07-14 05:14:24'),
(329, 90, 'Block (display: block)', 0, NULL, '2025-07-14 05:14:24'),
(330, 90, 'Table (display: table)', 0, NULL, '2025-07-14 05:14:24'),
(331, 91, 'color: $main-color;', 0, NULL, '2025-07-14 05:14:24'),
(332, 91, 'color: --main-color;', 0, NULL, '2025-07-14 05:14:24'),
(333, 91, 'color: var(--main-color);', 1, NULL, '2025-07-14 05:14:24'),
(334, 91, 'color: main-color;', 0, NULL, '2025-07-14 05:14:24'),
(335, 92, 'The first child of its parent element.', 0, NULL, '2025-07-14 05:14:24'),
(336, 92, 'An element that has no child elements.', 0, NULL, '2025-07-14 05:14:24'),
(337, 92, 'An element whose parent has only one type of child.', 0, NULL, '2025-07-14 05:14:24'),
(338, 92, 'An element that is the only child of its parent.', 1, NULL, '2025-07-14 05:14:24'),
(339, 93, 'undefined', 0, NULL, '2025-07-14 05:14:24'),
(340, 93, 'number', 1, NULL, '2025-07-14 05:14:24'),
(341, 93, 'object', 0, NULL, '2025-07-14 05:14:24'),
(342, 93, 'NaN', 0, NULL, '2025-07-14 05:14:24'),
(343, 94, 'Only in regular (non-async) functions.', 0, NULL, '2025-07-14 05:14:24'),
(344, 94, 'Only in global scope.', 0, NULL, '2025-07-14 05:14:24'),
(345, 94, 'Only inside functions declared with async.', 1, NULL, '2025-07-14 05:14:24'),
(346, 94, 'In any function.', 0, NULL, '2025-07-14 05:14:24'),
(347, 95, 'True', 1, NULL, '2025-07-14 05:14:24'),
(348, 95, 'False', 0, NULL, '2025-07-14 05:14:24'),
(349, 96, 'Arrow functions define their own this context at call time.', 0, NULL, '2025-07-14 05:14:24'),
(350, 96, 'Arrow functions do not have their own this; they inherit it lexically from the surrounding scope.', 1, NULL, '2025-07-14 05:14:24'),
(351, 96, 'Arrow functions always bind this to the global object.', 0, NULL, '2025-07-14 05:14:24'),
(352, 96, 'The this value in an arrow function can be changed with call() or apply().', 0, NULL, '2025-07-14 05:14:24'),
(353, 97, 'True', 1, NULL, '2025-07-14 05:14:24'),
(354, 97, 'False', 0, NULL, '2025-07-14 05:14:24'),
(355, 98, 'True', 0, NULL, '2025-07-14 05:14:24'),
(356, 98, 'False', 1, NULL, '2025-07-14 05:14:24'),
(357, 99, 'There is no difference; it’s safe to use mutable defaults.', 0, NULL, '2025-07-14 05:14:24'),
(358, 99, 'The mutable default is shared across calls, leading to unintended side-effects.', 1, NULL, '2025-07-14 05:14:24'),
(359, 99, 'Mutable default arguments cause a syntax error.', 0, NULL, '2025-07-14 05:14:24'),
(360, 99, 'Default arguments must always be immutable by language specification.', 0, NULL, '2025-07-14 05:14:24'),
(361, 100, 'lst.copy()', 0, NULL, '2025-07-14 05:14:24'),
(362, 100, 'copy.copy(lst)', 0, NULL, '2025-07-14 05:14:24'),
(363, 100, 'lst[:]', 0, NULL, '2025-07-14 05:14:24'),
(364, 100, 'All of the above.', 1, NULL, '2025-07-14 05:14:24'),
(365, 101, 'list', 0, NULL, '2025-07-14 05:14:24'),
(366, 101, 'set', 0, NULL, '2025-07-14 05:14:24'),
(367, 101, 'dict', 0, NULL, '2025-07-14 05:14:24'),
(368, 101, 'tuple', 1, NULL, '2025-07-14 05:14:24'),
(369, 102, 'True', 1, NULL, '2025-07-14 05:14:24'),
(370, 102, 'False', 0, NULL, '2025-07-14 05:14:24'),
(371, 103, 'True', 1, NULL, '2025-07-14 05:14:24'),
(372, 103, 'False', 0, NULL, '2025-07-14 05:14:24'),
(373, 104, 'dict.copy()', 0, NULL, '2025-07-14 05:14:24'),
(374, 104, 'copy.copy(dct)', 0, NULL, '2025-07-14 05:14:24'),
(375, 104, 'dict(dct)', 0, NULL, '2025-07-14 05:14:24'),
(376, 104, 'All of the above.', 1, NULL, '2025-07-14 05:14:24'),
(377, 105, '6', 1, NULL, '2025-07-14 05:14:24'),
(378, 105, '5', 0, NULL, '2025-07-14 05:14:24'),
(379, 105, '7', 0, NULL, '2025-07-14 05:14:24'),
(380, 105, '4', 0, NULL, '2025-07-14 05:14:24'),
(381, 106, '.no-gutters', 0, NULL, '2025-07-14 05:14:24'),
(382, 106, '.g-0', 1, NULL, '2025-07-14 05:14:24'),
(383, 106, '.p-0', 0, NULL, '2025-07-14 05:14:24'),
(384, 106, '.m-0', 0, NULL, '2025-07-14 05:14:24'),
(385, 107, 'Small only (≥576px)', 0, NULL, '2025-07-14 05:14:24'),
(386, 107, 'Small and all larger (≥576px)', 1, NULL, '2025-07-14 05:14:24'),
(387, 107, 'Extra small only (<576px)', 0, NULL, '2025-07-14 05:14:24'),
(388, 107, 'All sizes (no breakpoints)', 0, NULL, '2025-07-14 05:14:24'),
(389, 108, 'Switches the carousel images to a dark theme by inverting their colors.', 0, NULL, '2025-07-14 05:14:24'),
(390, 108, 'Enables a crossfade transition effect between slides instead of the default slide effect.', 0, NULL, '2025-07-14 05:14:24'),
(391, 108, 'Applies a style variant with dark-colored controls and indicators for use on light backgrounds', 1, NULL, '2025-07-14 05:14:24'),
(392, 108, 'Adds full-screen modal behavior to the carousel.', 0, NULL, '2025-07-14 05:14:24'),
(393, 109, 'True', 0, NULL, '2025-07-14 05:14:24'),
(394, 109, 'False', 1, NULL, '2025-07-14 05:14:24'),
(395, 110, 'True', 0, NULL, '2025-07-14 05:14:24'),
(396, 110, 'False', 1, NULL, '2025-07-14 05:14:24'),
(397, 111, 'Objects are initialized to zero by default.', 0, NULL, '2025-07-14 05:14:24'),
(398, 111, 'Resources acquired in a constructor are released in the destructor, preventing leaks.', 1, NULL, '2025-07-14 05:14:24'),
(399, 111, 'Heap memory is automatically compacted.', 0, NULL, '2025-07-14 05:14:24'),
(400, 111, 'All functions are executed in reverse order.', 0, NULL, '2025-07-14 05:14:24'),
(401, 112, 'The behavior is undefined.', 1, NULL, '2025-07-14 05:14:24'),
(402, 112, 'It throws a C++ exception at runtime.', 0, NULL, '2025-07-14 05:14:24'),
(403, 112, 'It causes a compiler warning but works.', 0, NULL, '2025-07-14 05:14:24'),
(404, 112, 'It correctly deallocates the array.', 0, NULL, '2025-07-14 05:14:24'),
(405, 113, 'std::shared_ptr', 0, NULL, '2025-07-14 05:14:24'),
(406, 113, 'std::weak_ptr', 0, NULL, '2025-07-14 05:14:24'),
(407, 113, 'std::unique_ptr', 1, NULL, '2025-07-14 05:14:24'),
(408, 113, 'None of the above', 0, NULL, '2025-07-14 05:14:24'),
(409, 114, 'std::unique_ptr', 0, NULL, '2025-07-14 05:14:24'),
(410, 114, 'std::weak_ptr', 0, NULL, '2025-07-14 05:14:24'),
(411, 114, 'std::auto_ptr', 0, NULL, '2025-07-14 05:14:24'),
(412, 114, 'std::shared_ptr', 1, NULL, '2025-07-14 05:14:24'),
(413, 115, 'HashMap', 0, NULL, '2025-07-14 05:14:24'),
(414, 115, 'Hashtable', 1, NULL, '2025-07-14 05:14:24'),
(415, 115, 'TreeMap', 0, NULL, '2025-07-14 05:14:24'),
(416, 115, 'LinkedHashMap', 0, NULL, '2025-07-14 05:14:24'),
(417, 116, 'The class can’t be subclassed (no inheritance).', 1, NULL, '2025-07-14 05:14:24'),
(418, 116, 'The class methods can’t be overridden.', 0, NULL, '2025-07-14 05:14:24'),
(419, 116, 'Instances of the class are immutable.', 0, NULL, '2025-07-14 05:14:24'),
(420, 116, 'The class must be compiled last.', 0, NULL, '2025-07-14 05:14:24'),
(421, 117, 'start() invokes run() immediately without creating a new thread; run() creates a new thread.', 0, NULL, '2025-07-14 05:14:24'),
(422, 117, 'They are equivalent; both spawn a new thread.', 0, NULL, '2025-07-14 05:14:24'),
(423, 117, 'start() creates a new thread and then calls run() on it; calling run() directly executes it in the current thread.', 1, NULL, '2025-07-14 05:14:24'),
(424, 117, 'run() is only for Runnable, start() only for Thread.', 0, NULL, '2025-07-14 05:14:24'),
(425, 118, 'volatile', 1, NULL, '2025-07-14 05:14:24'),
(426, 118, 'synchronized', 0, NULL, '2025-07-14 05:14:24'),
(427, 118, 'transient', 0, NULL, '2025-07-14 05:14:24'),
(428, 118, 'static', 0, NULL, '2025-07-14 05:14:24'),
(429, 119, 'The code in the try block.', 0, NULL, '2025-07-14 05:14:24'),
(430, 119, 'The code in the catch block (if an exception occurs).', 0, NULL, '2025-07-14 05:14:24'),
(431, 119, 'The code in the finally block.', 1, NULL, '2025-07-14 05:14:24'),
(432, 119, 'The code after the try-catch block.', 0, NULL, '2025-07-14 05:14:24');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--
-- Creation: Jun 25, 2025 at 02:36 PM
--

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL,
  `topic_id` varchar(50) NOT NULL,
  `question` text NOT NULL,
  `question_type` enum('multiple_choice','true_false','code') NOT NULL,
  `difficulty` enum('beginner','intermediate','expert') NOT NULL,
  `points` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `quiz_questions`:
--

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `topic_id`, `question`, `question_type`, `difficulty`, `points`, `created_at`) VALUES
(1, 'html', 'What does HTML stand for?', 'multiple_choice', 'beginner', 1, '2025-07-10 05:06:39'),
(2, 'html', 'HTML files are saved with which extension?', 'multiple_choice', 'beginner', 1, '2025-07-10 05:06:39'),
(3, 'css', 'CSS is used for styling web pages.', 'true_false', 'beginner', 1, '2025-07-10 05:06:39'),
(4, 'css', 'Which property is used to change the text color in CSS?', 'multiple_choice', 'beginner', 1, '2025-07-10 05:06:39'),
(5, 'javascript', 'Which of the following is a correct way to declare a variable in JavaScript?', 'multiple_choice', 'intermediate', 1, '2025-07-10 05:06:39'),
(6, 'python', 'Python uses indentation to define code blocks.', 'true_false', 'intermediate', 1, '2025-07-10 05:06:39'),
(7, 'html', 'Which HTML tag is used to create a hyperlink (clickable link)?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:23'),
(8, 'html', 'Which tag defines the largest heading in HTML?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:23'),
(9, 'html', 'Which HTML element is used to display an image on a web page?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:23'),
(10, 'html', 'Which tag is used to create an unordered (bulleted) list in HTML?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(11, 'css', 'What does CSS stand for?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(12, 'css', 'Which CSS property is used to change the text color of an element?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(13, 'css', 'Which CSS property is used to change the background color of an element?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(14, 'css', 'In CSS, which selector targets elements with class header?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(15, 'javascript', 'What does the console.log() function do in JavaScript?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(16, 'javascript', 'Which operator is used for strict equality (value and type) comparison in JavaScript?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(17, 'javascript', 'Which keyword is used to declare a variable in JavaScript?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(18, 'javascript', 'In JavaScript, which event occurs when the user clicks on an HTML element?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(19, 'javascript', 'What is the output of: console.log(2 + \'2\'); in JavaScript?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(20, 'javascript', 'JavaScript is a programming language commonly used for web development.', 'true_false', 'beginner', 1, '2025-07-14 05:14:24'),
(21, 'python', 'Which function is used to print output to the console in Python?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(22, 'python', 'Which symbol is used to start a comment in Python?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(23, 'python', 'In Python, indentation is not used to define blocks of code.', 'true_false', 'beginner', 1, '2025-07-14 05:14:24'),
(24, 'python', 'What is the standard file extension for Python files?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(25, 'python', 'Which keyword is used to define a function in Python?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(26, 'python', 'Python is a case-sensitive programming language.', 'true_false', 'beginner', 1, '2025-07-14 05:14:24'),
(27, 'bootstrap', 'Which of the following best describes Bootstrap?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(28, 'bootstrap', 'How many columns are in the default Bootstrap grid system?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(29, 'bootstrap', 'Which class is used to create a centered, fixed-width container in Bootstrap?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(30, 'bootstrap', 'Which class is used to create a new row in a Bootstrap grid layout?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(31, 'bootstrap', 'Which class makes images responsive (scalable) in Bootstrap 4?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(32, 'bootstrap', 'Bootstrap is a front-end framework designed for responsive (mobile-friendly) web design.', 'true_false', 'beginner', 1, '2025-07-14 05:14:24'),
(33, 'cpp', 'What is the correct directive to include the iostream library (for input/output) in C++?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(34, 'cpp', 'Which operator is used with std::cout to output data to the console?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(35, 'cpp', 'Which of the following is the correct signature for the main function in a C++ program?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(36, 'cpp', 'Which of the following correctly declares an integer variable in C++?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(37, 'cpp', 'In C++, the cout object and the << operator are not used together to print output to the console.', 'true_false', 'beginner', 1, '2025-07-14 05:14:24'),
(38, 'java', 'What is the standard file extension for Java source files?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(39, 'java', 'What keyword is used to declare a class in Java?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(40, 'java', 'Which of the following is NOT a primitive data type in Java?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(41, 'java', 'What does JDK stand for?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(42, 'java', 'Which of the following is the correct signature of the main method in Java?', 'multiple_choice', 'beginner', 1, '2025-07-14 05:14:24'),
(43, 'html', 'Which of the following is NOT considered a semantic HTML element?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(44, 'html', 'Which HTML5 element is used to draw graphics via scripting?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(45, 'html', 'Which attribute in an HTML <form> tag specifies how form data is sent to the server?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(46, 'html', 'What is the correct doctype declaration for HTML5?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(47, 'html', 'What is the purpose of the HTML5 <aside> element?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(48, 'css', 'Which CSS selector has the highest specificity?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(49, 'css', 'Which of the following is a CSS pseudo-element (not a pseudo-class)?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(50, 'css', 'In a flex container, which CSS property aligns items along the cross-axis (vertically if flex-direction: row)?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(51, 'css', 'Which CSS property defines the spacing (gutter) between rows or columns in a Flexbox or Grid container?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(52, 'css', 'In the default CSS box model, if an element has width: 100px and padding: 10px, the total rendered width becomes 120px.', 'true_false', 'intermediate', 1, '2025-07-14 05:14:24'),
(53, 'javascript', 'What is the output of typeof null in JavaScript?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(54, 'javascript', 'Which of the following is true about the == and === operators in JavaScript?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(55, 'javascript', 'Which of the following statements about variables in JavaScript is true?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(56, 'javascript', 'What is a closure in JavaScript?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(57, 'javascript', 'Which of the following is NOT a state of a JavaScript Promise?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(58, 'python', 'What is the output of the following code? print(2**3**2)', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(59, 'python', 'In Python function definitions, what do *args and **kwargs represent?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(60, 'python', 'What is the output of the following Python code? try: print(\"Hello\") 1/0 except ZeroDivisionError: print(\"Error\") finally: print(\"Done\")', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(61, 'python', 'Which of the following constructs a dictionary in Python?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(62, 'python', 'Python tuples are immutable.', 'true_false', 'intermediate', 1, '2025-07-14 05:14:24'),
(63, 'bootstrap', 'Which Bootstrap class provides a full-width container that spans the entire viewport width?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(64, 'bootstrap', 'In Bootstrap 4 and 5, what is the minimum viewport width (in pixels) for the \'md\' (medium) breakpoint?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(65, 'bootstrap', 'Bootstrap 5 requires jQuery for its JavaScript plugins.', 'true_false', 'intermediate', 1, '2025-07-14 05:14:24'),
(66, 'bootstrap', 'In Bootstrap 5, this adds indicators for the carousel. These are the little dots at the bottom of each slide.', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(67, 'bootstrap', 'Which of the following was introduced as a new feature of Bootstrap 5’s pagination component?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(68, 'cpp', 'Which C++ operator should be used to deallocate memory allocated with new[]?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(69, 'cpp', 'In C++, references must be initialized when declared.', 'true_false', 'intermediate', 1, '2025-07-14 05:14:24'),
(70, 'cpp', 'What is a virtual function in C++ used for?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(71, 'cpp', 'Which of these is NOT part of the C++ Standard Template Library (STL)?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(72, 'cpp', 'What does the auto keyword do in C++11 and later?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(73, 'cpp', 'What is an advantage of using C++ smart pointers (like std::unique_ptr/std::shared_ptr) over raw pointers?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(74, 'java', 'In Java, what is the difference between == and .equals() when comparing objects (e.g., Strings)?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(75, 'java', 'Which statement is true about interfaces in Java?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(76, 'java', 'To properly create a new thread of execution in Java, which method should you call on a Thread object?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(77, 'java', 'Declaring a method as final in Java means the method:', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(78, 'java', 'Which is a benefit of using Java generics?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(79, 'java', 'In Java, the alternate term for class attributes is ‘fields’', 'true_false', 'intermediate', 1, '2025-07-14 05:14:24'),
(80, 'java', 'What is recursion in Java?', 'multiple_choice', 'intermediate', 1, '2025-07-14 05:14:24'),
(81, 'html', 'Which HTML5 semantic element is intended to define a section of navigation links on a page?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(82, 'html', 'Why is it recommended to use a <button> element instead of a <div> (or <span>) for clickable buttons in HTML?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(83, 'html', 'What is the primary purpose of the HTML <label> element when used with form controls?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(84, 'html', 'Which <meta> tag is commonly used to ensure proper scaling on mobile devices for responsive design?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(85, 'html', 'Using semantic HTML elements (like <header>, <main>, <article>) can improve SEO by helping search engines understand the content structure.', 'true_false', 'expert', 1, '2025-07-14 05:14:24'),
(86, 'html', 'Which attribute should be provided on every <img> element to improve accessibility?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(87, 'css', 'Consider the following two CSS selectors: #header .nav-item a:hover and ul li.nav-item a. Which selector has higher specificity?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(88, 'css', 'The universal selector * has a specificity of 0-0-0 in CSS.', 'true_false', 'expert', 1, '2025-07-14 05:14:24'),
(89, 'css', 'In CSS, an element with position: absolute; is removed from the normal document flow.', 'true_false', 'expert', 1, '2025-07-14 05:14:24'),
(90, 'css', 'Which CSS layout mode is best suited for creating a grid of rows and columns with explicit row/column definitions?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(91, 'css', 'What is the correct syntax to use a CSS custom property (variable) named --main-color in a rule?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(92, 'css', 'What does the CSS pseudo-class :only-child select?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(93, 'javascript', 'What is the output of typeof NaN in JavaScript?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(94, 'javascript', 'Where is the JavaScript await operator allowed to be used?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(95, 'javascript', 'In the JavaScript event loop, promise callbacks (microtasks) always run before setTimeout callbacks (tasks) queued with 0ms delay.', 'true_false', 'expert', 1, '2025-07-14 05:14:24'),
(96, 'javascript', 'Which statement about ‘this’ in JavaScript is correct, particularly regarding arrow functions?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(97, 'javascript', 'Variables declared with let in JavaScript have block scope (not function scope).', 'true_false', 'expert', 1, '2025-07-14 05:14:24'),
(98, 'javascript', 'typeof null does not return \'object\' in JavaScript.', 'true_false', 'expert', 1, '2025-07-14 05:14:24'),
(99, 'python', 'Why should you avoid using a mutable object (like a list or dict) as a default value for a function parameter in Python?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(100, 'python', 'Which of the following creates a shallow copy of a list lst in Python?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(101, 'python', 'Which of these Python types is immutable?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(102, 'python', 'An integer in Python is immutable (cannot be changed once created).', 'true_false', 'expert', 1, '2025-07-14 05:14:24'),
(103, 'python', 'Python’s range object generates values lazily and does not store all the elements in memory.', 'true_false', 'expert', 1, '2025-07-14 05:14:24'),
(104, 'python', 'In Python, which built-in method creates a shallow copy of a dictionary dct?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(105, 'bootstrap', 'How many default grid breakpoints (tiers) are defined in Bootstrap 5’s grid system?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(106, 'bootstrap', 'Which Bootstrap utility class removes all horizontal and vertical gutters (gaps) in a grid or row?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(107, 'bootstrap', 'If you use .col-sm-4 on an element, to which screen sizes does it apply by default?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(108, 'bootstrap', 'In Bootstrap 5, what is the purpose of the new .carousel-dark class variant?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(109, 'bootstrap', 'In Bootstrap 5, using the class .offcanvas-right will position an offcanvas on the right side of the viewport.', 'true_false', 'expert', 1, '2025-07-14 05:14:24'),
(110, 'bootstrap', 'In Bootstrap 5, you should use data-backdrop=\"static\" (with no bs- prefix) to create a static (non-dismissible) modal backdrop.', 'true_false', 'expert', 1, '2025-07-14 05:14:24'),
(111, 'cpp', 'What does the RAII (Resource Acquisition Is Initialization) idiom guarantee in C++?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(112, 'cpp', 'What is the behavior if you use delete on a pointer that was allocated with new[] (i.e., an array)?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(113, 'cpp', 'Which C++ smart pointer type enforces unique ownership of a dynamically allocated object (no shared references)?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(114, 'cpp', 'Which C++ smart pointer uses reference counting to allow multiple owners of the same object?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(115, 'java', 'Which Java Map implementation is synchronized (thread-safe) by default?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(116, 'java', 'What does the final keyword indicate when applied to a class declaration in Java?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(117, 'java', 'In Java multithreading, what is the difference between Thread.start() and Thread.run()?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(118, 'java', 'Which keyword in Java is used for obtaining a \'page cache\' for a field that ensures visibility of writes across threads?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24'),
(119, 'java', 'Which of the following is guaranteed to execute in a try-catch block regardless of exceptions?', 'multiple_choice', 'expert', 1, '2025-07-14 05:14:24');

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--
-- Creation: Sep 29, 2025 at 06:58 AM
--

CREATE TABLE `team_members` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'assets/images/background.png',
  `bio` text DEFAULT NULL,
  `fun_fact` text DEFAULT NULL,
  `mission_statement` text DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `github_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `team_members`:
--

--
-- Dumping data for table `team_members`
--

INSERT INTO `team_members` (`id`, `name`, `role`, `age`, `email`, `code`, `photo`, `bio`, `fun_fact`, `mission_statement`, `facebook_url`, `instagram_url`, `github_url`, `linkedin_url`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Santiago, James Aries G.', 'Lead Developer', 22, 'jgsantiago@paterostechnologicalcollege.edu.ph', 'CG-004', 'assets/images/Santiago.PNG', 'Passionate full-stack developer with expertise in PHP, JavaScript, and modern web technologies. Leads the technical architecture and ensures code quality across all projects.', 'Can code and meme at the same time. Loves listening to j-pop music and playing video games.', 'Lezzgoooo, the show must go on! Creating engaging frontend experiences that make learning code feel like playing a game.', 'https://www.facebook.com/Areyszxc', 'https://www.instagram.com/areys27_tiago.san/?hl=en', 'https://github.com/Areyzxc', NULL, 1, 4, '2025-10-04 00:19:58', '2025-10-04 00:19:58');

-- --------------------------------------------------------

--
-- Table structure for table `timeline_events`
--
-- Creation: Sep 29, 2025 at 06:58 AM
--

CREATE TABLE `timeline_events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-calendar',
  `category` enum('milestone','development','testing','launch','update') DEFAULT 'milestone',
  `is_featured` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `timeline_events`:
--

--
-- Dumping data for table `timeline_events`
--

INSERT INTO `timeline_events` (`id`, `title`, `description`, `event_date`, `image_url`, `icon`, `category`, `is_featured`, `display_order`, `created_at`) VALUES
(1, 'Project Conception', 'Initial brainstorming and concept development for the Code Gaming platform', '2025-05-15', NULL, 'fas fa-calendar', 'milestone', 1, 1, '2025-09-29 07:25:27'),
(2, 'Team Formation', 'Assembly of the core development team and role assignments', '2025-05-20', NULL, 'fas fa-calendar', 'milestone', 1, 2, '2025-09-29 07:25:27'),
(3, 'Technical Planning', 'Architecture design and technology stack selection', '2025-05-23', NULL, 'fas fa-calendar', 'development', 0, 3, '2025-09-29 07:25:27'),
(4, 'UI/UX Design Phase', 'Creation of wireframes, mockups, and user experience design', '2025-06-30', NULL, 'fas fa-calendar', 'development', 1, 4, '2025-09-29 07:25:27'),
(5, 'Database Design', 'Database schema creation and optimization', '2025-07-14', NULL, 'fas fa-calendar', 'development', 0, 5, '2025-09-29 07:25:27'),
(6, 'Core Development', 'Implementation of core features and functionality', '2025-06-17', NULL, 'fas fa-calendar', 'development', 1, 6, '2025-09-29 07:25:27'),
(7, 'Alpha Testing', 'Internal testing and bug fixing phase', '2025-09-15', NULL, 'fas fa-calendar', 'testing', 0, 7, '2025-09-29 07:25:27'),
(8, 'Beta Release', 'Limited beta release for user feedback', '2025-10-20', NULL, 'fas fa-calendar', 'testing', 1, 8, '2025-09-29 07:25:27'),
(9, 'Feature Enhancement', 'Addition of advanced features based on user feedback', '2025-10-27', NULL, 'fas fa-calendar', 'development', 0, 9, '2025-09-29 07:25:27'),
(10, 'Public Launch', 'Official launch of the Code Gaming platform', '2025-11-10', NULL, 'fas fa-calendar', 'launch', 1, 10, '2025-09-29 07:25:27');

-- --------------------------------------------------------

--
-- Table structure for table `tutorial_visits`
--
-- Creation: Jun 25, 2025 at 02:40 PM
--

CREATE TABLE `tutorial_visits` (
  `id` int(11) NOT NULL,
  `visitor_id` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `language_id` varchar(50) DEFAULT NULL,
  `topic_id` varchar(50) DEFAULT NULL,
  `visit_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `duration` int(11) DEFAULT 0,
  `is_completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `tutorial_visits`:
--   `user_id`
--       `users` -> `id`
--   `language_id`
--       `programming_languages` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
-- Creation: Oct 06, 2025 at 08:08 AM
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `profile_picture` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT 'Code Enthusiast',
  `social_instagram` varchar(100) DEFAULT NULL,
  `social_facebook` varchar(100) DEFAULT NULL,
  `social_twitter` varchar(100) DEFAULT NULL,
  `social_pinterest` varchar(100) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `profile_views` int(11) NOT NULL DEFAULT 0,
  `header_banner` varchar(255) DEFAULT NULL,
  `preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'User preferences in JSON format' CHECK (json_valid(`preferences`)),
  `is_banned` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_activity` datetime DEFAULT NULL,
  `last_seen` timestamp NULL DEFAULT NULL,
  `first_visit` tinyint(1) DEFAULT 1 COMMENT 'Tracks if user has seen the welcome modal on first visit',
  `welcome_dont_show` tinyint(1) DEFAULT 0 COMMENT 'User preference to not show welcome modal again'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `users`:
--

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `profile_picture`, `bio`, `location`, `title`, `social_instagram`, `social_facebook`, `social_twitter`, `social_pinterest`, `email_verified`, `profile_views`, `header_banner`, `preferences`, `is_banned`, `created_at`, `updated_at`, `last_activity`, `last_seen`, `first_visit`, `welcome_dont_show`) VALUES
(1, 'john_doee', 'john@example.com', '$2y$10$fa59uEESP0COAy8m.6tDJOCWmC88fsrFnd1cQp3WGlUFrl0lVW.U.', 'user', 'user_1_1753348122.jpg', NULL, NULL, 'Code Enthusiast', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 0, '2025-06-29 03:02:00', '2025-07-24 09:08:42', NULL, NULL, 1, 0),
(2, 'jane_smith', 'jane@example.com', '$2y$10$fa59uEESP0COAy8m.6tDJOCWmC88fsrFnd1cQp3WGlUFrl0lVW.U.', 'user', 'user_2_1753347738.jpg', NULL, NULL, 'Code Enthusiast', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 0, '2025-06-29 03:02:00', '2025-07-24 09:02:18', NULL, NULL, 1, 0),
(3, 'bob_wilson', 'bob@example.com', '$2y$10$fa59uEESP0COAy8m.6tDJOCWmC88fsrFnd1cQp3WGlUFrl0lVW.U.', 'user', 'user_3_1753348710.jpg', NULL, NULL, 'Code Enthusiast', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 0, '2025-06-29 03:02:00', '2025-07-24 09:18:30', NULL, NULL, 1, 0),
(4, 'Areyszxc', 'james@gmail.com', '$2y$10$RXvPOP4byBQOSxtTxNwAYuSkeI.8D/YFfZIHaUc7j4Ov8zpHhoSlu', 'user', 'user_4_1759413639.png', NULL, NULL, 'Code Enthusiast', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 0, '2025-06-29 03:45:59', '2025-10-02 14:00:39', NULL, '2025-10-02 10:14:07', 0, 0),
(5, 'James Aries :0', 'jamesaries@gmail.com', '$2y$10$i82HyGD7WUmdCtcg3nkmNuyQOvI8r3rIkwR0HxSoI7CCyKkZBnZhW', 'user', 'user_5_1753348358.jpg', NULL, NULL, 'Code Enthusiast', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 0, '2025-06-30 11:40:14', '2025-10-02 14:21:50', NULL, NULL, 1, 0),
(6, 'areys2003', 'aries@gmail.com', '$2y$10$JBQRXcPgYXHiy8aJLwlT3.wFrFTOncZmtF.i9pT57f5g6otYHlkgS', 'user', 'uploads/avatars/68da609e36ba49.49426548-552580287_4205266713077367_3258606755289042665_n.jpg', NULL, NULL, 'Code Enthusiast', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 0, '2025-07-16 02:08:56', '2025-09-29 10:34:18', NULL, '2025-09-29 10:34:18', 0, 0),
(7, 'Army10', 'army10@yahoo.com', '$2y$10$HZTY8zqKHrDjteerfEK0I.eJn6iplLXvJmwiRJmUbHusYNCvvWFlW', 'user', 'user_7_1753348614.jpg', NULL, NULL, 'Code Enthusiast', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 0, '2025-07-22 04:11:15', '2025-07-24 09:16:54', NULL, NULL, 1, 0),
(8, 'Amogus27', 'areys@yahoo.com', '$2y$10$M069GJDbdNGivvZ4ov0eduma3Hp7ZJlZvSdUYuwM/6FlXjQxXuxja', 'user', NULL, NULL, NULL, 'Code Enthusiast', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 0, '2025-09-12 23:54:35', '2025-09-13 00:19:20', NULL, '2025-09-13 00:19:20', 1, 0),
(9, 'JegsAries', 'areyss@gmail.com', '$2y$10$rZMDC9ovHUV/9WAaz/84luREYBOwJGaGpQoyVjEGMd5G/VWh5UZ9K', 'user', 'user_9_1759407760.png', NULL, NULL, 'Code Enthusiast', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 0, '2025-09-29 08:55:26', '2025-10-02 12:22:40', NULL, '2025-09-29 10:30:02', 0, 0),
(10, 'Imposterrr', 'jamesariess76@gmail.com', '$2y$10$EF2/ClDG416gr6UW2cWraeNrexswDYcym3JPvsuaWNUAMq/5nkDUm', 'user', 'user_10_1759418641.png', NULL, NULL, 'Code Enthusiast', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 0, '2025-10-01 10:39:48', '2025-10-02 15:24:01', NULL, '2025-10-01 14:04:38', 0, 0),
(11, 'ProfileUser', 'profileuser@gmail.com', '$2y$10$f2tlF3JrhZcGZa0PEmgtSudDhcPM.S50.skSYA.RtLaQmSGJo/QO6', 'user', 'uploads/avatars/68e3bcf4e17239.85796096-Gemini_Generated_Image_16ux6216ux6216ux.png', '', '', 'Code Enthusiast', NULL, NULL, NULL, NULL, 0, 0, 'uploads/banners/68e3be8ea22796.08129451-539173707_763225279885077_4526928741896960010_n.jpg', NULL, 0, '2025-10-06 06:44:11', '2025-10-06 13:05:18', NULL, '2025-10-06 13:05:18', 0, 0),
(12, 'Areys2990', 'areys@gmail.com', '$2y$10$SXWM9DhMN4up6WhlcaGlk.O9wrTfr6TSe9YyVjm.qO4cfPpbnh9oi', 'user', 'uploads/avatars/68e3fd855b89a7.09833422-download.gif', 'Helloooooooooooooooooooooooo', 'Manila, Peteros 27', 'Code Enthusiast', NULL, NULL, NULL, NULL, 0, 0, 'uploads/banners/68e3f1bf63f6f0.66205933-398660213_726545229493718_3999809918895038138_n.jpg', NULL, 0, '2025-10-06 13:08:03', '2025-10-06 17:33:57', NULL, '2025-10-06 14:55:49', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--
-- Creation: Jun 25, 2025 at 02:40 PM
--

CREATE TABLE `user_achievements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `achievement_id` varchar(50) NOT NULL,
  `awarded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `user_achievements`:
--   `user_id`
--       `users` -> `id`
--

--
-- Dumping data for table `user_achievements`
--

INSERT INTO `user_achievements` (`id`, `user_id`, `achievement_id`, `awarded_at`) VALUES
(1, 8, 'first_challenge', '2025-09-13 00:02:05'),
(2, 4, 'first_challenge', '2025-09-27 15:52:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_challenge_attempts`
--
-- Creation: Jul 18, 2025 at 05:10 AM
--

CREATE TABLE `user_challenge_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `submitted_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `points_earned` int(11) DEFAULT 0,
  `time_taken` float DEFAULT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `user_challenge_attempts`:
--   `user_id`
--       `users` -> `id`
--   `question_id`
--       `challenge_questions` -> `id`
--

--
-- Dumping data for table `user_challenge_attempts`
--

INSERT INTO `user_challenge_attempts` (`id`, `user_id`, `question_id`, `submitted_answer`, `is_correct`, `points_earned`, `time_taken`, `attempted_at`) VALUES
(6, 4, 20, 'Implement distributed transaction management and eventual consistency', 1, 30, 52, '2025-09-27 15:51:04'),
(7, 4, 6, 'Child', 1, 30, 93, '2025-09-27 15:51:45'),
(8, 4, 13, 'x % 2 != 1', 1, 30, 123, '2025-09-27 15:52:15'),
(9, 4, 5, 'filter', 1, 30, 39, '2025-09-27 16:16:15'),
(10, 4, 8, 'Event listeners not being removed, causing memory leaks', 1, 30, 63, '2025-09-27 16:16:38');

-- --------------------------------------------------------

--
-- Table structure for table `user_code_submissions`
--
-- Creation: Jun 25, 2025 at 02:39 PM
--

CREATE TABLE `user_code_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `challenge_id` int(11) NOT NULL,
  `code` text NOT NULL,
  `status` enum('pending','running','passed','failed') DEFAULT 'pending',
  `test_results` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`test_results`)),
  `points_earned` int(11) DEFAULT 0,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `user_code_submissions`:
--   `user_id`
--       `users` -> `id`
--   `challenge_id`
--       `code_challenges` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_mini_game_attempts`
--
-- Creation: Oct 06, 2025 at 07:09 AM
--

CREATE TABLE `user_mini_game_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mode_key` varchar(50) NOT NULL,
  `difficulty_level` varchar(20) NOT NULL,
  `language_used` varchar(50) NOT NULL,
  `score` int(11) DEFAULT 0,
  `time_spent_seconds` int(11) DEFAULT 0,
  `is_correct` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracks user attempts and performance in mini-games';

--
-- RELATIONSHIPS FOR TABLE `user_mini_game_attempts`:
--   `user_id`
--       `users` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_mini_game_preferences`
--
-- Creation: Sep 29, 2025 at 08:16 AM
--

CREATE TABLE `user_mini_game_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `show_welcome_modal` tinyint(1) DEFAULT 1,
  `preferred_language` varchar(50) DEFAULT 'javascript',
  `preferred_difficulty` varchar(20) DEFAULT 'beginner',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `user_mini_game_preferences`:
--   `user_id`
--       `users` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--
-- Creation: Jun 25, 2025 at 02:38 PM
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `topic_id` varchar(50) NOT NULL,
  `status` enum('pending','currently_reading','done_reading') DEFAULT 'pending',
  `progress` int(11) DEFAULT 0,
  `last_accessed` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `user_progress`:
--   `user_id`
--       `users` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_quiz_attempts`
--
-- Creation: Jun 25, 2025 at 02:38 PM
--

CREATE TABLE `user_quiz_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer_id` int(11) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `points_earned` int(11) DEFAULT 0,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `user_quiz_attempts`:
--   `user_id`
--       `users` -> `id`
--   `question_id`
--       `quiz_questions` -> `id`
--   `selected_answer_id`
--       `quiz_answers` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_tutorial_modes_progress`
--
-- Creation: Jun 25, 2025 at 02:38 PM
--

CREATE TABLE `user_tutorial_modes_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mode` varchar(50) NOT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `user_tutorial_modes_progress`:
--   `user_id`
--       `users` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_welcome_tracking`
--
-- Creation: Sep 27, 2025 at 02:57 PM
--

CREATE TABLE `user_welcome_tracking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `section_clicked` varchar(50) NOT NULL,
  `clicked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_clicked_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `click_count` int(11) DEFAULT 1,
  `is_admin` tinyint(1) DEFAULT 0,
  `action_type` varchar(20) DEFAULT 'click'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracks user interactions with welcome modal for personalization';

--
-- RELATIONSHIPS FOR TABLE `user_welcome_tracking`:
--   `user_id`
--       `users` -> `id`
--

--
-- Dumping data for table `user_welcome_tracking`
--

INSERT INTO `user_welcome_tracking` (`id`, `user_id`, `section_clicked`, `clicked_at`, `last_clicked_at`, `click_count`, `is_admin`, `action_type`) VALUES
(1, 4, 'home', '2025-09-27 15:12:37', '2025-09-27 15:12:37', 1, 0, 'click'),
(2, 4, 'home', '2025-09-27 15:12:38', '2025-09-27 15:12:38', 1, 0, 'click'),
(3, 4, 'home', '2025-09-27 15:12:39', '2025-09-27 15:12:39', 1, 0, 'click'),
(4, 4, 'tutorials', '2025-09-27 15:12:41', '2025-09-27 15:12:41', 1, 0, 'click'),
(5, 4, 'home', '2025-09-27 15:12:43', '2025-09-27 15:12:43', 1, 0, 'click'),
(6, 4, 'tutorials', '2025-09-27 15:12:47', '2025-09-27 15:12:47', 1, 0, 'click'),
(7, 4, 'games', '2025-09-27 15:12:50', '2025-09-27 15:12:50', 1, 0, 'click'),
(8, 4, 'announcements', '2025-09-27 15:12:55', '2025-09-27 15:12:55', 1, 0, 'click'),
(9, 4, 'profile', '2025-09-27 15:12:59', '2025-09-27 15:12:59', 1, 0, 'click'),
(10, 4, 'profile', '2025-09-27 15:13:02', '2025-09-27 15:13:02', 1, 0, 'click'),
(11, 4, 'modal_completed', '2025-09-27 15:13:11', '2025-09-27 15:13:11', 1, 0, 'completed'),
(12, 9, 'modal_completed', '2025-09-29 08:55:54', '2025-09-29 08:55:54', 1, 0, 'completed'),
(13, 6, 'profile', '2025-09-29 10:08:32', '2025-09-29 10:08:32', 1, 0, 'click'),
(14, 6, 'modal_completed', '2025-09-29 10:08:48', '2025-09-29 10:08:48', 1, 0, 'completed'),
(15, 10, 'announcements', '2025-10-01 10:40:30', '2025-10-01 10:40:30', 1, 0, 'click'),
(16, 10, 'announcements', '2025-10-01 10:40:32', '2025-10-01 10:40:32', 1, 0, 'click'),
(17, 10, 'announcements', '2025-10-01 10:40:33', '2025-10-01 10:40:33', 1, 0, 'click'),
(18, 10, 'announcements', '2025-10-01 10:40:34', '2025-10-01 10:40:34', 1, 0, 'click'),
(19, 10, 'home', '2025-10-01 10:40:44', '2025-10-01 10:40:44', 1, 0, 'click'),
(20, 10, 'home', '2025-10-01 10:40:52', '2025-10-01 10:40:52', 1, 0, 'click'),
(21, 10, 'profile', '2025-10-01 10:40:54', '2025-10-01 10:40:54', 1, 0, 'click'),
(22, 10, 'profile', '2025-10-01 10:40:55', '2025-10-01 10:40:55', 1, 0, 'click'),
(23, 10, 'modal_completed', '2025-10-01 10:40:59', '2025-10-01 10:40:59', 1, 0, 'completed'),
(24, 11, 'modal_completed', '2025-10-06 06:44:48', '2025-10-06 06:44:48', 1, 0, 'completed'),
(25, 12, 'modal_completed', '2025-10-06 13:08:29', '2025-10-06 13:08:29', 1, 0, 'completed'),
(26, 12, 'modal_completed', '2025-10-06 13:08:29', '2025-10-06 13:08:29', 1, 0, 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_logs`
--
-- Creation: Jun 25, 2025 at 02:35 PM
-- Last update: Oct 07, 2025 at 09:33 AM
--

CREATE TABLE `visitor_logs` (
  `log_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `visit_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `visitor_logs`:
--

--
-- Dumping data for table `visitor_logs`
--

INSERT INTO `visitor_logs` (`log_id`, `ip_address`, `user_agent`, `visit_time`) VALUES
(1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-27 13:01:00'),
(2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-29 03:00:27'),
(3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-29 03:31:46'),
(4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-29 03:43:56'),
(5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-29 03:45:12'),
(6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-29 03:50:06'),
(7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-29 03:55:26'),
(8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-29 04:09:31'),
(9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-29 04:11:31'),
(10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-29 04:23:01'),
(11, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-29 04:27:48'),
(12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-29 04:28:30'),
(13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-30 11:38:17'),
(14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-30 11:38:57'),
(15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-30 12:22:22'),
(16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-30 12:31:33'),
(17, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-30 12:37:25'),
(18, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-30 12:45:51'),
(19, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-30 12:55:18'),
(20, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-30 12:56:36'),
(21, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-30 13:06:14'),
(22, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-30 15:34:36'),
(23, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 11:56:08'),
(24, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 13:30:37'),
(25, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 13:45:43'),
(26, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-04 11:12:08'),
(27, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-04 13:59:45'),
(28, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-07 10:08:01'),
(29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-09 06:28:40'),
(30, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-10 02:24:28'),
(31, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-10 04:52:21'),
(32, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-10 05:11:32'),
(33, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 05:12:37'),
(34, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 05:13:13'),
(35, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 05:16:34'),
(36, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-07-16 02:05:48'),
(37, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-16 02:52:20'),
(38, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-16 03:25:25'),
(39, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-18 05:11:07'),
(40, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-18 06:38:34'),
(41, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-20 09:03:45'),
(42, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 04:09:45'),
(43, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-24 06:46:00'),
(44, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-24 09:16:59'),
(45, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 23:41:14'),
(46, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 23:51:51'),
(47, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 23:52:34'),
(48, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 23:53:22'),
(49, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-19 08:32:18'),
(50, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-19 09:35:31'),
(51, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-26 14:46:12'),
(52, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-27 15:11:15'),
(53, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-29 07:10:27'),
(54, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-29 07:11:24'),
(55, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-29 09:32:58'),
(56, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-29 09:51:42'),
(57, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-29 10:01:15'),
(58, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-29 13:40:01'),
(59, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-29 13:40:07'),
(60, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 10:17:20'),
(61, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 10:20:23'),
(62, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 10:23:28'),
(63, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 10:23:54'),
(64, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 10:25:50'),
(65, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 10:28:20'),
(66, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-01 12:10:35'),
(67, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-02 10:14:56'),
(68, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-02 10:15:49'),
(69, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-02 12:08:27'),
(70, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-02 16:03:08'),
(71, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-02 16:05:01'),
(72, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-02 16:07:25'),
(73, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-02 16:10:29'),
(74, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-02 16:11:28'),
(75, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-02 16:13:24'),
(76, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-02 16:19:04'),
(77, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 09:31:57'),
(78, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:03:30'),
(79, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:14:47'),
(80, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:19:48'),
(81, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:19:57'),
(82, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:23:00'),
(83, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:29:45'),
(84, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:32:22'),
(85, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:33:58'),
(86, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:37:29'),
(87, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:42:05'),
(88, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:43:03'),
(89, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:44:23'),
(90, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:48:47'),
(91, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:52:10'),
(92, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:52:23'),
(93, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:52:34'),
(94, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 10:55:10'),
(95, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 11:09:48'),
(96, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 11:11:45'),
(97, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 11:13:48'),
(98, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 11:13:55'),
(99, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 11:21:39'),
(100, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 11:24:02'),
(101, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 11:30:18'),
(102, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-03 12:31:56'),
(103, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 00:09:27'),
(104, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:15:46'),
(105, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:17:18'),
(106, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:24:02'),
(107, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:25:51'),
(108, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:27:53'),
(109, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:29:22'),
(110, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:32:00'),
(111, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:32:38'),
(112, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:34:18'),
(113, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:37:41'),
(114, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:38:46'),
(115, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:48:28'),
(116, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:50:27'),
(117, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:55:16'),
(118, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 01:57:36'),
(119, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:00:24'),
(120, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:02:14'),
(121, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:02:46'),
(122, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:04:37'),
(123, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:06:49'),
(124, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:08:53'),
(125, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:12:04'),
(126, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:14:12'),
(127, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:17:23'),
(128, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:19:03'),
(129, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:20:23'),
(130, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:21:58'),
(131, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:23:32'),
(132, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:24:06'),
(133, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:38:32'),
(134, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:39:53'),
(135, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:41:19'),
(136, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:43:25'),
(137, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:45:27'),
(138, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:47:26'),
(139, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 02:48:07'),
(140, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:15:07'),
(141, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:15:24'),
(142, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:15:52'),
(143, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:16:25'),
(144, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:16:57'),
(145, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 03:17:52'),
(146, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 14:32:55'),
(147, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 14:38:35'),
(148, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 14:41:18'),
(149, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 14:41:26'),
(150, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 14:42:10'),
(151, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 14:43:19'),
(152, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 14:44:19'),
(153, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:11:32'),
(154, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:14:55'),
(155, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:19:47'),
(156, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:23:16'),
(157, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:28:41'),
(158, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:29:30'),
(159, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:30:12'),
(160, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:30:41'),
(161, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:31:09'),
(162, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:31:44'),
(163, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:32:28'),
(164, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:33:10'),
(165, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:33:48'),
(166, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:36:23'),
(167, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:40:00'),
(168, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:45:07'),
(169, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:49:48'),
(170, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:51:20'),
(171, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 15:53:59'),
(172, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:02:19'),
(173, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:10:02'),
(174, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:15:22'),
(175, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:20:51'),
(176, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:21:10'),
(177, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:25:00'),
(178, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:30:11'),
(179, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:31:52'),
(180, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:34:16'),
(181, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:40:11'),
(182, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:43:00'),
(183, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:44:57'),
(184, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:46:07'),
(185, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:46:57'),
(186, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:47:19'),
(187, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:54:33'),
(188, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 16:57:39'),
(189, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 17:11:29'),
(190, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 17:21:08'),
(191, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-04 17:31:48'),
(192, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-06 06:42:46'),
(193, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-06 13:05:24'),
(194, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-06 13:06:35'),
(195, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-07 07:50:20'),
(196, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-07 07:51:05'),
(197, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-07 07:52:21'),
(198, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-07 08:55:26'),
(199, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-07 09:33:06');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_stats`
--
-- Creation: Oct 02, 2025 at 10:58 AM
-- Last update: Oct 07, 2025 at 01:04 PM
--

CREATE TABLE `visitor_stats` (
  `id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `total_visits` int(11) NOT NULL DEFAULT 0,
  `unique_visits` int(11) NOT NULL DEFAULT 0,
  `page_views` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `visitor_stats`:
--

--
-- Dumping data for table `visitor_stats`
--

INSERT INTO `visitor_stats` (`id`, `visit_date`, `total_visits`, `unique_visits`, `page_views`) VALUES
(1, '2025-10-02', 7, 0, 7),
(2, '2025-10-03', 47, 3, 47),
(3, '2025-10-04', 72, 2, 72),
(4, '2025-10-06', 99, 5, 99),
(5, '2025-10-07', 13, 3, 13);

-- --------------------------------------------------------

--
-- Table structure for table `visitor_tracking`
--
-- Creation: Oct 02, 2025 at 11:57 AM
-- Last update: Oct 07, 2025 at 01:04 PM
--

CREATE TABLE `visitor_tracking` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `page_visited` varchar(255) NOT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `visit_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_unique` tinyint(1) DEFAULT 0,
  `session_id` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `is_bot` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `visitor_tracking`:
--

--
-- Dumping data for table `visitor_tracking`
--

INSERT INTO `visitor_tracking` (`id`, `ip_address`, `user_agent`, `page_visited`, `referrer`, `visit_time`, `is_unique`, `session_id`, `country`, `device_type`, `browser`, `os`, `is_bot`) VALUES
(1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', '', '2025-10-02 12:08:08', 1, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/anchor.php', '2025-10-02 16:03:46', 1, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/anchor.php', '2025-10-02 16:05:01', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/announcements.php', '2025-10-02 16:05:17', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/tutorial.php', '2025-10-02 16:05:22', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/announcements.php', '2025-10-02 16:05:30', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/challenges.php', '2025-10-02 16:06:01', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/terms.php', 'http://localhost/codegaming/privacy.php', '2025-10-02 16:06:39', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(9, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/terms.php', 'http://localhost/codegaming/privacy.php', '2025-10-02 16:07:07', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(10, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', '', '2025-10-03 09:22:59', 1, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(11, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/tutorial.php', '2025-10-03 09:31:57', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(12, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/home_page.php', '2025-10-03 09:32:18', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-03 09:34:13', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-03 09:35:40', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-03 09:38:54', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-03 09:39:44', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(17, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-03 09:44:50', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(18, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-03 09:46:43', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(19, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-03 09:48:35', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(20, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-03 09:49:41', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(21, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-03 09:51:13', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(22, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-03 09:54:38', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(23, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/quiz.php', '2025-10-03 10:03:30', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(24, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/quiz.php', '2025-10-03 10:14:47', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(25, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/quiz.php', '2025-10-03 10:19:48', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(26, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/quiz.php', '2025-10-03 10:19:57', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(27, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/home_page.php', '2025-10-03 10:20:39', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(28, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/challenges.php', '2025-10-03 10:23:00', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/challenges.php', '2025-10-03 10:29:45', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(30, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/challenges.php', '2025-10-03 10:32:22', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(31, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/challenges.php', '2025-10-03 10:33:58', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(32, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 10:37:29', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(33, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 10:42:05', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(34, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 10:43:03', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(35, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 10:44:23', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(36, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 10:48:47', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(37, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 10:52:10', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(38, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 10:52:23', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(39, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 10:52:34', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(40, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 10:55:10', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(41, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 11:09:48', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(42, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 11:11:45', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(43, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 11:13:48', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(44, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 11:13:55', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(45, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 11:21:39', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(46, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 11:24:02', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(47, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php?id=2', '2025-10-03 11:30:18', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(48, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/announcements.php', '2025-10-03 11:32:05', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(49, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/tutorial.php', '2025-10-03 11:32:14', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(50, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-03 11:32:19', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(51, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/announcements.php', '2025-10-03 12:23:47', 1, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(52, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/tutorial.php', '2025-10-03 12:31:41', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(53, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-03 12:36:12', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(54, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-03 12:36:37', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(55, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-03 13:37:34', 1, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(56, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-03 13:38:07', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(57, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:02:21', 1, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(58, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:04:28', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(59, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:20:09', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(60, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:21:28', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(61, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:23:01', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(62, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:24:33', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(63, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:24:41', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(64, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:25:43', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(65, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:26:19', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(66, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:26:57', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(67, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:27:25', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(68, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:28:31', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(69, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:29:19', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(70, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:30:00', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(71, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:30:52', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(72, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:33:08', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(73, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:38:28', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(74, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:42:26', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(75, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:47:57', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(76, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 00:58:34', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(77, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 01:02:41', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(78, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 14:38:35', 1, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(79, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 14:41:18', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(80, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 14:41:26', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(81, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 14:42:10', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(82, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 14:43:19', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(83, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/home_page.php', '2025-10-04 14:43:25', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(84, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/anchor.php', '2025-10-04 14:44:19', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(85, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/home_page.php', '2025-10-04 14:44:27', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(86, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 14:48:24', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(87, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:09:23', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(88, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:11:32', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(89, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:14:55', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(90, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:19:47', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(91, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:23:16', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(92, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:28:40', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(93, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:29:30', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(94, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:30:12', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(95, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:30:41', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(96, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:31:07', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(97, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:31:44', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(98, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:32:28', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(99, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:33:10', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(100, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:33:48', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(101, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:36:23', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(102, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:40:00', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(103, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:45:07', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(104, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:49:48', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(105, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:51:20', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(106, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 15:53:59', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(107, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 16:02:19', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(108, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 16:10:02', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(109, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 16:15:22', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(110, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 16:20:51', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(111, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-04 16:21:10', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(112, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/home_page.php', '2025-10-04 16:24:40', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(113, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/quiz.php', '2025-10-04 16:24:43', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(114, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 16:25:00', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(115, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 16:30:11', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(116, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 16:31:52', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(117, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 16:34:16', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(118, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 16:40:11', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(119, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 16:43:00', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(120, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 16:44:57', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(121, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 16:46:07', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(122, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 16:46:57', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(123, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 16:47:19', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(124, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 16:54:33', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(125, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 16:57:39', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(126, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 17:11:29', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(127, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 17:21:08', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(128, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-04 17:31:48', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(129, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-06 06:42:46', 1, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(130, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php?login=success', 'http://localhost/codegaming/home_page.php', '2025-10-06 06:44:27', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(131, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 07:00:38', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(132, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 07:04:59', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(133, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 07:11:13', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(134, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 07:14:47', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(135, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 07:18:04', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(136, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/profile.php', '2025-10-06 12:07:31', 1, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(137, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/announcements.php', '2025-10-06 12:07:33', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(138, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-06 12:07:34', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(139, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 12:08:23', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(140, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/home_page.php', '2025-10-06 12:08:50', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(141, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 12:08:52', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(142, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/about.php', 'http://localhost/codegaming/home_page.php', '2025-10-06 12:09:08', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(143, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php?login=success', 'http://localhost/codegaming/anchor.php', '2025-10-06 13:08:22', 1, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(144, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/anchor.php', '2025-10-06 13:08:34', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(145, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/profile.php', '2025-10-06 13:22:46', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(146, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 13:22:56', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(147, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/profile.php', '2025-10-06 13:29:51', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(148, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-06 13:29:56', 0, '', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(149, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/mini-game.php', '2025-10-06 13:30:17', 0, '', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(150, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-06 13:30:33', 0, '', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(151, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/home_page.php', '2025-10-06 13:30:40', 0, '', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(152, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/home_page.php', '2025-10-06 13:41:57', 0, '', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(153, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/profile.php', '2025-10-06 14:26:56', 1, '', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(154, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/announcements.php', '2025-10-06 14:27:22', 0, '', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(155, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/tutorial.php', '2025-10-06 14:27:26', 0, '', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(156, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/announcements.php', '2025-10-06 15:52:34', 1, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(157, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/tutorial.php', '2025-10-06 15:52:36', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(158, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-06 15:52:38', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(159, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 15:52:59', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(160, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 15:58:17', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(161, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 15:59:29', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(162, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/announcements.php', '2025-10-06 15:59:35', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(163, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/tutorial.php', '2025-10-06 15:59:37', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(164, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/mini-game.php', '2025-10-06 15:59:40', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(165, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/profile.php', '2025-10-06 15:59:56', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(166, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/challenges.php', '2025-10-06 16:00:04', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(167, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/challenges.php', '2025-10-06 16:03:31', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(168, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/quiz.php', '2025-10-06 16:03:42', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(169, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/challenges.php', '2025-10-06 16:03:50', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(170, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/challenges.php', '2025-10-06 16:03:58', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(171, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/challenges.php', '2025-10-06 16:05:45', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(172, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/challenges.php', '2025-10-06 16:06:01', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(173, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-06 16:06:52', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0);
INSERT INTO `visitor_tracking` (`id`, `ip_address`, `user_agent`, `page_visited`, `referrer`, `visit_time`, `is_unique`, `session_id`, `country`, `device_type`, `browser`, `os`, `is_bot`) VALUES
(174, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/home_page.php', '2025-10-06 16:07:27', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(175, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/home_page.php', '2025-10-06 16:09:10', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(176, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/quiz.php', '2025-10-06 16:09:31', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(177, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-06 16:10:08', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(178, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/mini-game.php', '2025-10-06 16:17:48', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(179, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/home_page.php', '2025-10-06 16:18:07', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(180, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:18:31', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(181, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:25:40', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(182, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:31:41', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(183, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:33:06', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(184, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:38:33', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(185, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:40:46', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(186, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:42:51', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(187, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:43:48', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(188, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:45:29', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(189, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:48:25', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(190, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:50:46', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(191, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:52:53', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(192, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:53:50', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(193, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:57:14', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(194, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 16:58:41', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(195, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:00:56', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(196, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:04:15', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(197, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:04:19', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(198, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:04:27', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(199, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:05:02', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(200, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:06:43', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(201, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:06:56', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(202, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:07:28', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(203, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:08:52', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(204, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:10:41', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(205, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:16:02', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(206, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:16:55', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(207, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:19:22', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(208, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:20:20', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(209, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:20:40', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(210, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:22:08', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(211, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:23:41', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(212, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:24:07', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(213, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:25:25', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(214, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:29:15', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(215, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:30:42', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(216, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:32:44', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(217, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:33:28', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(218, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:34:00', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(219, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:36:59', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(220, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:39:20', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(221, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:40:39', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(222, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:42:01', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(223, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:43:55', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(224, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:44:08', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(225, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:46:26', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(226, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/profile.php', '2025-10-06 17:46:39', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(227, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Mobile Safari/537.36', '/codegaming/quiz.php', 'http://localhost/codegaming/home_page.php', '2025-10-06 17:46:51', 0, 'b9a1fv8d68qq1iqvfksiklgcmq', 'Local', 'Mobile', 'Google Chrome', 'Linux', 0),
(228, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', '', '2025-10-07 07:50:14', 1, 'u541o8unvas7lbhj334o2p5i46', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(229, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-07 07:50:20', 0, 'u541o8unvas7lbhj334o2p5i46', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(230, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/announcements.php', '2025-10-07 07:51:03', 0, 'u541o8unvas7lbhj334o2p5i46', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(231, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/home_page.php', '2025-10-07 07:51:41', 0, 'u541o8unvas7lbhj334o2p5i46', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(232, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/tutorial.php', '2025-10-07 07:51:52', 0, 'u541o8unvas7lbhj334o2p5i46', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(233, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/mini-game.php', 'http://localhost/codegaming/challenges.php', '2025-10-07 07:52:12', 0, 'u541o8unvas7lbhj334o2p5i46', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(234, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/home_page.php', 'http://localhost/codegaming/anchor.php', '2025-10-07 09:33:06', 1, 'u541o8unvas7lbhj334o2p5i46', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(235, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/home_page.php', '2025-10-07 09:33:08', 0, 'u541o8unvas7lbhj334o2p5i46', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(236, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/home_page.php', '2025-10-07 09:37:20', 0, 'u541o8unvas7lbhj334o2p5i46', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(237, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/home_page.php', '2025-10-07 09:48:39', 0, 'u541o8unvas7lbhj334o2p5i46', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(238, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/home_page.php', '2025-10-07 09:49:10', 0, 'u541o8unvas7lbhj334o2p5i46', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(239, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/home_page.php', '2025-10-07 09:54:34', 0, 'u541o8unvas7lbhj334o2p5i46', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0),
(240, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '/codegaming/tutorial.php', 'http://localhost/codegaming/home_page.php', '2025-10-07 13:04:54', 1, 'p4dnghigmhui3alg4pt37ldumj', 'Local', 'Desktop', 'Google Chrome', 'Windows 10', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_actions_log`
--
ALTER TABLE `admin_actions_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_admin_users_first_visit` (`first_visit`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `challenge_answers`
--
ALTER TABLE `challenge_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `challenge_leaderboard`
--
ALTER TABLE `challenge_leaderboard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `guest_session_id` (`guest_session_id`),
  ADD KEY `idx_score` (`total_score`),
  ADD KEY `idx_completed` (`completed_at`);

--
-- Indexes for table `challenge_questions`
--
ALTER TABLE `challenge_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `challenge_test_cases`
--
ALTER TABLE `challenge_test_cases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `code_challenges`
--
ALTER TABLE `code_challenges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coding_playlist`
--
ALTER TABLE `coding_playlist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq_items`
--
ALTER TABLE `faq_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback_likes`
--
ALTER TABLE `feedback_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_feedback_like` (`feedback_id`,`ip_address`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `feedback_messages`
--
ALTER TABLE `feedback_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guest_challenge_attempts`
--
ALTER TABLE `guest_challenge_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guest_session_id` (`guest_session_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `guest_quiz_attempts`
--
ALTER TABLE `guest_quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guest_session_id` (`guest_session_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `selected_answer_id` (`selected_answer_id`);

--
-- Indexes for table `guest_sessions`
--
ALTER TABLE `guest_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mini_game_modes`
--
ALTER TABLE `mini_game_modes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mode_key` (`mode_key`),
  ADD KEY `idx_mini_game_modes_active` (`is_active`);

--
-- Indexes for table `mini_game_results`
--
ALTER TABLE `mini_game_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD UNIQUE KEY `reset_token` (`reset_token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`reset_token`);

--
-- Indexes for table `programming_languages`
--
ALTER TABLE `programming_languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_statistics`
--
ALTER TABLE `project_statistics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stat_name` (`stat_name`);

--
-- Indexes for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `timeline_events`
--
ALTER TABLE `timeline_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tutorial_visits`
--
ALTER TABLE `tutorial_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `language_id` (`language_id`),
  ADD KEY `idx_visitor` (`visitor_id`),
  ADD KEY `idx_visit_time` (`visit_time`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_first_visit` (`first_visit`),
  ADD KEY `idx_user_activity` (`last_activity`),
  ADD KEY `idx_user_views` (`profile_views`);

--
-- Indexes for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_achievement` (`user_id`,`achievement_id`);

--
-- Indexes for table `user_challenge_attempts`
--
ALTER TABLE `user_challenge_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `user_code_submissions`
--
ALTER TABLE `user_code_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `challenge_id` (`challenge_id`);

--
-- Indexes for table `user_mini_game_attempts`
--
ALTER TABLE `user_mini_game_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_mini_game_attempts_user` (`user_id`),
  ADD KEY `idx_user_mini_game_attempts_mode` (`mode_key`),
  ADD KEY `idx_user_mini_game_attempts_created` (`created_at`);

--
-- Indexes for table `user_mini_game_preferences`
--
ALTER TABLE `user_mini_game_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_preferences_user_id` (`user_id`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_topic` (`user_id`,`topic_id`);

--
-- Indexes for table `user_quiz_attempts`
--
ALTER TABLE `user_quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `selected_answer_id` (`selected_answer_id`);

--
-- Indexes for table `user_tutorial_modes_progress`
--
ALTER TABLE `user_tutorial_modes_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_mode` (`user_id`,`mode`);

--
-- Indexes for table `user_welcome_tracking`
--
ALTER TABLE `user_welcome_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_section` (`user_id`,`section_clicked`),
  ADD KEY `idx_clicked_at` (`clicked_at`);

--
-- Indexes for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_visit_time` (`visit_time`);

--
-- Indexes for table `visitor_stats`
--
ALTER TABLE `visitor_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `visit_date` (`visit_date`);

--
-- Indexes for table `visitor_tracking`
--
ALTER TABLE `visitor_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_address` (`ip_address`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `visit_time` (`visit_time`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_actions_log`
--
ALTER TABLE `admin_actions_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `challenge_answers`
--
ALTER TABLE `challenge_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `challenge_leaderboard`
--
ALTER TABLE `challenge_leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `challenge_questions`
--
ALTER TABLE `challenge_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `challenge_test_cases`
--
ALTER TABLE `challenge_test_cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `code_challenges`
--
ALTER TABLE `code_challenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coding_playlist`
--
ALTER TABLE `coding_playlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faq_items`
--
ALTER TABLE `faq_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback_likes`
--
ALTER TABLE `feedback_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback_messages`
--
ALTER TABLE `feedback_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `guest_challenge_attempts`
--
ALTER TABLE `guest_challenge_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `guest_quiz_attempts`
--
ALTER TABLE `guest_quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `guest_sessions`
--
ALTER TABLE `guest_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT for table `mini_game_modes`
--
ALTER TABLE `mini_game_modes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mini_game_results`
--
ALTER TABLE `mini_game_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_statistics`
--
ALTER TABLE `project_statistics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=433;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `timeline_events`
--
ALTER TABLE `timeline_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tutorial_visits`
--
ALTER TABLE `tutorial_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_achievements`
--
ALTER TABLE `user_achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_challenge_attempts`
--
ALTER TABLE `user_challenge_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_code_submissions`
--
ALTER TABLE `user_code_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_mini_game_attempts`
--
ALTER TABLE `user_mini_game_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_mini_game_preferences`
--
ALTER TABLE `user_mini_game_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_quiz_attempts`
--
ALTER TABLE `user_quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_tutorial_modes_progress`
--
ALTER TABLE `user_tutorial_modes_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_welcome_tracking`
--
ALTER TABLE `user_welcome_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `visitor_stats`
--
ALTER TABLE `visitor_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `visitor_tracking`
--
ALTER TABLE `visitor_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_actions_log`
--
ALTER TABLE `admin_actions_log`
  ADD CONSTRAINT `admin_actions_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL;

--
-- Constraints for table `challenge_answers`
--
ALTER TABLE `challenge_answers`
  ADD CONSTRAINT `challenge_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `challenge_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `challenge_leaderboard`
--
ALTER TABLE `challenge_leaderboard`
  ADD CONSTRAINT `challenge_leaderboard_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `challenge_leaderboard_ibfk_2` FOREIGN KEY (`guest_session_id`) REFERENCES `guest_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `challenge_test_cases`
--
ALTER TABLE `challenge_test_cases`
  ADD CONSTRAINT `challenge_test_cases_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `challenge_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback_likes`
--
ALTER TABLE `feedback_likes`
  ADD CONSTRAINT `feedback_likes_ibfk_1` FOREIGN KEY (`feedback_id`) REFERENCES `feedback_messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `guest_challenge_attempts`
--
ALTER TABLE `guest_challenge_attempts`
  ADD CONSTRAINT `guest_challenge_attempts_ibfk_1` FOREIGN KEY (`guest_session_id`) REFERENCES `guest_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guest_challenge_attempts_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `challenge_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guest_quiz_attempts`
--
ALTER TABLE `guest_quiz_attempts`
  ADD CONSTRAINT `guest_quiz_attempts_ibfk_1` FOREIGN KEY (`guest_session_id`) REFERENCES `guest_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guest_quiz_attempts_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guest_quiz_attempts_ibfk_3` FOREIGN KEY (`selected_answer_id`) REFERENCES `quiz_answers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `mini_game_results`
--
ALTER TABLE `mini_game_results`
  ADD CONSTRAINT `mini_game_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD CONSTRAINT `password_reset_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tutorial_visits`
--
ALTER TABLE `tutorial_visits`
  ADD CONSTRAINT `tutorial_visits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tutorial_visits_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `programming_languages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD CONSTRAINT `user_achievements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_challenge_attempts`
--
ALTER TABLE `user_challenge_attempts`
  ADD CONSTRAINT `user_challenge_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_challenge_attempts_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `challenge_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_code_submissions`
--
ALTER TABLE `user_code_submissions`
  ADD CONSTRAINT `user_code_submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_code_submissions_ibfk_2` FOREIGN KEY (`challenge_id`) REFERENCES `code_challenges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_mini_game_attempts`
--
ALTER TABLE `user_mini_game_attempts`
  ADD CONSTRAINT `user_mini_game_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_mini_game_preferences`
--
ALTER TABLE `user_mini_game_preferences`
  ADD CONSTRAINT `user_mini_game_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_quiz_attempts`
--
ALTER TABLE `user_quiz_attempts`
  ADD CONSTRAINT `user_quiz_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_quiz_attempts_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_quiz_attempts_ibfk_3` FOREIGN KEY (`selected_answer_id`) REFERENCES `quiz_answers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_tutorial_modes_progress`
--
ALTER TABLE `user_tutorial_modes_progress`
  ADD CONSTRAINT `user_tutorial_modes_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_welcome_tracking`
--
ALTER TABLE `user_welcome_tracking`
  ADD CONSTRAINT `user_welcome_tracking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
