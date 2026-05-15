-- Add stable public-facing codes for key entities (MySQL 8 compatible).

SET @sql := IF(
  EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'user_code'),
  'SELECT 1',
  'ALTER TABLE users ADD COLUMN user_code VARCHAR(20) NULL AFTER id'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'uq_users_user_code'),
  'SELECT 1',
  'ALTER TABLE users ADD UNIQUE KEY uq_users_user_code (user_code)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'service_categories' AND COLUMN_NAME = 'category_code'),
  'SELECT 1',
  'ALTER TABLE service_categories ADD COLUMN category_code VARCHAR(20) NULL AFTER id'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'service_categories' AND INDEX_NAME = 'uq_service_categories_category_code'),
  'SELECT 1',
  'ALTER TABLE service_categories ADD UNIQUE KEY uq_service_categories_category_code (category_code)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'service_sections' AND COLUMN_NAME = 'section_code'),
  'SELECT 1',
  'ALTER TABLE service_sections ADD COLUMN section_code VARCHAR(20) NULL AFTER id'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'service_sections' AND INDEX_NAME = 'uq_service_sections_section_code'),
  'SELECT 1',
  'ALTER TABLE service_sections ADD UNIQUE KEY uq_service_sections_section_code (section_code)'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @n := 0;
UPDATE users
SET user_code = CONCAT('USR-', LPAD((@n := @n + 1), 6, '0'))
WHERE user_code IS NULL OR user_code = ''
ORDER BY id;

SET @n := 0;
UPDATE service_categories
SET category_code = CONCAT('CAT-', LPAD((@n := @n + 1), 4, '0'))
WHERE category_code IS NULL OR category_code = ''
ORDER BY id;

SET @n := 0;
UPDATE service_sections
SET section_code = CONCAT('SEC-', LPAD((@n := @n + 1), 4, '0'))
WHERE section_code IS NULL OR section_code = ''
ORDER BY id;

SET @sql := IF(
  EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'user_code' AND IS_NULLABLE = 'YES'),
  'ALTER TABLE users MODIFY user_code VARCHAR(20) NOT NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'service_categories' AND COLUMN_NAME = 'category_code' AND IS_NULLABLE = 'YES'),
  'ALTER TABLE service_categories MODIFY category_code VARCHAR(20) NOT NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  EXISTS(SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'service_sections' AND COLUMN_NAME = 'section_code' AND IS_NULLABLE = 'YES'),
  'ALTER TABLE service_sections MODIFY section_code VARCHAR(20) NOT NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
