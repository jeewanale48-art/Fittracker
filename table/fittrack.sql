-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2026 at 10:25 AM
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
-- Database: `fittrack`
--

-- --------------------------------------------------------

--
-- Table structure for table `body_measurements`
--

CREATE TABLE `body_measurements` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `measured_on` date NOT NULL,
  `weight_kg` decimal(6,2) DEFAULT NULL,
  `body_fat_percent` decimal(5,2) DEFAULT NULL,
  `chest_cm` decimal(6,2) DEFAULT NULL,
  `waist_cm` decimal(6,2) DEFAULT NULL,
  `hips_cm` decimal(6,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `category` varchar(80) NOT NULL DEFAULT 'General',
  `muscle_group` varchar(100) DEFAULT NULL,
  `difficulty` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `equipment` varchar(120) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `name`, `category`, `muscle_group`, `difficulty`, `equipment`, `instructions`, `image_url`, `created_at`) VALUES
(1, 'Push-Up', 'Strength', 'Chest', 'beginner', 'None', 'Keep your body straight, lower your chest toward the floor, then push back up.', NULL, '2026-10-03 05:28:58'),
(2, 'Bodyweight Squat', 'Strength', 'Legs', 'beginner', 'None', 'Stand with feet shoulder-width apart, bend your knees and hips, then stand up.', NULL, '2026-10-03 05:28:58'),
(3, 'Plank', 'Core', 'Abs', 'beginner', 'None', 'Keep your elbows under your shoulders and hold a straight body position.', NULL, '2026-10-03 05:28:58'),
(4, 'Dumbbell Row', 'Strength', 'Back', 'intermediate', 'Dumbbells', 'Keep your back flat and pull the dumbbell toward your hip with control.', NULL, '2026-10-03 05:28:58'),
(5, 'Chest Press', 'General', 'Chest,Triceps', 'intermediate', 'Dumbell', '2 set to each', NULL, '2026-10-03 06:43:20');

-- --------------------------------------------------------

--
-- Table structure for table `fitness_goals`
--

CREATE TABLE `fitness_goals` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `goal_type` enum('weight','workouts','duration','steps','custom') NOT NULL DEFAULT 'custom',
  `target_value` decimal(10,2) DEFAULT NULL,
  `current_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(30) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `status` enum('active','completed','paused') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fitness_goals`
--

INSERT INTO `fitness_goals` (`id`, `user_id`, `title`, `goal_type`, `target_value`, `current_value`, `unit`, `start_date`, `target_date`, `status`, `created_at`) VALUES
(1, 5, 'arnold chest', 'duration', 1.00, 0.00, '60', '2026-10-03', NULL, 'active', '2026-10-03 07:43:28');

-- --------------------------------------------------------

--
-- Table structure for table `food_logs`
--

CREATE TABLE `food_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `logged_at` datetime NOT NULL DEFAULT current_timestamp(),
  `meal_type` enum('breakfast','lunch','dinner','snack') NOT NULL DEFAULT 'snack',
  `food_name` varchar(150) NOT NULL,
  `calories` int(10) UNSIGNED DEFAULT NULL,
  `protein_g` decimal(7,2) DEFAULT NULL,
  `carbs_g` decimal(7,2) DEFAULT NULL,
  `fat_g` decimal(7,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan_exercises`
--

