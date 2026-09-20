# Sari-Sari Store POS System

A Point-of-Sale and inventory management system for a small sari-sari (retail) store, built for **WMA4 – Advanced Web Development** (Tarlac State University).

**Assigned System Type:** Point-of-Sale (POS) / Sales Management System

## Midterm Phase (Current)

Built with native PHP (OOP) and MySQL (PDO, prepared statements). No framework yet — Laravel integration, authentication/RBAC, and the REST API are Final Term work.

### Features
- Product, category, and supplier management (full CRUD)
- Searchable, filterable, sortable product listing (`WHERE`, `ORDER BY`, `LIMIT`)
- Customizable category badge colors (six-color swatch picker, falls back to an automatic hash-based color) plus a legend on Products/Checkout
- Point-of-sale transaction screen — cart-based checkout with server-side stock validation
- Senior Citizen / PWD discount at checkout (RA 9994 / RA 10754, 20% off), recomputed server-side from the cart subtotal and gated behind an "ID presented and verified" confirmation, with a confirm-sale review step before finalizing
- Sales history and receipt view, with date/keyword filtering and sort order
- Dashboard with today's sales total, transaction count, low-stock alerts, top-selling products, and a 7-day sales trend chart
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

## Deployment (Railway)

1. Push this repo to GitHub and create a new Railway project from it.
2. Add a MySQL plugin to the Railway project (Railway auto-injects `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE` into the app service — `config/database.php` reads these automatically).
3. Open the MySQL plugin's query console (or connect with a MySQL client using its credentials) and run `database/schema.sql` to create the tables and seed data.
4. Railway builds with Nixpacks and starts the app via `railway.json` (`php -S 0.0.0.0:$PORT -t public`), so no further web server config is needed.
5. Once deployed, Railway assigns a public URL — the app works at the domain root (no subfolder), same as the built-in PHP server setup above.

## Roadmap (Final Term)
- User authentication & Role-Based Access Control (Admin/Cashier) — the sidebar's "Admin User" and "Log out" are currently static placeholders with no real session/login behind them
- Real Customer/Cashier attribution on sales — Sales History currently shows every row as "Walk-in" / "Admin" since there's no logged-in user or customer record to attach yet
- CSRF protection and secure logout (SQL injection is already mitigated project-wide via PDO prepared statements)
- RESTful API for products/sales, tested and documented with Postman
- Laravel module migration (MVC structure, migrations/seeders, Eloquent relationships)
- Deeper sales/inventory analytics — date-range reports, exportable data, profit margin using `cost_price` (today's dashboard chart only covers a rolling 7 days)
