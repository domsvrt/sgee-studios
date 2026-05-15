ALTER TABLE users
  ADD COLUMN first_name VARCHAR(60) NOT NULL DEFAULT '' AFTER password_hash,
  ADD COLUMN last_name VARCHAR(60) NOT NULL DEFAULT '' AFTER first_name;

UPDATE users
SET
  first_name = TRIM(SUBSTRING_INDEX(full_name, ' ', 1)),
  last_name = TRIM(SUBSTRING(full_name, CHAR_LENGTH(SUBSTRING_INDEX(full_name, ' ', 1)) + 1));

ALTER TABLE users
  DROP COLUMN full_name,
  MODIFY COLUMN first_name VARCHAR(60) NOT NULL,
  MODIFY COLUMN last_name VARCHAR(60) NOT NULL;
