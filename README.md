# EasyColoc App

Laravel 11 application for shared housing management:
- colocations and memberships
- invitation flow (accept/refuse)
- expenses and categories
- balances and simplified debts
- settlements (mark paid)
- reputation rules
- global admin dashboard with ban/unban

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+ and npm
- MySQL (or SQLite for local quick run)

## Installation

1. Install PHP dependencies:

```bash
composer install
```

2. Install frontend dependencies:

```bash
npm install
```

3. Copy environment file:

```bash
cp .env.example .env
```

4. Generate app key:

```bash
php artisan key:generate
```

## Environment Setup

Configure database in `.env`.

Example MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

For invitations in local development, keep mail driver as log:

```env
MAIL_MAILER=log
```

## Database + Demo Data

Run migrations and seed demo data:

```bash
php artisan migrate:fresh --seed
```

Seeder used:
- `database/seeders/DemoSeeder.php`

Demo data includes:
- 1 global admin user
- 1 owner + 2 member users
- 1 active colocation with memberships
- categories
- expenses across 2 months
- at least 1 settlement record

## Run Application

```bash
php artisan serve
```

Open:
- `http://127.0.0.1:8000`

## Run Tests

```bash
php artisan test
```

## Demo Accounts

All seeded users use password:
- `password`

Seeded emails:
- `admin@easycoloc.test` (admin)
- `owner@easycoloc.test`
- `member1@easycoloc.test`
- `member2@easycoloc.test`

## Basic Usage Walkthrough

1. Register/login.
2. Create or access a colocation.
3. Invite members by email from colocation page.
4. Accept/refuse invitations from invitation links.
5. Manage categories (owner only).
6. Add expenses (active members).
7. Filter expenses by month on colocation page.
8. View balances and "who owes who".
9. Use "Mark paid" to create settlements and reduce debts.
10. Admin features:
   - open `/admin/dashboard` for global stats
   - open `/admin/users` to ban/unban users

## Admin Dashboard Stats

Dashboard shows:
- total users
- total colocations
- total expenses
- total banned users
- total active colocations

## Edge Cases Checklist

- One active colocation rule is enforced.
- Invitation email mismatch is blocked.
- Expired invitation is blocked.
- Banned users are logged out and blocked from protected routes.
- Removing a member with debt imputes debt to owner via internal settlement.
- Cancelled colocations block lifecycle actions.
