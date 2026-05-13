# SGee Studios Admin Interface Implementation Plan

## Summary

Build a PHP 8.4 MVC admin interface for managing the existing SGee Studios MySQL schema:

- `users`
- `service_categories`
- `services`
- `bookings`
- `booking_items`
- `booking_status_logs`

The admin UI must use Tailwind CSS for a modern, operational dashboard aesthetic. The interface should prioritize dense information, readable tables, clear status states, fast CRUD actions, and responsive behavior for desktop and mobile.

## Project Conventions

Use MVC structure throughout the implementation.

Directory naming:
- All new directories must use lowercase letters.
- Use kebab-case for multi-word directory names.
- Use one-word lowercase names when the directory name is a single word.
- Existing uppercase directories should be migrated to lowercase equivalents as part of the admin implementation.

File naming:
- All new project files must use camelCase.
- PHP class names may remain PascalCase inside files, but filenames should be camelCase.
- Example: `adminController.php` contains `class AdminController`.

Planned structure:

```text
app/
  controllers/
    homeController.php
    adminController.php
  database/
    databaseConnection.php
  models/
    userModel.php
    serviceCategoryModel.php
    serviceModel.php
    bookingModel.php
    bookingItemModel.php
    bookingStatusLogModel.php
  views/
    home.php
    admin/
      layout.php
      dashboard.php
      users.php
      categories.php
      services.php
      bookings.php
      logs.php
public/
  index.php
```

## Tailwind Design Direction

Use Tailwind via CDN for the first admin pass to avoid adding a frontend build tool.

Admin visual system:
- Dark left sidebar with SGee Studios brand and section navigation.
- Light or warm-neutral main workspace for readability.
- Compact top bar with page title, filters, and primary action button.
- Tables with sticky headers, subtle row hover states, and responsive horizontal scroll.
- Status badges for `active`, `inactive`, `banned`, `pending`, `confirmed`, `completed`, and `cancelled`.
- Modal dialogs or slide-over panels for create/update forms.
- Use Tailwind utility classes directly in views.
- Use semantic form labels, accessible buttons, and visible validation errors.

Recommended Tailwind CDN include in `app/views/admin/layout.php`:

```html
<script src="https://cdn.tailwindcss.com/3.4.17"></script>
```

## Routes

Use path-based routing in `public/index.php`.

Read routes:

```text
/                 home placeholder
/admin            dashboard
/admin/users      users management
/admin/categories category management
/admin/services   service management
/admin/bookings   booking management
/admin/logs       booking status logs
```

Mutation routes:

```text
POST /admin/users/create
POST /admin/users/update
POST /admin/users/delete
POST /admin/categories/create
POST /admin/categories/update
POST /admin/categories/delete
POST /admin/services/create
POST /admin/services/update
POST /admin/services/delete
POST /admin/bookings/create
POST /admin/bookings/update
POST /admin/bookings/status
POST /admin/bookings/delete
```

Routing rules:
- Keep routing centralized in `public/index.php`.
- Match by request method and normalized path.
- Return `404` for unknown routes.
- Redirect after successful POST actions.

## MVC Implementation

### Controllers

Create `app/controllers/adminController.php`.

Responsibilities:
- Render all admin views.
- Validate request payloads.
- Call model methods for database reads/writes.
- Redirect after mutations.
- Pass page data into views.

Keep controller methods grouped by resource:
- dashboard
- users
- categories
- services
- bookings
- logs

### Models

Create one model per database area:

- `userModel.php`
- `serviceCategoryModel.php`
- `serviceModel.php`
- `bookingModel.php`
- `bookingItemModel.php`
- `bookingStatusLogModel.php`

Model rules:
- Use PDO prepared statements only.
- Return plain associative arrays.
- Keep SQL inside models, not views.
- Keep validation in controllers unless the validation protects model invariants.

### Views

Create admin views under `app/views/admin/`.

View rules:
- Use Tailwind CSS classes directly.
- Escape all dynamic output with `htmlspecialchars`.
- Keep database queries out of views.
- Use shared layout variables: `$title`, `$activeNav`, `$content`.

## Database Management Features

### Dashboard

