# SGee Studios MySQL Database Draft (Admin + User Unified, No Payments)

This is a proposed schema draft for review.

## Scope from sample UI files

- `sampleOnly/admin.html` needs:
  - Dashboard metrics from bookings
  - Booking management (status, date/time, notes, amount)
  - User directory (replacing separate clients table)
- `sampleOnly/usersample.html` needs:
  - Service catalog by category
  - Service selection per booking
  - Calendar date selection and time slot
  - Contact details and notes from registered user account
- Payment feature intentionally removed.
- No guest booking: every booking must belong to a registered user account.

## Proposed tables

### 1) `users`
Single account table for both admin staff and regular booking users.

```sql
id BIGINT UNSIGNED PK AUTO_INCREMENT
email VARCHAR(191) UNIQUE NOT NULL
password_hash VARCHAR(255) NOT NULL
full_name VARCHAR(120) NOT NULL
phone VARCHAR(30) NULL
role ENUM('admin','user') NOT NULL DEFAULT 'user'
admin_level ENUM('owner','manager','staff') NULL
status ENUM('active','inactive','banned') DEFAULT 'active'
email_verified_at DATETIME NULL
last_login_at DATETIME NULL
created_at DATETIME NOT NULL
updated_at DATETIME NOT NULL
```

Notes:
- `role='admin'` for admin interface users.
- `admin_level` only used when `role='admin'`.
- `email` is unique login identity.
- Stores only password hash, never plaintext password.

### 2) `service_categories`
Main categories shown in booking UI.

```sql
id BIGINT UNSIGNED PK AUTO_INCREMENT
slug VARCHAR(60) UNIQUE NOT NULL
name VARCHAR(100) NOT NULL
description VARCHAR(255) NULL
is_active TINYINT(1) DEFAULT 1
sort_order INT DEFAULT 0
created_at DATETIME NOT NULL
updated_at DATETIME NOT NULL
```

Suggested seed examples:
- `real_estate`
- `branding`
- `events`
- `weddings`

### 3) `services`
Individual selectable services inside each category.

```sql
id BIGINT UNSIGNED PK AUTO_INCREMENT
category_id BIGINT UNSIGNED NOT NULL
code VARCHAR(60) UNIQUE NOT NULL
name VARCHAR(150) NOT NULL
description TEXT NULL
price DECIMAL(10,2) NOT NULL DEFAULT 0.00
unit_label VARCHAR(50) NULL
selection_type ENUM('single','multiple','quantity') DEFAULT 'multiple'
is_active TINYINT(1) DEFAULT 1
sort_order INT DEFAULT 0
created_at DATETIME NOT NULL
updated_at DATETIME NOT NULL

FK (category_id) REFERENCES service_categories(id) ON DELETE CASCADE
INDEX (category_id)
```

### 4) `bookings`
Main booking record for admin table and dashboard metrics.

```sql
id BIGINT UNSIGNED PK AUTO_INCREMENT
booking_code VARCHAR(40) UNIQUE NOT NULL
user_id BIGINT UNSIGNED NOT NULL
category_id BIGINT UNSIGNED NULL
booking_date DATE NOT NULL
booking_time TIME NOT NULL
status ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending'
notes TEXT NULL
total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00
created_by_user_id BIGINT UNSIGNED NULL
updated_by_user_id BIGINT UNSIGNED NULL
created_at DATETIME NOT NULL
updated_at DATETIME NOT NULL

FK (user_id) REFERENCES users(id) ON DELETE RESTRICT
FK (category_id) REFERENCES service_categories(id) ON DELETE SET NULL
FK (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL
FK (updated_by_user_id) REFERENCES users(id) ON DELETE SET NULL
INDEX (booking_date)
INDEX (status)
INDEX (user_id)
```

Notes:
- `booking_time` is required for all categories.
- `user_id` is required, so no guest bookings.

### 5) `booking_items`
Chosen services for each booking.

```sql
id BIGINT UNSIGNED PK AUTO_INCREMENT
booking_id BIGINT UNSIGNED NOT NULL
service_id BIGINT UNSIGNED NOT NULL
service_name_snapshot VARCHAR(150) NOT NULL
unit_price_snapshot DECIMAL(10,2) NOT NULL
quantity INT NOT NULL DEFAULT 1
line_total DECIMAL(10,2) NOT NULL
created_at DATETIME NOT NULL
updated_at DATETIME NOT NULL

FK (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
FK (service_id) REFERENCES services(id) ON DELETE RESTRICT
INDEX (booking_id)
UNIQUE (booking_id, service_id)
```

Notes:
- Snapshot fields keep historical price/name even if service catalog changes later.

### 6) `booking_status_logs` (optional but recommended)
Audit trail for booking status updates.

```sql
id BIGINT UNSIGNED PK AUTO_INCREMENT
booking_id BIGINT UNSIGNED NOT NULL
old_status ENUM('pending','confirmed','completed','cancelled') NULL
new_status ENUM('pending','confirmed','completed','cancelled') NOT NULL
changed_by_user_id BIGINT UNSIGNED NULL
change_note VARCHAR(255) NULL
created_at DATETIME NOT NULL

FK (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
FK (changed_by_user_id) REFERENCES users(id) ON DELETE SET NULL
INDEX (booking_id)
INDEX (created_at)
```

## Removed payment-related data

No payment tables now:
- no `payments`
- no `payment_transactions`
- no `invoices`

Admin can still track operational booking status and total amount.

## High-level relationships

- `users` 1 --- many `bookings` (booker)
- `users` 1 --- many `bookings` (created/updated by staff or self)
- `service_categories` 1 --- many `services`
- `service_categories` 1 --- many `bookings` (optional category summary on booking)
- `bookings` 1 --- many `booking_items`
- `services` 1 --- many `booking_items`
- `bookings` 1 --- many `booking_status_logs`
- `users` 1 --- many `booking_status_logs` (changed by)

## Confirmed decisions reflected in this draft

- admin and booking users are merged into one `users` table
- `clients` table removed
- guest booking removed (registered users only)
- `booking_time` is required for all bookings
