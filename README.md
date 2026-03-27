# Sonesta

Sonesta is a Laravel 13 application using Inertia.js and Vue 3. The current codebase is a clean starter foundation with authentication, email verification, profile management, and a Vite-powered frontend already configured.

## Stack

- PHP 8.3
- Laravel 13
- Inertia.js 2
- Vue 3
- Vite 8
- Tailwind CSS
- SQLite by default for local development

## Current Features

- User registration and login
- Password reset flow
- Email verification
- Auth-protected dashboard
- Profile update and account deletion
- Seeded test user for local development

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

- The app currently uses the standard Breeze + Inertia Vue structure as a foundation.
- The landing page is `resources/js/Pages/Welcome.vue`.
- The authenticated home page is `resources/js/Pages/Dashboard.vue`.

## License

This project is open-sourced under the MIT license.
