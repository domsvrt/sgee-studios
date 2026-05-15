-- Seed catalog copied from sampleOnly/usersample.html
-- Idempotent by matching category/section names and unique service codes.

INSERT INTO service_categories (name, description, is_active, sort_order)
SELECT 'Real Estate', 'Listings, aerials, virtual staging and more', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM service_categories WHERE name = 'Real Estate');
INSERT INTO service_categories (name, description, is_active, sort_order)
SELECT 'Branding Sessions', 'Personal branding and headshots', 1, 2
WHERE NOT EXISTS (SELECT 1 FROM service_categories WHERE name = 'Branding Sessions');
INSERT INTO service_categories (name, description, is_active, sort_order)
SELECT 'Event Coverage', 'Birthdays, corporate and special events', 1, 3
WHERE NOT EXISTS (SELECT 1 FROM service_categories WHERE name = 'Event Coverage');
INSERT INTO service_categories (name, description, is_active, sort_order)
SELECT 'Weddings', 'Wedding photo and video coverage', 1, 4
WHERE NOT EXISTS (SELECT 1 FROM service_categories WHERE name = 'Weddings');

UPDATE service_categories SET description = 'Listings, aerials, virtual staging and more', is_active = 1, sort_order = 1 WHERE name = 'Real Estate';
UPDATE service_categories SET description = 'Personal branding and headshots', is_active = 1, sort_order = 2 WHERE name = 'Branding Sessions';
UPDATE service_categories SET description = 'Birthdays, corporate and special events', is_active = 1, sort_order = 3 WHERE name = 'Event Coverage';
UPDATE service_categories SET description = 'Wedding photo and video coverage', is_active = 1, sort_order = 4 WHERE name = 'Weddings';

INSERT INTO service_sections (category_id, name, description, selection_type, is_active, sort_order)
SELECT c.id, s.name, s.description, s.selection_type, 1, s.sort_order
FROM service_categories c
JOIN (
    SELECT 'Real Estate' AS category_name, 'Residential Photography' AS name, 'All photography includes: Print Ready Photos | MLS Ready Photos | Blue Skies | Green Grass | Clear Windows | Aerial Shot | Next Day Delivery' AS description, 'single' AS selection_type, 1 AS sort_order
    UNION ALL SELECT 'Real Estate', 'Video Walk-Through', 'Drone Footage included in all packages!', 'multiple', 2
    UNION ALL SELECT 'Real Estate', 'Aerial Drone Photography', 'Capturing stunning 4K High Definition images and videos!', 'multiple', 3
    UNION ALL SELECT 'Real Estate', 'Packages', 'Premium bundled services for maximum impact.', 'single', 4
    UNION ALL SELECT 'Branding Sessions', 'Personal Branding Photography', 'Elevate your professional image with a tailored branding session.', 'single', 1
    UNION ALL SELECT 'Branding Sessions', 'Headshots', NULL, 'single', 2
    UNION ALL SELECT 'Event Coverage', 'Event Photography', 'Professional coverage for any occasion.', 'single', 1
    UNION ALL SELECT 'Event Coverage', 'Event Videography', NULL, 'single', 2
    UNION ALL SELECT 'Weddings', 'Wedding Photography', 'Your love story, beautifully told.', 'single', 1
    UNION ALL SELECT 'Weddings', 'Wedding Videography', NULL, 'single', 2
) s ON s.category_name = c.name
WHERE NOT EXISTS (
    SELECT 1
    FROM service_sections ss
    WHERE ss.category_id = c.id
      AND ss.name = s.name
);

