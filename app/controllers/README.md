# Controllers Guide

This folder is now simplified to 3 main controllers:

- `app/controllers/public/publicController.php`
- `app/controllers/user/userController.php`
- `app/controllers/admin/adminController.php`

Shared logic lives here:
- `app/controllers/shared/homeActionHandler.php` (public + user behavior)
- `app/controllers/shared/adminActionHandler.php` (admin behavior)
- `app/controllers/shared/baseController.php` (render/redirect/flash/role helpers)

How request flow works:
1. URL is matched in `public/index.php`.
2. It calls one method on `PublicController`, `UserController`, or `AdminController`.
3. Those methods call shared handler logic.
4. Shared handlers call models in `app/models/shared/*` and render views.

Rule of thumb:
- Start with route in `public/index.php`.
- Then open role controller method.
- Then open matching shared handler method.
