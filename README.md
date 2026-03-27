# Sonesta

Sonesta is a Laravel 13 hotel management foundation built with Inertia.js and Vue 3. The project currently combines Breeze authentication with role-based dashboards, permission management, Sanctum support, and the first set of hotel domain tables for floors, rooms, clients, and reservations.

## Stack

- PHP 8.3+
- Laravel 13
- Inertia.js 2
- Vue 3
- Vite 8
- Tailwind CSS
- SQLite for local development by default
- Chart.js on the frontend

## Installed Packages

- `laravel/breeze`
- `spatie/laravel-permission`
- `laravel/sanctum`
- `cybercog/laravel-ban`
- `rinvex/countries`
- `stripe/stripe-php`
- `chart.js`

## Current Application State

### Authentication and Access

- Login, registration, password reset, email verification, and profile management are available
- Users with the `admin` role are redirected after login to `/admin/dashboard`
- Users with the `manager` role are redirected after login to `/manager/dashboard`
- Users without those roles are redirected after login to `/dashboard`
- Route protection uses the custom `role` middleware alias

### Domain Models

The database currently includes:

- `users`
- `roles`, `permissions`, and related permission pivot tables
- `personal_access_tokens`
- `floors`
- `rooms`
- `clients`
- `reservations`

### User-Related Data

The `users` table also includes:

- `national_id`
- `avatar`
- `created_by`

## Seeded Data

Running the main database seeder currently creates:

- Role: `admin`
- Role: `manager`
- Role: `receptionist`
- Role: `client`
- Default admin email: `admin@admin.com`
- Default admin password: `123456`
- Default admin role: `admin`

The main seeder calls:

- `RoleSeeder`
- `AdminSeeder`

## Project Structure

- `app/` models, middleware, and controllers
- `bootstrap/` app bootstrap and middleware aliases
- `config/` package and framework configuration
- `database/migrations/` schema history
- `database/seeders/` roles and admin seed data
- `resources/js/` Inertia pages, Vue components, and layouts
- `routes/` web and auth routes

## Installation

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Create environment file

```bash
cp .env.example .env
```

### 3. Generate the app key

```bash
php artisan key:generate
```

### 4. Prepare the database

The project is configured for SQLite by default. Make sure the database file exists:

```bash
touch database/database.sqlite
```

If you want to use another database, update `.env` before running migrations.

### 5. Clear cached config

This is useful after pulling package/config changes:

```bash
php artisan config:clear
php artisan cache:clear
```

### 6. Run migrations and seed all default data

For a fresh local install:

```bash
php artisan migrate:fresh --seed
```

If your database is already empty and you only need normal migration flow:

```bash
php artisan migrate --seed
```

## Running the Project

Start the full development workflow:

```bash
composer run dev
```

This runs:

- Laravel development server
- queue listener
- Laravel Pail
- Vite dev server

Frontend only:

```bash
npm run dev
```

Production build:

```bash
npm run build
```

## Seeding Commands

Seed everything from `DatabaseSeeder`:

```bash
php artisan db:seed
```

Seed roles only:

```bash
php artisan db:seed --class=RoleSeeder
```

Seed the admin account only:

```bash
php artisan db:seed --class=AdminSeeder
```

If you seed `AdminSeeder` by itself, make sure roles already exist first.

## Testing

Run tests with:

```bash
composer test
```

## Notes

- `resources/js/Pages/Admin/Dashboard.vue` and `resources/js/Pages/Manager/Dashboard.vue` exist as dashboard entry pages
- `RoleMiddleware` is implemented in `app/Http/Middleware/RoleMiddleware.php`
- Registration currently assigns the `client` role, so role seed data should exist before using the registration flow
- Stripe, ban logic, countries, and charts are installed, but their business flows are not fully built out yet

## License

This project is open-sourced under the MIT license.
