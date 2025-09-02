Run migrations and clear cache:

- php artisan migrate
- php artisan optimize:clear

If using storage public disk for wallpapers:
- php artisan storage:link

Seed/admin note:
- Ensure at least one admin user exists (user_type = 1). Only admin can POST/PATCH/DELETE /wallpapers.
