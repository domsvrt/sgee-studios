# Controllers Guide

The controller layer is split by role:

- `app/controllers/public/publicController.php`
- `app/controllers/user/userController.php`
- `app/controllers/admin/adminController.php`

Each role controller stays thin and delegates to shared behavior.

Shared controller pieces:

- `app/controllers/shared/baseController.php`
  - Base render, layout, redirect, flash, and role-check helpers.
- `app/controllers/shared/homeActionHandler.php`
  - Core public/user actions (book now, profile/avatar, notifications, settings, my bookings).
- `app/controllers/shared/homeViewSupportTrait.php`
  - Shared home/user view composition helpers.
- `app/controllers/shared/adminActionHandler.php`
  - Core admin actions (dashboard, users, categories, services, bookings, logs, analytics).
- `app/controllers/shared/adminPayloadTrait.php`
  - Admin request payload parsing and validation helpers.
- `app/controllers/shared/adminGuardTrait.php`
  - Admin role guard + standardized action error/success handling.

## Request flow

1. Route is matched in `public/index.php`.
2. Router dispatches to `PublicController`, `UserController`, or `AdminController`.
3. Role controller method forwards to shared handler/trait logic.
4. Shared handler calls models in `app/models/shared/*`.
5. Controller renders a view in `app/views/*` (often via layout wrappers).

## Where to debug quickly

1. Start at route in `public/index.php`.
2. Open the matching role controller method.
3. Follow into `homeActionHandler.php` or `adminActionHandler.php`.
4. Check payload parsing in `adminPayloadTrait.php` (for admin POST actions).
5. Check model queries in `app/models/shared/*`.
