FITTRACK REDESIGNED PHP + MYSQL PROJECT
======================================

REQUIREMENTS
- XAMPP with Apache and MySQL
- PHP with PDO MySQL enabled
- Your existing MySQL database named `fittrack`

INSTALL
1. Back up your existing folder:
   C:\xampp\htdocs\FitTrack
2. Extract this ZIP into a NEW folder first, then copy/merge the files into:
   C:\xampp\htdocs\FitTrack
   Do not overwrite your database or SQL data.
3. Keep your database name and connection credentials in config/database.php.
   Default values in this package are host 127.0.0.1, database fittrack,
   username root, empty password. Change them if yours differ.
4. Open config/app.php and change ADMIN_REGISTRATION_CODE to a private,
   long value before registering an admin.
5. Start Apache and MySQL in XAMPP.
6. Visit http://localhost/FitTrack/index.php

FIRST PAGE
- index.php is the first page and lets visitors choose User or Admin.
- User registration inserts role='user'.
- Admin registration inserts role='admin' only when the private admin code is correct.
- Sign-in checks the role stored in the users table, not just the URL.
- A user cannot open admin pages by changing the URL.

IMPORTANT DATABASE NOTES
This code matches the uploaded fittrack_database.sql schema:
- users.role ENUM('user','admin')
- users.password_hash
- workout_sessions.title and workout_sessions.workout_date
- body_measurements.measured_on
- fitness_goals.target_value/current_value/unit/target_date/status
- exercises name/category/muscle_group/difficulty/equipment/instructions

The code does not drop, recreate, or import tables automatically. Keep your existing
database and data. Back up your project before replacing any existing files.

INCLUDED PAGES
- index.php: choose User or Admin
- login.php / register.php: role-specific sign-in and registration
- dashboard.php: user dashboard
- admin/dashboard.php: admin overview
- admin/users.php: list and delete regular user accounts (admin accounts are protected)
- admin/exercises.php: add/list/delete exercises
- logout.php: end the current session
- assets/css/style.css: responsive visual design

OTHER PAGE LINKS
The user sidebar contains links to workouts.php, workout_plans.php,
body_measurements.php, goals.php, nutrition.php, and water.php. These pages
are not implemented in this starter package unless they already exist in your
current FitTrack folder. Keep your existing versions or ask to have them built.


Subscription feature:
- User panel: Subscription page with 1-month, 3-month, and 12-month plans.
- Admin panel: Manage users shows subscription status and expiry date; admins can activate/renew or set a user to Normal.
- No payment gateway is connected; admin approval activates a plan.
- Back up your database, then import database/subscriptions_migration.sql into the existing fittrack database in phpMyAdmin. Run once only.
