# Sonesta

Sonesta is a Laravel 13 application using Inertia.js and Vue 3. The project currently includes the Breeze authentication foundation plus newly added infrastructure for roles and permissions, API token authentication, account banning, country data, Stripe integration, and chart rendering.

## Stack

- PHP 8.3
- Laravel 13
- Inertia.js 2
- Vue 3
- Vite 8
- Tailwind CSS
- Chart.js
- SQLite by default for local development

## Installed Addons

- `spatie/laravel-permission` for roles and permissions
- `laravel/sanctum` for personal access tokens and SPA/API authentication
- `cybercog/laravel-ban` for banning users or related models
- `rinvex/countries` for country reference data
- `stripe/stripe-php` for Stripe payment integration
- `chart.js` for frontend charts and data visualization

## Current Features

- User registration and login
- Password reset flow
- Email verification
- Auth-protected dashboard
- Profile update and account deletion
- Seeded test user for local development

## Integrated Infrastructure

- Permission package config published in `config/permission.php`
- Sanctum config published in `config/sanctum.php`
- Role and permission tables migration added
- Personal access tokens migration added
- Placeholder `RoleMiddleware` created for future authorization checks

These addons are installed in the codebase, but they are not yet wired into complete business flows everywhere in the app.

## Project Structure

- `app/` application logic and controllers
- `database/` migrations, seeders, factories, and local SQLite database
- `resources/js/` Inertia pages, Vue components, and layouts
- `resources/views/` Blade entry view for the Inertia app
- `routes/` web and authentication routes

## Requirements

- PHP 8.3+
- Composer
- Node.js and npm

## Local Setup

1. Install PHP dependencies:

```bash
composer install
```

2. Install frontend dependencies:

```bash
npm install
```

3. Create the environment file if needed:

```bash
cp .env.example .env
```

4. Generate the application key:

```bash
php artisan key:generate
```

5. Run migrations and seed the database:

```bash
php artisan migrate --seed
```

If package configs were cached before these addons were installed, clear them once:

```bash
php artisan config:clear
```

## Run The App

For the full local development workflow:

```bash
composer run dev
```

This starts:

- Laravel development server
- Queue listener
- Laravel Pail log viewer
- Vite dev server

If you only want the frontend asset server:

```bash
npm run dev
```

To build production assets:

```bash
npm run build
```

## Test Account

The default database seeder creates:

- Email: `test@example.com`
- Password: `password`

## Testing

Run the test suite with:

```bash
composer test
```

## Notes

- The app currently uses the standard Breeze + Inertia Vue structure as its base.
- The landing page is `resources/js/Pages/Welcome.vue`.
- The authenticated home page is `resources/js/Pages/Dashboard.vue`.
- Recent database additions include permission tables and `personal_access_tokens`.
- The current `RoleMiddleware` exists in `app/Http/Middleware/RoleMiddleware.php`, but its authorization logic has not been implemented yet.

## License

This project is open-sourced under the MIT license.