UPDATE service_sections ss
JOIN service_categories c ON c.id = ss.category_id
SET ss.description = 'All photography includes: Print Ready Photos | MLS Ready Photos | Blue Skies | Green Grass | Clear Windows | Aerial Shot | Next Day Delivery', ss.selection_type = 'single', ss.is_active = 1, ss.sort_order = 1
WHERE c.name = 'Real Estate' AND ss.name = 'Residential Photography';
UPDATE service_sections ss
JOIN service_categories c ON c.id = ss.category_id
SET ss.description = 'Drone Footage included in all packages!', ss.selection_type = 'multiple', ss.is_active = 1, ss.sort_order = 2
WHERE c.name = 'Real Estate' AND ss.name = 'Video Walk-Through';
UPDATE service_sections ss
JOIN service_categories c ON c.id = ss.category_id
SET ss.description = 'Capturing stunning 4K High Definition images and videos!', ss.selection_type = 'multiple', ss.is_active = 1, ss.sort_order = 3
WHERE c.name = 'Real Estate' AND ss.name = 'Aerial Drone Photography';
UPDATE service_sections ss
JOIN service_categories c ON c.id = ss.category_id
SET ss.description = 'Premium bundled services for maximum impact.', ss.selection_type = 'single', ss.is_active = 1, ss.sort_order = 4
WHERE c.name = 'Real Estate' AND ss.name = 'Packages';
UPDATE service_sections ss
JOIN service_categories c ON c.id = ss.category_id
SET ss.description = 'Elevate your professional image with a tailored branding session.', ss.selection_type = 'single', ss.is_active = 1, ss.sort_order = 1
WHERE c.name = 'Branding Sessions' AND ss.name = 'Personal Branding Photography';
UPDATE service_sections ss
JOIN service_categories c ON c.id = ss.category_id
SET ss.description = NULL, ss.selection_type = 'single', ss.is_active = 1, ss.sort_order = 2
WHERE c.name = 'Branding Sessions' AND ss.name = 'Headshots';
UPDATE service_sections ss
JOIN service_categories c ON c.id = ss.category_id
SET ss.description = 'Professional coverage for any occasion.', ss.selection_type = 'single', ss.is_active = 1, ss.sort_order = 1
WHERE c.name = 'Event Coverage' AND ss.name = 'Event Photography';
UPDATE service_sections ss
JOIN service_categories c ON c.id = ss.category_id
SET ss.description = NULL, ss.selection_type = 'single', ss.is_active = 1, ss.sort_order = 2
WHERE c.name = 'Event Coverage' AND ss.name = 'Event Videography';
UPDATE service_sections ss
JOIN service_categories c ON c.id = ss.category_id
SET ss.description = 'Your love story, beautifully told.', ss.selection_type = 'single', ss.is_active = 1, ss.sort_order = 1
WHERE c.name = 'Weddings' AND ss.name = 'Wedding Photography';
UPDATE service_sections ss
JOIN service_categories c ON c.id = ss.category_id
SET ss.description = NULL, ss.selection_type = 'single', ss.is_active = 1, ss.sort_order = 2
WHERE c.name = 'Weddings' AND ss.name = 'Wedding Videography';

