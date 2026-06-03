-- SeedCycle: Weather Logs Table
-- Run in phpMyAdmin against the 'seed cycle' database

USE `seed cycle`;

CREATE TABLE IF NOT EXISTS `weather_logs` (
  `id`                BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`           INT(11)          NULL,
  `city`              VARCHAR(100)     NOT NULL DEFAULT '',
  `country`           VARCHAR(10)      NOT NULL DEFAULT '',
  `temperature`       DECIMAL(5,2)     NOT NULL DEFAULT 0,
  `feels_like`        DECIMAL(5,2)     NOT NULL DEFAULT 0,
  `humidity`          INT              NOT NULL DEFAULT 0,
  `rain_chance`       INT              NOT NULL DEFAULT 0,
  `weather_condition` VARCHAR(100)     NOT NULL DEFAULT '',
  `weather_icon`      VARCHAR(20)      NOT NULL DEFAULT '',
  `wind_speed`        DECIMAL(6,2)     NOT NULL DEFAULT 0,
  `recommendation`    ENUM('good','caution','bad') NOT NULL DEFAULT 'caution',
  `checked_at`        TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id`   (`user_id`),
  KEY `idx_city`      (`city`),
  KEY `idx_checked_at`(`checked_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
