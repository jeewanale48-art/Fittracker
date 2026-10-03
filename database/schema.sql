CREATE DATABASE IF NOT EXISTS fittrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fittrack;

CREATE TABLE users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('user','admin') NOT NULL DEFAULT 'user',
 subscription_plan ENUM('monthly','quarterly','yearly') NULL DEFAULT NULL,
 subscription_status ENUM('normal','pending','active','expired') NOT NULL DEFAULT 'normal',
 subscription_expires_at DATE NULL DEFAULT NULL,
 date_of_birth DATE NULL,
 height_cm DECIMAL(5,2) NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE exercises (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 category VARCHAR(50) NOT NULL,
 muscle_group VARCHAR(100) NULL,
 instructions TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE workout_plans (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 name VARCHAR(150) NOT NULL,
 description TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE plan_exercises (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 plan_id BIGINT UNSIGNED NOT NULL,
 exercise_id BIGINT UNSIGNED NOT NULL,
 planned_sets INT UNSIGNED NULL,
 planned_reps INT UNSIGNED NULL,
 sort_order INT UNSIGNED NOT NULL DEFAULT 0,
 FOREIGN KEY (plan_id) REFERENCES workout_plans(id) ON DELETE CASCADE,
 FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE workout_sessions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 plan_id BIGINT UNSIGNED NULL,
 session_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 duration_minutes INT UNSIGNED NULL,
 notes TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY (plan_id) REFERENCES workout_plans(id) ON DELETE SET NULL,
 INDEX idx_sessions_user_date (user_id, session_date)
) ENGINE=InnoDB;

CREATE TABLE workout_sets (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 session_id BIGINT UNSIGNED NOT NULL,
 exercise_id BIGINT UNSIGNED NOT NULL,
 set_number INT UNSIGNED NOT NULL,
 repetitions INT UNSIGNED NULL,
 weight_kg DECIMAL(7,2) NULL,
 duration_seconds INT UNSIGNED NULL,
 distance_km DECIMAL(8,3) NULL,
 FOREIGN KEY (session_id) REFERENCES workout_sessions(id) ON DELETE CASCADE,
 FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE fitness_goals (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 title VARCHAR(150) NOT NULL,
 goal_type ENUM('target_weight','workout_frequency','workout_minutes','other') NOT NULL DEFAULT 'other',
 starting_value DECIMAL(10,2) NULL,
 target_value DECIMAL(10,2) NOT NULL,
 unit VARCHAR(30) NULL,
 start_date DATE NOT NULL,
 deadline DATE NULL,
 status ENUM('active','completed','cancelled') NOT NULL DEFAULT 'active',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE body_measurements (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 weight_kg DECIMAL(6,2) NULL,
 body_fat_percent DECIMAL(5,2) NULL,
 waist_cm DECIMAL(6,2) NULL,
 notes TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_measurements_user_date (user_id, recorded_at)
) ENGINE=InnoDB;

CREATE TABLE water_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 log_date DATE NOT NULL,
 amount_ml INT UNSIGNED NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_water_user_date (user_id, log_date)
) ENGINE=InnoDB;

CREATE TABLE food_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 meal_name VARCHAR(150) NOT NULL,
 meal_type ENUM('breakfast','lunch','dinner','snack') NOT NULL DEFAULT 'snack',
 calories DECIMAL(8,2) NULL,
 protein_g DECIMAL(7,2) NULL,
 carbohydrates_g DECIMAL(7,2) NULL,
 fat_g DECIMAL(7,2) NULL,
 logged_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_food_user_date (user_id, logged_at)
) ENGINE=InnoDB;

INSERT INTO exercises (name, category, muscle_group, instructions) VALUES
('Push-up', 'Strength', 'Chest', 'Keep the body straight and lower with control.'),
('Bodyweight squat', 'Strength', 'Legs', 'Keep the feet stable and lower to a comfortable depth.'),
('Plank', 'Strength', 'Core', 'Keep the body aligned and brace the core.'),
('Walking', 'Cardio', 'Full body', 'Walk at a comfortable, steady pace.'),
('Running', 'Cardio', 'Full body', 'Choose a pace appropriate to your fitness level.'),
('Cycling', 'Cardio', 'Legs', 'Maintain a comfortable cadence and safe posture.'),
('Hamstring stretch', 'Flexibility', 'Hamstrings', 'Stretch gently without bouncing.');
