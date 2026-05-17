UPDATE users
SET role = 'admin'
WHERE role = 'manager';

ALTER TABLE users
  DROP COLUMN admin_level,
  MODIFY COLUMN role ENUM('user','admin') NOT NULL DEFAULT 'user';
