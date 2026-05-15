# Models (Beginner Guide)

This folder is now intentionally simple:

- `shared/` = all real model/database logic.

If you need to change how data is queried or saved, edit files in:
- `app/models/shared/*`

Quick trace:
1. Find route in `public/index.php`.
2. Open controller method (`app/controllers/public/publicController.php`, `app/controllers/user/userController.php`, or `app/controllers/admin/adminController.php`).
3. Follow into shared handler (`app/controllers/shared/*`).
4. Open model in `app/models/shared/*`.
