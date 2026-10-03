# FitTrack — PHP + MySQL Starter Project

A beginner-friendly fitness tracker for XAMPP using plain PHP, PDO, MySQL, HTML, CSS, and PHP sessions.

## Included
- Registration, login, logout, CSRF protection, session authentication
- Dashboard with workout and measurement summaries
- Exercise library (view/search; admin management)
- Workout plans and plan exercises
- Workout sessions and sets
- Fitness goals
- Body measurements and progress history
- Basic admin pages
- Responsive CSS
- Optional database schema in `database/schema.sql`

## Setup
1. Copy the `FitTrack` folder into `C:\xampp\htdocs\`.
2. Start Apache and MySQL in the XAMPP Control Panel.
3. Open phpMyAdmin and optionally import `database/schema.sql` to create the tables.
4. Edit `config/database.php` to match your own MySQL credentials.
5. Open `http://localhost/FitTrack/`.
6. Register an account.

## Make an admin account
For safety, registration always creates a normal `user`. After registering, use phpMyAdmin to promote your own account:
```sql
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
```
Do not expose admin promotion in public registration.

## Notes
- This project is for local learning and should be reviewed before public deployment.
- The default database settings are the common local XAMPP settings only. Configure your own credentials.
- `secure => false` in the session cookie settings is only for local HTTP. Set it to `true` when using HTTPS.
- Add rate limiting, password reset, automated tests, and production deployment hardening before publishing.
- No Apache configuration is included; configure Apache as you prefer.


UPDATED PACKAGE NOTES
- Added body_measurements.php, nutrition.php, and water.php using the uploaded fittrack SQL schema.
- Profile editing now uses the actual users columns (age, gender, height_cm, weight_kg, fitness_level).
- Progress now redirects to Body Measurements.
- The actual imported schema uses workout_sessions.workout_date and body_measurements.measured_on. Back up your existing FitTrack folder before copying these files over it.
- Do not import database/schema.sql over your existing database; it represents an older/different schema. Use your exported fittrack database as the source of truth.


## Subscription feature (added)
- User panel: open **Subscription** to choose a 1-month, 3-month, or 12-month plan and submit a request.
- Admin panel: open **Manage users** to see Normal, Pending, Active, or Expired status and the subscription expiry date.
- Admins can approve/activate or renew a plan for a selected duration, or set the account back to Normal.
- This implementation does not take payments. Activation is confirmed manually by an administrator.

### Apply the subscription database migration
Back up your database first. In phpMyAdmin, select the existing `fittrack` database, open **Import**, and import `database/subscriptions_migration.sql`. Run this migration only once; it adds the subscription columns to the existing `users` table. Do not import the older `database/schema.sql` into your existing database.
