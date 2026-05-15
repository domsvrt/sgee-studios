CREATE TABLE IF NOT EXISTS activity_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(60) NOT NULL,
  title VARCHAR(150) NOT NULL,
  message VARCHAR(255) NOT NULL,
  user_id BIGINT UNSIGNED NULL,
  booking_id BIGINT UNSIGNED NULL,
  metadata_json JSON NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_activity_logs_type_created (type, created_at),
  KEY idx_activity_logs_is_read_created (is_read, created_at),
  KEY idx_activity_logs_user_id (user_id),
  KEY idx_activity_logs_booking_id (booking_id),
  CONSTRAINT fk_activity_logs_user_id
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_activity_logs_booking_id
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
