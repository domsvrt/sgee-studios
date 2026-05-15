CREATE TABLE IF NOT EXISTS service_sections (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(140) NOT NULL,
  description VARCHAR(255) NULL,
  selection_type ENUM('multiple','single') NOT NULL DEFAULT 'multiple',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_service_sections_category_id (category_id),
  KEY idx_service_sections_sort_order (sort_order),
  CONSTRAINT fk_service_sections_category_id
    FOREIGN KEY (category_id) REFERENCES service_categories(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE services
  ADD COLUMN section_id BIGINT UNSIGNED NULL AFTER category_id,
  ADD KEY idx_services_section_id (section_id),
  ADD CONSTRAINT fk_services_section_id
    FOREIGN KEY (section_id) REFERENCES service_sections(id)
    ON DELETE SET NULL ON UPDATE CASCADE;