INSERT INTO services (category_id, section_id, code, name, description, price, unit_label, selection_type, is_active, sort_order)
SELECT c.id, ss.id, x.code, x.name, x.description, x.price, NULL, x.selection_type, 1, x.sort_order
FROM (
    SELECT 'Real Estate' AS category_name, 'Residential Photography' AS section_name, 'rp5' AS code, '5 Photos' AS name, NULL AS description, 75.00 AS price, 'single' AS selection_type, 1 AS sort_order
    UNION ALL SELECT 'Real Estate','Residential Photography','rp10','10 Photos',NULL,90.00,'single',2
    UNION ALL SELECT 'Real Estate','Residential Photography','rp15','15 Photos',NULL,100.00,'single',3
    UNION ALL SELECT 'Real Estate','Residential Photography','rp20','20 Photos',NULL,125.00,'single',4
    UNION ALL SELECT 'Real Estate','Residential Photography','rp30','30 Photos',NULL,150.00,'single',5
    UNION ALL SELECT 'Real Estate','Residential Photography','rp35','35 Photos',NULL,165.00,'single',6
    UNION ALL SELECT 'Real Estate','Residential Photography','rp40','40 Photos',NULL,175.00,'single',7
    UNION ALL SELECT 'Real Estate','Video Walk-Through','sev-he','Social Edge Video (High-Energy)','Best for agents who want a fast, high-energy reel.',185.00,'multiple',1
    UNION ALL SELECT 'Real Estate','Video Walk-Through','sev-bal','Social Edge Video (Balanced)','Best for agents who want current and engaging marketing.',185.00,'multiple',2
    UNION ALL SELECT 'Real Estate','Video Walk-Through','sev-cin','Social Edge Video (Cinematic)','Best for agents who want slow, cinematic reels.',185.00,'multiple',3
    UNION ALL SELECT 'Real Estate','Video Walk-Through','lt','Listing Teaser','30 second video slideshow for Facebook and Instagram.',10.00,'multiple',4
    UNION ALL SELECT 'Real Estate','Aerial Drone Photography','ad5','5-10 Drone Photos',NULL,125.00,'multiple',1
    UNION ALL SELECT 'Real Estate','Aerial Drone Photography','ad10','10-20 Drone Photos',NULL,145.00,'multiple',2
    UNION ALL SELECT 'Real Estate','Aerial Drone Photography','adv','Aerial Drone Videography','Birds-eye view aerial videography.',125.00,'multiple',3
    UNION ALL SELECT 'Real Estate','Packages','pkg-lux','Luxury','Interior & Exterior Photos | Video | Twilight | Drone | Floorplan',1250.00,'single',1
    UNION ALL SELECT 'Real Estate','Packages','pkg-tp','Top Producer','Interior & Exterior | Video | Twilight | Drone | Floorplan',950.00,'single',2
    UNION ALL SELECT 'Real Estate','Packages','pkg-pl','Preferred Listing','Interior & Exterior | Twilight | Drone | Floorplan',595.00,'single',3
    UNION ALL SELECT 'Real Estate','Packages','pkg-en','Enhanced','Interior & Exterior | Twilight | Drone',425.00,'single',4
    UNION ALL SELECT 'Real Estate','Packages','pkg-es','Essentials','Interior & Exterior | Drone or Floorplan',280.00,'single',5
    UNION ALL SELECT 'Branding Sessions','Personal Branding Photography','br-starter','Starter Brand','45 Min | 10 Retouched | 1 Location',350.00,'single',1
    UNION ALL SELECT 'Branding Sessions','Personal Branding Photography','br-pro','Professional Brand','90 Min | 20 Retouched | 2 Locations',650.00,'single',2
    UNION ALL SELECT 'Branding Sessions','Personal Branding Photography','br-elite','Elite Brand','Half-Day | 40 Retouched | 3 Locations',1200.00,'single',3
    UNION ALL SELECT 'Branding Sessions','Headshots','brhs-basic','Basic Package','30 Min | 5 Retouched | 2 Backdrops',185.00,'single',1
    UNION ALL SELECT 'Branding Sessions','Headshots','brhs-pro','Professional','60 Min | 10 Retouched | Consultation',250.00,'single',2
    UNION ALL SELECT 'Branding Sessions','Headshots','brhs-prem','Premium','90 Min | 15 Retouched | Multiple Options',400.00,'single',3
    UNION ALL SELECT 'Event Coverage','Event Photography','ev-2hr','2-Hour Coverage','Up to 100 edited photos',350.00,'single',1
    UNION ALL SELECT 'Event Coverage','Event Photography','ev-4hr','4-Hour Coverage','Up to 200 edited photos',600.00,'single',2
    UNION ALL SELECT 'Event Coverage','Event Photography','ev-full','Full-Day Coverage','Up to 500 edited photos | 2 Photographers',1100.00,'single',3
    UNION ALL SELECT 'Event Coverage','Event Videography','evv-high','Highlight Reel','2-3 minute cinematic video',500.00,'single',1
    UNION ALL SELECT 'Event Coverage','Event Videography','evv-doc','Documentary Edit','10-15 minute full event film',1200.00,'single',2
    UNION ALL SELECT 'Weddings','Wedding Photography','wd-silver','Silver','6 Hours | 1 Photographer | 300+ Photos',2200.00,'single',1
    UNION ALL SELECT 'Weddings','Wedding Photography','wd-gold','Gold','8 Hours | 2 Photographers | 500+ Photos',3500.00,'single',2
    UNION ALL SELECT 'Weddings','Wedding Photography','wd-plat','Platinum','Full Day | 2 Photographers | Unlimited',5000.00,'single',3
    UNION ALL SELECT 'Weddings','Wedding Videography','wdv-hl','Highlight Film','3-5 minute cinematic',1800.00,'single',1
    UNION ALL SELECT 'Weddings','Wedding Videography','wdv-feat','Feature Film','15-20 minute documentary',3000.00,'single',2
) x
JOIN service_categories c ON c.name = x.category_name
JOIN service_sections ss ON ss.category_id = c.id AND ss.name = x.section_name
WHERE NOT EXISTS (SELECT 1 FROM services s WHERE s.code = x.code);

