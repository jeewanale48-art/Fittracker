-- FitTrack subscription feature migration
-- Import this file into the existing `fittrack` database using phpMyAdmin.
ALTER TABLE users
  ADD COLUMN subscription_plan ENUM('monthly','quarterly','yearly') NULL DEFAULT NULL,
  ADD COLUMN subscription_status ENUM('normal','pending','active','expired') NOT NULL DEFAULT 'normal',
  ADD COLUMN subscription_expires_at DATE NULL DEFAULT NULL;
