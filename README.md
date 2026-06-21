# Zikola Intern — E-Commerce REST API

A RESTful e-commerce API built with **Laravel 13** and **JWT authentication**. Developed as part of the Zikola internship program, it covers the full backend for a product catalog, order management, user profiles, and reviews.

## Features

- **JWT Authentication** — Register, login, logout, and get current user via `php-open-source-saver/jwt-auth`
- **Email Verification** — OTP-based email verification on registration (6-digit code, 10-min expiry)
- **Password Reset** — Forgot-password / reset-password flow using email OTP
- **Product Management** — Full CRUD with image uploads, category assignment, stock restocking, full-text search, and pagination
- **Category Management** — Create, list, show, and delete categories; batch-attach products to a category
- **Order Management** — Place orders, update status (admin), soft-delete, view trashed orders, and filter expensive orders (> $500)
- **Reviews** — Polymorphic reviews with validated ratings on products
- **User Profiles** — Create, update, and delete user profiles (avatar, bio, etc.)
- **Admin User Management** — Admin-only CRUD for managing users
- **Authorization** — Role-based middleware (`IsAdmin`, `EmailIsVerified`) and policies (`OrderPolicy`, `ProfilePolicy`)
- **Notifications** — Order status change notifications
- **API Resources** — Consistent JSON responses via Eloquent API Resources
- **Rate Limiting** — Throttled login endpoint

## Tech Stack

| Layer         | Technology                       |
|---------------|----------------------------------|
| Framework     | Laravel 13                       |
| PHP           | 8.3+                             |
| Auth          | JWT (`php-open-source-saver/jwt-auth`) |
| Database      | MySQL                            |


## Prerequisites

- PHP ≥ 8.3
- Composer
- MySQL

## Getting Started

### 1. Clone the repository

```bash
git clone https://github.com/3ayary/zikola-intern.git
cd zikola-intern
```

### 2. Quick setup (recommended)

```bash
composer run setup
```

This will install PHP & JS dependencies, copy `.env.example` to `.env`, generate an app key, run migrations, and build frontend assets.

### 3. Manual setup

```bash
# Install dependencies
composer install

# Environment
cp .env.example .env
php artisan key:generate

# Configure your database in .env
# DB_DATABASE=zikola_intern
# DB_USERNAME=root
# DB_PASSWORD=

# Publish JWT secret
php artisan jwt:secret

# Run migrations
php artisan migrate


```

### 4. Start the development server

```bash
composer run dev
```

This launches the Laravel server, queue worker, Pail log viewer, and Vite dev server concurrently.

## API Endpoints

All routes are prefixed with `/api`.

### Authentication

| Method | Endpoint              | Description              | Auth |
|--------|-----------------------|--------------------------|------|
| POST   | `/register`           | Register a new user      | ✗    |
| POST   | `/login`              | Login (rate-limited)     | ✗    |
| POST   | `/logout`             | Logout                   | ✓    |
| GET    | `/me`                 | Get current user         | ✓    |
| POST   | `/verify-email`       | Verify email via OTP     | ✓    |
| POST   | `/forgot-password`    | Send password reset OTP  | ✗    |
| POST   | `/reset-password`     | Reset password with OTP  | ✗    |

### Products

| Method | Endpoint                          | Description                     | Auth  |
|--------|-----------------------------------|---------------------------------|-------|
| GET    | `/products`                       | List products (search, paginate)| ✗     |
| GET    | `/products/{id}`                  | Show product with reviews       | ✗     |
| POST   | `/products`                       | Create product                  | Admin |
| PUT    | `/products/{id}`                  | Update product                  | Admin |
| DELETE | `/products/{id}`                  | Delete product                  | Admin |
| POST   | `/products/{category}/attach`     | Attach products to category     | Admin |
| POST   | `/products/{productId}/stock`     | Restock product                 | Admin |

### Orders

| Method | Endpoint               | Description               | Auth       |
|--------|------------------------|---------------------------|------------|
| GET    | `/order`               | List all orders            | Admin      |
| POST   | `/order`               | Place an order             | Verified   |
| DELETE | `/order/{id}`          | Cancel / delete order      | Verified   |
| PUT    | `/order/{id}/status`   | Update order status        | Admin      |
| GET    | `/order/expensive`     | List expensive orders      | Admin      |
| GET    | `/order/trash`         | List soft-deleted orders   | Admin      |

### Categories

| Method | Endpoint           | Description        | Auth  |
|--------|--------------------|--------------------|-------|
| GET    | `/category`        | List categories    | ✓     |
| GET    | `/category/{id}`   | Show category      | ✓     |
| POST   | `/category`        | Create category    | Admin |
| DELETE | `/category/{id}`   | Delete category    | Admin |

### Reviews

| Method | Endpoint                        | Description        | Auth     |
|--------|---------------------------------|--------------------|----------|
| POST   | `/products/{id}/reviews`        | Submit a review    | Verified |

### Profiles

| Method | Endpoint    | Description      | Auth     |
|--------|-------------|------------------|----------|
| POST   | `/profile`  | Create profile   | Verified |
| POST   | `/profile/update` | Update profile | Verified |
| DELETE | `/profile`  | Delete profile   | Verified |

### Users (Admin)

| Method | Endpoint       | Description    | Auth  |
|--------|----------------|----------------|-------|
| GET    | `/user`        | List users     | Admin |
| GET    | `/user/show`   | Show user      | Admin |
| POST   | `/user`        | Create user    | Admin |
| PUT    | `/user/{id}`   | Update user    | Admin |
| DELETE | `/user/{id}`   | Delete user    | Admin |

## Project Structure

```
app/
├── Http/
│   ├── Controllers/       # API controllers
│   ├── Middleware/         # IsAdmin, EmailIsVerified
│   ├── Requests/          # Form request validation
│   ├── Resources/         # Eloquent API resources
│   └── helpers/           # ApiResponse helper
├── Mail/                  # OtpMail mailable
├── Models/                # User, Product, Order, Category, Review, Profile, ProductImages
├── Notifications/         # OrderStatusChanged
├── Observers/             # OrderObserver
├── Policies/              # OrderPolicy, ProfilePolicy
├── Rules/                 # ValidRating
└── Services/              # orderServices (business logic)
database/
├── factories/
├── migrations/
└── seeders/
routes/
└── api.php                # All API route definitions
```
