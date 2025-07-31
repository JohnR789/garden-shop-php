# 🌱 Garden Shop

A modern e-commerce demo web app for selling gardening tools, built with PHP 8.2, PostgreSQL, Composer, and Docker.  
This project is an end-to-end, database-driven web application using a fully custom MVC structure and contemporary dev workflows.  
**Note:** This is not a framework-based app (no Laravel, Symfony, etc.), but hand-crafted to demonstrate mastery of PHP, OOP design, Docker deployment, and SQL schema migration.

---

## 🚀 Features

- **Advanced MVC Structure:**  
  Organizes logic into Controllers, Models, Views (`app/Controllers`, `app/Models`, `app/Views`).  
- **PostgreSQL Database Integration:**  
  Handles complex product catalogs, users, carts, and orders.
- **User Auth & Sessions:**  
  Secure registration, login, admin/user separation, and session-based cart management.
- **Admin Product Management:**  
  Soft-deletes, restores, bulk actions, category/tagging, and media support.
- **Modern Dev Workflow:**  
  - Fully Dockerized for reliable local and cloud deploys  
  - PSR-4 autoloading via Composer  
  - Environment variables with `vlucas/phpdotenv`  
- **Responsive Bootstrap UI:**  
  Clean, mobile-friendly pages for shopping, checkout, and admin actions.

---

## 🏗️ Tech Stack

| Area           | Tech                                                         |
| -------------- | ------------------------------------------------------------ |
| Backend        | PHP 8.2 (OOP, custom MVC), Composer (PSR-4)                  |
| Frontend       | Bootstrap 5, HTML5, minimal JS                               |
| Database       | PostgreSQL 16+ (official Docker image, advanced schema)      |
| Deployment     | Docker, Docker Compose, Render.com (cloud PaaS ready)        |
| Auth           | Native PHP sessions, secure password hashing                 |
| Config         | `.env` via `vlucas/phpdotenv`                                |
| Data Import    | Raw SQL, CSV, or INSERTs (see `/database/` or `/sql/` folder)|

---

## 📦 Directory Structure

.
├── app/
│ ├── Controllers/ # All route logic (Product, Auth, Cart, Admin, etc)
│ ├── Core/ # Router, View, base controller
│ ├── Models/ # Database ORM-like models
│ └── Views/ # PHP views, includes, layouts
├── public/ # Public web root (index.php entry point)
├── config/ # DB/env config
├── vendor/ # Composer dependencies (autoload)
├── Dockerfile
├── docker-compose.yml
├── composer.json
├── README.md
└── ... (assets, images, migrations, etc)

yaml
Copy
Edit

---

## 🧑‍💻 Getting Started

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- (Optional) [psql CLI](https://www.postgresql.org/download/) for database access

### Quickstart: Docker

```bash
git clone https://github.com/YOURNAME/garden-shop-php.git
cd garden-shop-php

# Build and run the app (Docker)
docker build -t gardenshop .
docker run --rm -p 8080:80 gardenshop
Visit http://localhost:8080 in your browser.

PostgreSQL Database Setup
Edit /config/config.php or set environment variables for your DB host/user/pass.

The app auto-connects to PostgreSQL using PDO.

Run SQL migrations in /database/init.sql or use the psql CLI:

bash
Copy
Edit
psql -h <host> -U <user> <db>
# Then paste CREATE TABLE ... (see below)
🔐 Authentication & User Roles
Users can register, log in, and manage a shopping cart.

Admins can add/edit/delete (soft-delete) products, categories, and manage orders.

Passwords are hashed with password_hash() (bcrypt) for security.

🗃️ Database Schema
See /database/init.sql for full schema, or a summary below:

sql
Copy
Edit
-- Users
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(32) UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN NOT NULL DEFAULT false
);

-- Products, Categories, Orders, Carts...
-- (see full SQL above)
🌎 Environment Variables
.env file (not committed) for DB secrets:

ini
Copy
Edit
DB_HOST=localhost
DB_NAME=gadenshop
DB_USER=postgres
DB_PASS=yourpassword
📝 Sample Data Import
To load demo products/categories:

Use /database/seed.sql

Or, login to Postgres and paste INSERT INTO products ... from your favorite sample set.

🛠️ Development Notes
Composer Autoload:
Run composer install and composer dump-autoload after changing class locations or adding dependencies.

Docker:
App runs as www-data, root dir is /var/www/html, with public/ as Apache DocumentRoot.

Error Handling:
Basic error messages shown; full stack traces in logs. For advanced debugging, use Xdebug or tailor error reporting in php.ini.

🤝 Contributing
Pull requests welcome! Please fork, commit with clear messages, and submit a PR.
If you spot issues or have suggestions, open an Issue or contact me directly.

⚠️ Disclaimer
This app is a demo/prototype and should not be used in production as-is (no CSRF, limited validation, no Stripe integration, etc.).
For a production app, use a robust framework and follow industry best practices for security and scaling.

👨‍🌾 Author
John Rollins

Built with care, PHP, caffeine, and a love of gardening tools! 🌷