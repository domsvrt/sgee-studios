-- Remove legacy users.full_name after split-name migration.
-- Safe to run multiple times.

SET @sql := IF(
  EXISTS(
    SELECT 1
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'full_name'
  ),
  'ALTER TABLE users DROP COLUMN full_name',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
