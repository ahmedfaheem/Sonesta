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
- Permissions for dashboard, managers, receptionists, clients, floors, rooms, and reservations
- Default admin email: `admin@admin.com`
- Default admin password: `123456`
- Default admin role: `admin`

The main seeder calls:

- `RoleSeeder`
- `AdminSeeder`
- `PermissionSeeder`

`PermissionSeeder` also assigns the default permission sets for:

- `admin`: all permissions
- `manager`: dashboard, receptionist management, floor management, and room management
- `receptionist`: dashboard, client access, client approval, and reservation viewing
- `client`: room viewing and reservation creation

## Project Structure

- `app/` models, middleware, and controllers
- `bootstrap/` app bootstrap and middleware aliases
- `config/` package and framework configuration
- `database/migrations/` schema history
- `database/seeders/` roles, permissions, and admin seed data
- `resources/js/` Inertia pages, Vue components, and layouts
- `routes/` web and auth routes

## Setup & Installation

You can choose to set up the project either manually on your local machine or using Docker.

### Option 1: Manual Setup

#### 1. Install dependencies

```bash
composer install
npm install
```

#### 2. Create environment file

```bash
cp .env.example .env
```

#### 3. Generate the app key

```bash
php artisan key:generate
```

#### 4. Prepare the database

The project is configured for SQLite by default. Make sure the database file exists:

```bash
touch database/database.sqlite
```

If you want to use another database, update `.env` before running migrations.

#### 5. Clear cached config

This is useful after pulling package/config changes:

```bash
php artisan config:clear
php artisan cache:clear
```

#### 6. Link public storage for uploaded images

```bash
php artisan storage:link
```

#### 7. Run migrations and seed all default data

For a fresh local install:

```bash
php artisan migrate:fresh --seed
```

#### 8. Start the development servers

Start the full development workflow:

```bash
composer run dev
```

This runs:

- Laravel development server
- Queue listener
- Laravel Pail
- Vite dev server

Alternative (separate terminals):

```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

---

### Option 2: Docker Setup

The project includes a `Dockerfile` and `docker-compose.yml` for an easy containerized setup.

#### 1. Build and start the containers

```bash
docker compose up -d --build
```

This now starts the web, app, queue, and scheduler services.

#### 2. Install dependencies inside the container

```bash
docker compose exec app composer install
docker compose exec app npm install
```

#### 3. Setup environment and project

```bash
docker compose exec app cp .env.example .env
docker compose exec app php artisan key:generate
docker compose exec app touch database/database.sqlite
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app npm run build
```

#### 4. Access the application

The application will be available at `http://localhost:8000`.

#### 5. Useful Docker Commands

- **Stop containers**: `docker compose down`
- **View logs**: `docker compose logs -f`
- **Run artisan commands**: `docker compose exec app php artisan <command>`
- **Run npm commands**: `docker compose exec app npm <command>`
- **Run Vite dev server**: `docker compose exec app npm run dev`

## Seeding Commands

Seed everything from `DatabaseSeeder`:

```bash
php artisan db:seed
```

Seed roles only:

```bash
php artisan db:seed --class=RoleSeeder
```

Seed permissions and role permission mappings only:

```bash
php artisan db:seed --class=PermissionSeeder
```

Seed the admin account only:

```bash
php artisan db:seed --class=AdminSeeder
```

If you seed `AdminSeeder` by itself, make sure roles already exist first.
If you seed `PermissionSeeder` by itself, it will create the default roles again if they do not already exist.

## Testing

Run tests with:

```bash
composer test
```

## API Auth (Sanctum) - Run & Use

### 1. Ensure Sanctum prerequisites

```bash
php artisan migrate
php artisan optimize:clear
```

Make sure `.env` has:

```env
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:*,127.0.0.1,127.0.0.1:*,your-domain.com,app.your-domain.com
SESSION_DOMAIN=null
```

### 2. Start the app

```bash
composer run dev
```

### 3. Create a token (authenticated user)

Endpoint:

- `POST /api/tokens`

Payload:

```json
{
  "name": "dashboard-client",
  "abilities": ["*"]
}
```

Example:

```bash
curl -X POST http://127.0.0.1:8001/api/tokens \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_EXISTING_TOKEN_OR_USE_SESSION_COOKIE" \
  -d '{"name":"dashboard-client","abilities":["*"]}'
```

### 4. Use token on protected API routes

Example:

