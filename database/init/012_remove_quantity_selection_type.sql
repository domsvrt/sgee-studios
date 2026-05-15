-- Remove legacy `quantity` selection type support from sections and services.
-- Normalize any existing quantity rows to multiple before tightening enums.

UPDATE services
SET selection_type = 'multiple'
WHERE selection_type = 'quantity';

UPDATE service_sections
SET selection_type = 'multiple'
WHERE selection_type = 'quantity';

ALTER TABLE services
  MODIFY selection_type ENUM('single','multiple') NOT NULL DEFAULT 'multiple';

ALTER TABLE service_sections
  MODIFY selection_type ENUM('multiple','single') NOT NULL DEFAULT 'multiple';
