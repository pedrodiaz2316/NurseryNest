# NurseryNest

A web platform that hosts Florida plant nurseries: each nursery gets a branded storefront and a dashboard for its inventory, and clients use one account to browse and order from all of them.

- Requirements: [docs/requirements.md](docs/requirements.md)
- Design: [docs/design.md](docs/design.md)

## Stack

PHP 8.3+, MySQL 8.0+, server-rendered templates with plain CSS and JavaScript. Composer is used for autoloading and PHPUnit.

## Local setup (Windows)

1. **Install PHP 8.3 and Composer** (skip if already installed):

   ```powershell
   winget install --id PHP.PHP.NTS.8.3 -e
   ```

   Then enable `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `intl` and `sodium` in `php.ini`, and install [Composer](https://getcomposer.org/download/).

2. **Install dependencies:**

   ```bash
   composer install
   ```

3. **Create the database** in MySQL Workbench, connected as an admin user such as `root`:
   1. Open `database/00_create_database.sql`, replace `CHANGE_ME_STRONG_PASSWORD` with a strong password, and run it.
   2. Run `database/01_schema.sql`.
   3. Optional, for demo data: run `database/02_seed_demo.sql`.

4. **Configure the app:** copy `.env.example` to `.env` and set `DB_PASS` to the password from step 3.

5. **Run it:**

   ```bash
   composer serve
   ```

   Open http://localhost:8000.

## Demo logins (from `02_seed_demo.sql`)

All demo accounts use the password `DemoPass!2026`.

| Where | Email | Role |
| --- | --- | --- |
| `/nursery/login` | manager@sunshinepalms.test | Nursery Manager |
| `/nursery/login` | office@sunshinepalms.test | Office Administrator |
| `/nursery/login` | inventory@sunshinepalms.test | Inventory Coordinator |
| `/nursery/login` | sales@sunshinepalms.test | Sales and Service Representative |
| `/nursery/login` | manager@gulfcoastgardens.test | Nursery Manager (second nursery) |
| `/login` | client@example.test | Client |

## Tests and checks

```bash
composer test        # PHPUnit
composer audit       # dependency security advisories (NFR-11)
```

## Project layout

```
config/routes.php        all URLs and the controller each one calls
database/                SQL: create database, schema, demo data
public/                  web root: index.php front controller, CSS, JS, images
src/Auth/                Policy (AUTH-9 permission matrix), nursery and client logins
src/Controller/          request handlers
src/Core/                router, database, session, CSRF, views
src/Repository/          data access; TenantRepository scopes every query by nursery_id
templates/               page templates and layouts (public, storefront, dashboard)
tests/                   PHPUnit tests
```

## Status

**Built (phase 1):** database schema, public marketing site, nursery storefronts with per-nursery themes, client sign-up, login and all-nursery browse, nursery login, and dashboard inventory management with role permissions.

**Next:** cart and checkout, payments (Stripe, PayPal, Square), sales tax, shipping restrictions, orders, nursery users/staff/settings, password reset, and the platform admin area.
