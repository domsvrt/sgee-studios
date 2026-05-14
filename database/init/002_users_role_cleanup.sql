ALTER TABLE users
  DROP COLUMN admin_level,
  MODIFY COLUMN role ENUM('user','manager','admin') NOT NULL DEFAULT 'user';
