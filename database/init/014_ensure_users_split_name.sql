-- Ensure users table has split-name columns and backfilled values.
-- Safe to run multiple times.

SET @sql := IF(
  EXISTS(
    SELECT 1
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'first_name'
  ),
  'SELECT 1',
  'ALTER TABLE users ADD COLUMN first_name VARCHAR(60) NULL AFTER password_hash'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  EXISTS(
    SELECT 1
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'last_name'
  ),
  'SELECT 1',
  'ALTER TABLE users ADD COLUMN last_name VARCHAR(60) NULL AFTER first_name'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Backfill first/last from full_name only when full_name exists.
SET @sql := IF(
  EXISTS(
    SELECT 1
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'full_name'
  ),
  \"UPDATE users
   SET
     first_name = TRIM(SUBSTRING_INDEX(TRIM(COALESCE(full_name, '')), ' ', 1)),
     last_name = TRIM(SUBSTRING(TRIM(COALESCE(full_name, '')), CHAR_LENGTH(SUBSTRING_INDEX(TRIM(COALESCE(full_name, '')), ' ', 1)) + 1))
   WHERE (first_name IS NULL OR TRIM(first_name) = '')
     AND (last_name IS NULL OR TRIM(last_name) = '')
     AND full_name IS NOT NULL
     AND TRIM(full_name) <> ''\",
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Final safeguard so names are always present for app logic.
UPDATE users
SET
  first_name = COALESCE(NULLIF(TRIM(first_name), ''), 'User'),
  last_name = COALESCE(NULLIF(TRIM(last_name), ''), '');

-- Tighten to NOT NULL once populated.
ALTER TABLE users
  MODIFY COLUMN first_name VARCHAR(60) NOT NULL,
  MODIFY COLUMN last_name VARCHAR(60) NOT NULL;