```bash
curl http://127.0.0.1:8001/api/rooms \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_PLAIN_TEXT_TOKEN"
```

Protected routes:

- `GET /api/rooms`
- `GET /api/analytics/revenue`
- `GET /api/analytics/top-clients`
- `GET /api/analytics/reservations-by-country`
- `GET /api/analytics/gender-ratio`

Notes:

- All API routes use `auth:sanctum`.
- Analytics routes also require role: `admin|manager|receptionist`.
- Banned users are blocked by `check.banned` middleware.

### 5. Who can create and use tokens

- Any authenticated user can create tokens from:
  - `POST /api/tokens`
  - Profile page (`/profile`) via the **API Tokens** card
- Token creation is throttled (`10` requests per minute).
- Token authentication does not bypass role checks.

### 6. API visibility by role

- `admin`:
  - Can access `/api/rooms`
  - Can access all `/api/analytics/*`
- `manager`:
  - Can access `/api/rooms`
  - Can access all `/api/analytics/*`
- `receptionist`:
  - Can access `/api/rooms`
  - Can access all `/api/analytics/*`
- `client`:
  - Can access `/api/rooms`
  - Cannot access `/api/analytics/*` (403)

## Ownership & Data Access Rules

- `Floor::scopeVisibleTo($user)`:
  - `admin` sees all floors
  - `manager` sees only floors where `manager_id = auth()->id()`
- `Room::scopeVisibleTo($user)`:
  - `admin` sees all rooms
  - `manager` sees only rooms where `manager_id = auth()->id()`

Controllers using `->visibleTo(auth()->user())`:

- `app/Http/Controllers/Manager/FloorController.php` (index)
- `app/Http/Controllers/Manager/RoomController.php` (index, floor options, floor lookup in store/update)

This prevents managers from viewing or assigning floors/rooms owned by other managers.

## Authorization (Policies)

Policy-driven access is enforced for user/client/receptionist management.

- `admin`:
  - Full access (handled by `UserPolicy::before()`).
- `manager`:
  - Can only view/edit/delete own receptionists and clients (`created_by = auth()->id()`).
- `receptionist`:
  - Limited to pending/approved client views and client approval actions.

Key policy methods in `app/Policies/UserPolicy.php`:

- `viewReceptionist`, `updateReceptionist`, `deleteReceptionist`, `banReceptionist`
- `viewClient`, `updateClient`, `deleteClient`
- `viewPendingClients`, `viewApprovedClients`, `approveClient`

Enforcement points:

- `app/Http/Controllers/Admin/UserManagementController.php`
  - uses policy authorization on show/edit/update/destroy flows
  - scopes manager queries to owned records for `client` and `receptionist`
- `routes/web.php`
  - receptionist routes use `can:viewPendingClients`, `can:viewApprovedClients`, `can:approveClient`
  - manager resources use `can:viewAnyReceptionists` and `can:viewAnyClients`

Result:

- Managers cannot edit/delete/view other managers' users.
- Receptionists cannot perform manager/admin user CRUD actions.
- Admin retains full access.

## Approval vs Ban Logic

The system now separates client approval from staff ban state:

- `is_approved`:
  - Used for **clients only** (approval workflow).
  - Unapproved clients can login but are redirected to `/pending-approval`.
- `is_banned`:
  - Used for **staff accounts** (`admin`, `manager`, `receptionist`).
  - Banned staff are blocked by `check.banned` middleware.

Login flow:

- Authentication is allowed for valid credentials.
- Redirect logic sends unapproved clients to `/pending-approval`.
- Staff ban checks are enforced in middleware, not in login request validation.

Implementation references:

- `app/Http/Requests/Auth/LoginRequest.php`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Middleware/CheckIfBanned.php`
- `app/Http/Controllers/Admin/UserManagementController.php`
- `app/Http/Controllers/Admin/ManagerController.php`
- `database/migrations/2026_04_01_220000_add_is_banned_to_users_table.php`

## Notes

- `resources/js/Pages/Admin/Dashboard.vue` and `resources/js/Pages/Manager/Dashboard.vue` exist as dashboard entry pages
- `RoleMiddleware` is implemented in `app/Http/Middleware/RoleMiddleware.php`
- Registration currently assigns the `client` role, so role and permission seed data should exist before using the registration flow
- Stripe, ban logic, countries, and charts are installed, but their business flows are not fully built out yet

## License

This project is open-sourced under the MIT license.
