ALTER TABLE service_categories
  DROP INDEX uq_service_categories_slug,
  DROP COLUMN slug;