UPDATE services s
JOIN (
    SELECT 'rp5' AS code, '5 Photos' AS name, NULL AS description, 75.00 AS price, 'single' AS selection_type, 1 AS sort_order
    UNION ALL SELECT 'rp10','10 Photos',NULL,90.00,'single',2
    UNION ALL SELECT 'rp15','15 Photos',NULL,100.00,'single',3
    UNION ALL SELECT 'rp20','20 Photos',NULL,125.00,'single',4
    UNION ALL SELECT 'rp30','30 Photos',NULL,150.00,'single',5
    UNION ALL SELECT 'rp35','35 Photos',NULL,165.00,'single',6
    UNION ALL SELECT 'rp40','40 Photos',NULL,175.00,'single',7
    UNION ALL SELECT 'sev-he','Social Edge Video (High-Energy)','Best for agents who want a fast, high-energy reel.',185.00,'multiple',1
    UNION ALL SELECT 'sev-bal','Social Edge Video (Balanced)','Best for agents who want current and engaging marketing.',185.00,'multiple',2
    UNION ALL SELECT 'sev-cin','Social Edge Video (Cinematic)','Best for agents who want slow, cinematic reels.',185.00,'multiple',3
    UNION ALL SELECT 'lt','Listing Teaser','30 second video slideshow for Facebook and Instagram.',10.00,'multiple',4
    UNION ALL SELECT 'ad5','5-10 Drone Photos',NULL,125.00,'multiple',1
    UNION ALL SELECT 'ad10','10-20 Drone Photos',NULL,145.00,'multiple',2
    UNION ALL SELECT 'adv','Aerial Drone Videography','Birds-eye view aerial videography.',125.00,'multiple',3
    UNION ALL SELECT 'pkg-lux','Luxury','Interior & Exterior Photos | Video | Twilight | Drone | Floorplan',1250.00,'single',1
    UNION ALL SELECT 'pkg-tp','Top Producer','Interior & Exterior | Video | Twilight | Drone | Floorplan',950.00,'single',2
    UNION ALL SELECT 'pkg-pl','Preferred Listing','Interior & Exterior | Twilight | Drone | Floorplan',595.00,'single',3
    UNION ALL SELECT 'pkg-en','Enhanced','Interior & Exterior | Twilight | Drone',425.00,'single',4
    UNION ALL SELECT 'pkg-es','Essentials','Interior & Exterior | Drone or Floorplan',280.00,'single',5
    UNION ALL SELECT 'br-starter','Starter Brand','45 Min | 10 Retouched | 1 Location',350.00,'single',1
    UNION ALL SELECT 'br-pro','Professional Brand','90 Min | 20 Retouched | 2 Locations',650.00,'single',2
    UNION ALL SELECT 'br-elite','Elite Brand','Half-Day | 40 Retouched | 3 Locations',1200.00,'single',3
    UNION ALL SELECT 'brhs-basic','Basic Package','30 Min | 5 Retouched | 2 Backdrops',185.00,'single',1
    UNION ALL SELECT 'brhs-pro','Professional','60 Min | 10 Retouched | Consultation',250.00,'single',2
    UNION ALL SELECT 'brhs-prem','Premium','90 Min | 15 Retouched | Multiple Options',400.00,'single',3
    UNION ALL SELECT 'ev-2hr','2-Hour Coverage','Up to 100 edited photos',350.00,'single',1
    UNION ALL SELECT 'ev-4hr','4-Hour Coverage','Up to 200 edited photos',600.00,'single',2
    UNION ALL SELECT 'ev-full','Full-Day Coverage','Up to 500 edited photos | 2 Photographers',1100.00,'single',3
    UNION ALL SELECT 'evv-high','Highlight Reel','2-3 minute cinematic video',500.00,'single',1
    UNION ALL SELECT 'evv-doc','Documentary Edit','10-15 minute full event film',1200.00,'single',2
    UNION ALL SELECT 'wd-silver','Silver','6 Hours | 1 Photographer | 300+ Photos',2200.00,'single',1
    UNION ALL SELECT 'wd-gold','Gold','8 Hours | 2 Photographers | 500+ Photos',3500.00,'single',2
    UNION ALL SELECT 'wd-plat','Platinum','Full Day | 2 Photographers | Unlimited',5000.00,'single',3
    UNION ALL SELECT 'wdv-hl','Highlight Film','3-5 minute cinematic',1800.00,'single',1
    UNION ALL SELECT 'wdv-feat','Feature Film','15-20 minute documentary',3000.00,'single',2
) x ON x.code = s.code
SET s.name = x.name,
    s.description = x.description,
    s.price = x.price,
    s.selection_type = x.selection_type,
    s.is_active = 1,
    s.sort_order = x.sort_order;