CREATE TABLE `plan_exercises` (
  `id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `exercise_id` int(10) UNSIGNED NOT NULL,
  `sets_count` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `reps_count` varchar(30) NOT NULL DEFAULT '10',
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `age` tinyint(3) UNSIGNED DEFAULT NULL,
  `gender` enum('male','female','other','prefer_not_to_say') DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `fitness_level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `subscription_plan` enum('monthly','quarterly','yearly') DEFAULT NULL,
  `subscription_status` enum('normal','pending','active','expired') NOT NULL DEFAULT 'normal',
  `subscription_expires_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `age`, `gender`, `height_cm`, `weight_kg`, `fitness_level`, `created_at`, `updated_at`, `subscription_plan`, `subscription_status`, `subscription_expires_at`) VALUES
(3, 'Rishi shah', 'rishishahm1887@gmail.com', '$2y$10$qPxDat9B0C3haA5T.IoaVeebQx.CtLXvNzsdi7cb1rPoWewbOZJ1y', 'user', 23, 'male', 185.00, 60.00, 'beginner', '2026-10-03 06:26:25', '2026-10-03 07:56:23', 'monthly', 'active', '2026-11-03'),
(4, 'jeewan ale magar', 'jeewanale48@gmail.com', '$2y$10$w.Ua7Zj0H4Xo7qsiBTNX2eLd.DZlVkemw9wIdjyovfHCtRGNIl68u', 'admin', NULL, NULL, NULL, NULL, 'beginner', '2026-10-03 06:31:52', '2026-10-03 06:31:52', NULL, 'normal', NULL),
(5, 'sunny shah', 'sunnyshah35350@gmail.com', '$2y$10$YJMiA4zmx0yVvwIE7i2LVe2RztiAqN5P0S8s8am3zV6G9HawYJN5a', 'user', 23, 'male', 165.00, 47.00, 'intermediate', '2026-10-03 06:41:18', '2026-10-03 07:57:57', 'monthly', 'pending', '2027-01-03');

-- --------------------------------------------------------

--
-- Table structure for table `water_logs`
--

CREATE TABLE `water_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `logged_on` date NOT NULL,
  `amount_ml` smallint(5) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `water_logs`
--

INSERT INTO `water_logs` (`id`, `user_id`, `logged_on`, `amount_ml`, `created_at`) VALUES
(1, 5, '2026-10-03', 5000, '2026-10-03 07:28:46');

-- --------------------------------------------------------

--
-- Table structure for table `workout_plans`
--

CREATE TABLE `workout_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `goal` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workout_plans`
--

INSERT INTO `workout_plans` (`id`, `user_id`, `name`, `description`, `goal`, `created_at`) VALUES
(1, 5, 'Leg_Day', 'first day', NULL, '2026-10-03 07:41:23');

-- --------------------------------------------------------

--
-- Table structure for table `workout_sessions`
--

CREATE TABLE `workout_sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `workout_date` date NOT NULL,
  `duration_minutes` smallint(5) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workout_sessions`
--

INSERT INTO `workout_sessions` (`id`, `user_id`, `plan_id`, `title`, `workout_date`, `duration_minutes`, `notes`, `created_at`) VALUES
(1, 5, 1, 'Workout session', '2026-10-03', 30, 'nice', '2026-10-03 07:42:05');

-- --------------------------------------------------------

--
-- Table structure for table `workout_sets`
--

CREATE TABLE `workout_sets` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `exercise_id` int(10) UNSIGNED NOT NULL,
  `set_number` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `reps` smallint(5) UNSIGNED DEFAULT NULL,
  `weight_kg` decimal(7,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `body_measurements`
--
ALTER TABLE `body_measurements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_body_measurements_user_date` (`user_id`,`measured_on`);

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_exercises_name` (`name`),
  ADD KEY `idx_exercises_category` (`category`);

--
-- Indexes for table `fitness_goals`
--
ALTER TABLE `fitness_goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fitness_goals_user` (`user_id`);

--
-- Indexes for table `food_logs`
--
ALTER TABLE `food_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_food_logs_user_logged` (`user_id`,`logged_at`);

--
-- Indexes for table `plan_exercises`
--
ALTER TABLE `plan_exercises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_plan_exercise` (`plan_id`,`exercise_id`),
  ADD KEY `fk_plan_exercises_exercise` (`exercise_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- Indexes for table `water_logs`
--
ALTER TABLE `water_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_water_logs_user_date` (`user_id`,`logged_on`);

--
-- Indexes for table `workout_plans`
--
ALTER TABLE `workout_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_workout_plans_user` (`user_id`);

--
-- Indexes for table `workout_sessions`
--
ALTER TABLE `workout_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_workout_sessions_user_date` (`user_id`,`workout_date`),
  ADD KEY `fk_workout_sessions_plan` (`plan_id`);

--
-- Indexes for table `workout_sets`
--
ALTER TABLE `workout_sets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_workout_sets_session` (`session_id`),
  ADD KEY `fk_workout_sets_exercise` (`exercise_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `body_measurements`
--
ALTER TABLE `body_measurements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fitness_goals`
--
ALTER TABLE `fitness_goals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `food_logs`
--
ALTER TABLE `food_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plan_exercises`
--
ALTER TABLE `plan_exercises`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `water_logs`
--
ALTER TABLE `water_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `workout_plans`
--
ALTER TABLE `workout_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `workout_sessions`
--
ALTER TABLE `workout_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `workout_sets`
--
ALTER TABLE `workout_sets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `body_measurements`
--
ALTER TABLE `body_measurements`
  ADD CONSTRAINT `fk_body_measurements_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fitness_goals`
--
ALTER TABLE `fitness_goals`
  ADD CONSTRAINT `fk_fitness_goals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `food_logs`
--
ALTER TABLE `food_logs`
  ADD CONSTRAINT `fk_food_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plan_exercises`
--
ALTER TABLE `plan_exercises`
  ADD CONSTRAINT `fk_plan_exercises_exercise` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_plan_exercises_plan` FOREIGN KEY (`plan_id`) REFERENCES `workout_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `water_logs`
--
ALTER TABLE `water_logs`
  ADD CONSTRAINT `fk_water_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_plans`
--
ALTER TABLE `workout_plans`
  ADD CONSTRAINT `fk_workout_plans_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_sessions`
--
ALTER TABLE `workout_sessions`
  ADD CONSTRAINT `fk_workout_sessions_plan` FOREIGN KEY (`plan_id`) REFERENCES `workout_plans` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_workout_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_sets`
--
ALTER TABLE `workout_sets`
  ADD CONSTRAINT `fk_workout_sets_exercise` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_workout_sets_session` FOREIGN KEY (`session_id`) REFERENCES `workout_sessions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