Show real MySQL-backed metrics:
- Total users
- Total admins
- Active services
- Upcoming bookings
- Pending bookings
- Completed bookings

Show dashboard tables:
- Upcoming bookings ordered by `booking_date`, then `booking_time`.
- Recent booking status logs ordered newest first.
- Booking counts grouped by status.

### Users

Manage `users`.

Fields:
- `full_name`
- `email`
- `phone`
- `role`
- `admin_level`
- `status`
- `password`

Rules:
- Require `full_name`, `email`, `role`, and `status`.
- Require `password` on create.
- Make `password` optional on update.
- Hash passwords with `password_hash(..., PASSWORD_DEFAULT)`.
- Set `admin_level` to `NULL` when `role='user'`.
- Allow statuses only: `active`, `inactive`, `banned`.

### Categories

Manage `service_categories`.

Fields:
- `slug`
- `name`
- `description`
- `is_active`
- `sort_order`

Rules:
- Require `slug` and `name`.
- Keep `slug` unique.
- Normalize slugs to lowercase kebab-case or lowercase snake_case.
- Warn before delete because services cascade and bookings keep nullable `category_id`.

### Services

Manage `services`.

Fields:
- `category_id`
- `code`
- `name`
- `description`
- `price`
- `unit_label`
- `selection_type`
- `is_active`
- `sort_order`

Rules:
- Require `category_id`, `code`, `name`, and `price`.
- Keep `code` unique.
- Reject negative prices.
- Allow selection types only: `single`, `multiple`, `quantity`.

### Bookings

Manage `bookings` and `booking_items`.

Booking fields:
- `booking_code`
- `user_id`
- `category_id`
- `booking_date`
- `booking_time`
- `status`
- `notes`
- `total_amount`

Booking item fields:
- `service_id`
- `quantity`
- `service_name_snapshot`
- `unit_price_snapshot`
- `line_total`

Rules:
- Require `user_id`, `booking_date`, and `booking_time`.
- Do not support guest bookings.
- Always require `booking_time`.
- Generate `booking_code` if admin leaves it empty.
- Copy service name and price into snapshot fields when adding items.
- Calculate `line_total` from service price and quantity.
- Recalculate `bookings.total_amount` from booking items after item changes.
- Insert into `booking_status_logs` when status changes.

### Logs

Show `booking_status_logs` as a read-only table.

Display:
- Booking code
- Old status
- New status
- Changed by user
- Change note
- Created date

Sort newest first.

## Security and Validation

- Add basic session handling for admin access.
- Use a temporary development admin bypass only when no admin user exists.
- Escape view output with `htmlspecialchars`.
- Use prepared statements for every query.
- Validate every POST payload server-side.
- Use redirects after POST.
- Never reference removed `clients` tables.
- Never implement payment screens or payment tables.

## Acceptance Criteria

- `/admin` loads a Tailwind-styled dashboard with live database counts.
- Admin can manage users, categories, services, bookings, and status logs through MVC controllers/models/views.
- All new directories follow lowercase or kebab-case naming.
- All new project files use camelCase naming.
- Existing uppercase MVC directories are migrated to lowercase paths.
- Booking creation requires a registered user and a booking time.
- Booking status updates create audit rows.
- UI remains usable on mobile through responsive Tailwind layouts.

## Verification Plan

Run syntax checks:

```bash
docker compose exec -T app php -l public/index.php
docker compose exec -T app php -l app/controllers/adminController.php
docker compose exec -T app php -l app/database/databaseConnection.php
```

Run database checks:

```bash
docker compose exec -T db mysql -uroot -psgee_password -e "USE sgee_studios; SHOW TABLES;"
docker compose exec -T db mysql -uroot -psgee_password -e "USE sgee_studios; DESCRIBE bookings;"
```

Manual browser checks:
- Open `http://localhost:8080/admin`.
- Create an admin user and regular user.
- Create a category.
- Create a service under that category.
- Create a booking with user, date, and time.
- Change booking status and confirm the log appears.
- Resize to mobile width and confirm sidebar/navigation and tables remain usable.

## Out of Scope

- Payment management.
- Guest booking.
- User-facing booking flow.
- Email notifications.
- File or image uploads.
- Full permission matrix beyond admin vs user.
