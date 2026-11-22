# Bright-Market Point-of-Sale (POS) System — Laravel 13

[![Live Demo](https://img.shields.io/badge/Live_Demo-final--pos.freehosting.dev-133928?style=flat-square&logo=googlechrome&logoColor=white)](https://final-pos.freehosting.dev)
[![CI](https://img.shields.io/github/actions/workflow/status/aniciete/it0049-final-pos/ci.yml?branch=main&style=flat-square&logo=githubactions&logoColor=white&label=CI)](https://github.com/aniciete/it0049-final-pos/actions/workflows/ci.yml)
[![Last Commit](https://img.shields.io/github/last-commit/aniciete/it0049-final-pos?style=flat-square&color=133928&label=Updated)](https://github.com/aniciete/it0049-final-pos/commits/main)
[![Laravel 13](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php&logoColor=white)](composer.json)
[![Database](https://img.shields.io/badge/Database-SQLite%20%7C%20MySQL-005C84?style=flat-square&logo=mysql&logoColor=white)](database/pos_final.sql)
[![Vite 8](https://img.shields.io/badge/Vite-8.x-646CFF?style=flat-square&logo=vite&logoColor=white)](package.json)
[![Code Style](https://img.shields.io/badge/Code_Style-Laravel_Pint-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com/docs/pint)

**Course:** IT0049 — Web System Technologies  
**Repository:** [github.com/aniciete/it0049-final-pos](https://github.com/aniciete/it0049-final-pos)  
**Live Deployment:** [final-pos.freehosting.dev](https://final-pos.freehosting.dev)  
**Framework:** Laravel 13 (PHP 8.3+)  
**Authentication:** Laravel Breeze + `auth` middleware  
**Image Processing & Storage:** Intervention Image (`intervention/image-laravel`) on `Storage::disk('public')`  
**Database Compatibility:** SQLite (Local Development & PHPUnit Tests) and MySQL / MariaDB (Production & phpMyAdmin)

---

## 1. Project Overview

Bright-Market POS is a full-stack Point-of-Sale and Inventory Management application built strictly with Laravel MVC conventions. Authenticated staff operators can manage catalog products (with image uploads and soft-delete archiving), maintain customer and staff accounts (with avatars and bcrypt-hashed passwords), process counter sales inside atomic database transactions with strict stock protection, and inspect chronological sales history.

### Application Routes

| URI Pattern | HTTP Method(s) | Controller / Handler | Middleware | Purpose |
| :--- | :--- | :--- | :--- | :--- |
| `/` | `GET` | `PageController@home` | Public | Operational overview with live catalog, customer, staff, and sales counts |
| `/about` | `GET` | `PageController@about` | Public | 4-table relational schema and security architecture overview |
| `/login`, `/register` | `GET`, `POST` | Breeze Auth Controllers | `guest` | Staff sign-in (username or email) & account registration |
| `/forgot-password`, `/reset-password` | `GET`, `POST` | Breeze Password Reset | `guest` | Password recovery flow |
| `/dashboard` | `GET` | `PageController@dashboard` | `auth` | Authenticated KPI dashboard and recent transactions |
| `/products` (`Route::resource`) | `GET/POST/PUT/PATCH/DELETE` | `ProductController` | `auth` | Product CRUD, image upload/preview, soft-delete archive & purge |
| `/products/{product}/restore` | `POST` | `ProductController@restore` | `auth` | Restore archived product back to active inventory |
| `/customers` (`Route::resource`) | `GET/POST/PUT/PATCH/DELETE` | `CustomerController` | `auth` | Customer directory CRUD (`sales.customer_id` preserved via `SET NULL`) |
| `/users` (`Route::resource`) | `GET/POST/PUT/PATCH/DELETE` | `UserController` | `auth` | Staff accounts CRUD, 256&times;256 avatars, hashed passwords, deletion guards |
| `/sales` | `GET` | `SaleController@index` | `auth` | Chronological sales history ledger with search &amp; date filters |
| `/sales/create` | `GET` | `SaleController@create` | `auth` | Checkout register form with live order summary preview |
| `/sales` | `POST` | `SaleController@store` | `auth` | Atomic transactional sale recording &amp; stock deduction |
| `/sales/{sale}` | `GET` | `SaleController@show` | `auth` | Single transaction receipt breakdown view |
| `/profile` | `GET/PATCH/DELETE` | `ProfileController` | `auth` | Personal profile and password management |
| `/media/{folder}/{filename}` | `GET` | Closure (`Storage::disk('public')`) | `auth` | Serves stored `avatars` and `products` images when symlink is unavailable |
| `/media/{folder}/thumbs/{filename}` | `GET` | Closure (`Storage::disk('public')`) | `auth` | Serves cached 96&times;96 product list thumbnails |

---

## 2. Database Schema & Relationships

Migrations are located in `database/migrations/`, seeders in `database/seeders/`, and a standalone MySQL DDL/DML export in [`database/pos_final.sql`](database/pos_final.sql).

- **`products`**: `id`, `name`, `price` (`decimal(10,2)`), `stock_quantity`, `image` (nullable), `timestamps`, `deleted_at` (`SoftDeletes`). One product may appear in many sales (`Product::sales()`).
- **`customers`**: `id`, `full_name`, `email` (unique), `phone` (nullable), `timestamps`. Customer selection is optional during a sale (`Customer::sales()`).
- **`users`**: `id`, `username` (unique), `full_name`, `email` (nullable, unique), `email_verified_at`, `password` (bcrypt hashed), `role`, `avatar` (nullable), `remember_token`, `timestamps`. One staff user records many sales (`User::sales()`).
- **`sales`**: `id`, `product_id` (`FK -> products.id RESTRICT`), `customer_id` (`nullable FK -> customers.id SET NULL`), `sold_by` (`FK -> users.id RESTRICT`), `quantity`, `total_price` (`decimal(10,2)`), `timestamps`.

---

## 3. Local Development Setup

### Prerequisites
- PHP **8.3+** (`pdo_sqlite`, `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`)
- Composer **2.x** & Node.js / npm

### Quickstart
```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Open **[http://127.0.0.1:8000](http://127.0.0.1:8000)** and sign in at `/login` with any seeded staff account:
- **Username or Email:** `admin.reyes` or `admin.reyes@brightmarket.ph`
- **Password:** `POSDemo!2026`

---

## 4. Security, Validation & Transactional Integrity

- **Server-Side Validation**: Dedicated Form Requests (`SaveProductRequest`, `SaveCustomerRequest`, `SaveUserRequest`, `StoreSaleRequest`, `Auth\LoginRequest`) trim and validate all inputs before database writes.
- **Transactional Stock Protection (`App\Services\SaleService`)**: Executes inside `DB::transaction()`, checks product active state, runs a conditional atomic `UPDATE` (`stock_quantity >= quantity`), computes `total_price` server-side, and rejects overselling with zero inventory mutation or partial sale rows.
- **Safe Media Uploads (`App\Services\ImageUploadService`)**: Restricts uploads to JPG/PNG/WebP (&le; 2 MB), decodes bytes via Intervention Image, resizes cleanly (`640x640` products, `256x256` avatars), stores under `Storage::disk('public')`, and cleans up replaced images only after database persistence succeeds.
- **Authentication, CSRF & Hashing**: Breeze session authentication, `auth` middleware on all POS modules, `@csrf` tokens on all mutating forms, and Laravel `Hash::make()` / `'hashed'` model casts.

---

## 5. Automated Testing & Packaging

Run the full test suite and Laravel Pint code style check:
```bash
php artisan test
./vendor/bin/pint --test
```

Generate a clean Canvas submission ZIP (excluding `node_modules`, `vendor`, `.git`, and local `.env` secrets):
```bash
./build-submission-zip.sh
```

Build and upload a lean InfinityFree deployment archive using gitignored `.deploy.env` and `.env.production`:
```bash
./build-infinityfree-zip.sh
./deploy-infinityfree.sh
```
