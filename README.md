# Sari-Sari Store POS System

A Point-of-Sale and inventory management system for a small sari-sari (retail) store, built for **WMA4 – Advanced Web Development** (Tarlac State University).

**Assigned System Type:** Point-of-Sale (POS) / Sales Management System

## Midterm Phase (Current)

Built with native PHP (OOP) and MySQL (PDO, prepared statements). No framework yet — Laravel integration, authentication/RBAC, and the REST API are Final Term work.

### Features
- Product, category, and supplier management (full CRUD)
- Searchable, filterable, sortable product listing (`WHERE`, `ORDER BY`, `LIMIT`)
- Point-of-sale transaction screen — cart-based checkout with server-side stock validation
- Sales history and receipt view
- Dashboard with today's sales total, transaction count, and low-stock alerts
- OOP core: abstract `Model` base class; `Product`, `Category`, `Supplier`, `Sale`, `StockMovement` subclasses; custom `InsufficientStockException` guarding the stock-deduction operation during checkout

## Requirements
- PHP 8.1+
- MySQL 8.0+ (or MariaDB)
- PDO MySQL extension enabled

## Setup

1. Create the database and seed sample data:
   ```
   mysql -u root -p < database/schema.sql
   ```
2. Edit `config/database.php` with your local MySQL credentials.
3. Start the built-in PHP server from the project root:
   ```
   php -S localhost:8000 -t public
   ```
4. Visit `http://localhost:8000/index.php` in your browser.

(Alternatively, place the project inside your XAMPP/WAMP `htdocs` folder and point the web server's document root at `public/`.)

## Project Structure
```
├── bootstrap.php          # Autoloader, session start, helper functions
├── config/database.php    # DB credentials
├── database/schema.sql    # Schema + seed data
├── src/
│   ├── Core/Database.php  # PDO singleton connection
│   ├── Exceptions/        # Custom exceptions (InsufficientStockException)
│   ├── Models/             # OOP models (Model, Product, Category, Supplier, Sale, StockMovement)
│   └── Support/helpers.php
└── public/                # Web root
    ├── index.php           # Dashboard
    ├── products/
    ├── categories/
    ├── suppliers/
    ├── pos/                # POS checkout (data-entry/transaction form)
    ├── sales/              # Sales history & receipts
    └── includes/           # Shared header/nav/footer
```

## Roadmap (Final Term)
- User authentication & Role-Based Access Control (Admin/Cashier)
- SQL injection / CSRF protections, secure logout
- RESTful API for products/sales, tested and documented with Postman
- Laravel module migration (MVC structure, migrations/seeders, Eloquent relationships)
- Sales/inventory analytics report
