# AkzoneScripts — Single-Vendor Digital Marketplace

A complete, production-ready **Laravel 12** marketplace for selling digital products such as
scripts, source code, UI kits and design assets. It ships with a modern landing page, a full
storefront, customer and admin dashboards, a session cart, checkout, and secure digital downloads.

> Built with PHP 8.2+, Laravel 12, Tailwind CSS and Alpine.js.

---

## ✨ Features

### Storefront
- Modern, responsive **landing page** with hero, categories, featured & latest products
- **Product catalog** with full-text search, category filter, price filter and sorting
- **Product detail** pages with gallery, reviews, related products and a live demo link
- **Session-based cart** available to guests and members
- **Checkout** with billing details and pluggable payment methods (manual / Stripe / PayPal)
- **Verified-buyer reviews & ratings** (auto-recalculated averages)
- **Wishlist**

### Customer Dashboard
- Account overview with spend / orders / downloads stats
- **My Purchases** with secure, access-controlled download links
- Wishlist management
- Profile settings (avatar, bio) and password change

### Admin Dashboard
- KPI cards (revenue, products, customers, downloads) and a 7-day revenue chart
- **Products** — full CRUD with thumbnail + private package upload, tags, sale pricing, featured flag
- **Categories** — full CRUD with auto-slugging
- **Orders** — list, filter, view and update status
- **Customers** — list, view order history, change roles, delete accounts

### Security
- Role-based access control (`user` / `admin`) via the `admin` route middleware
- Purchased files stored on a **private disk** and streamed only to verified buyers
- CSRF protection, hashed passwords, validated requests throughout

---

## 🧰 Requirements

- PHP **8.2+** (8.3 / 8.4 supported)
- Composer 2
- One of: SQLite (default, zero-config), MySQL 8, MariaDB, or PostgreSQL
- Node.js 18+ **only if** you want to swap the Tailwind CDN for a compiled build (optional)

---

## 🚀 Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Create your environment file and generate an app key
cp .env.example .env
php artisan key:generate

# 3. (SQLite default) create the database file
touch database/database.sqlite

# 4. Run migrations and seed demo data
php artisan migrate --seed

# 5. Link the storage directory (for thumbnails / avatars)
php artisan storage:link

# 6. Start the development server
php artisan serve
```

Now visit **http://localhost:8000**.

### Using MySQL / PostgreSQL instead of SQLite

Edit `.env` and set the connection, then run `php artisan migrate --seed`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=akzonescripts
DB_USERNAME=root
DB_PASSWORD=secret
```

---

## 🔑 Demo Accounts

After seeding, log in with:

| Role     | Email                          | Password   |
|----------|--------------------------------|------------|
| Admin    | `admin@akzonescripts.test`     | `password` |
| Customer | `customer@akzonescripts.test`  | `password` |

The admin panel lives at **`/admin`**.

> **Note:** Seeded products do **not** include downloadable files. To enable real downloads,
> open the admin panel → Products → edit a product → upload a `.zip` package. Files are stored
> privately in `storage/app/private/products` and are only served to verified buyers.

---

## 💳 Enabling Live Payments

The checkout runs in **manual mode** out of the box so the full purchase flow works without any
external credentials. To accept real payments, add your keys in `.env` and wire the chosen gateway
into `App\Http\Controllers\CheckoutController@store`:

```dotenv
STRIPE_KEY=pk_live_xxx
STRIPE_SECRET=sk_live_xxx

PAYPAL_CLIENT_ID=xxx
PAYPAL_SECRET=xxx
PAYPAL_MODE=live
```

These are read in `config/services.php`.

---

## 🎨 Styling

The UI uses **Tailwind CSS via the Play CDN** plus **Alpine.js** (both loaded in
`resources/views/partials/head.blade.php`). This means the interface works instantly with **no
build step**.

For production you may prefer a compiled Tailwind bundle. To do that, install Tailwind with npm,
add a `resources/css/app.css`, configure `tailwind.config.js` with the same `brand`/`ink` palette,
and replace the CDN `<script>` with `@vite(['resources/css/app.css', 'resources/js/app.js'])`.

---

## ⚙️ Configuration

Marketplace-wide settings live in **`config/marketplace.php`** (also configurable via `.env`):

| Key                | Description                                  |
|--------------------|----------------------------------------------|
| `name` / `tagline` | Brand name and headline                      |
| `currency` / `currency_symbol` | Display currency                 |
| `per_page`         | Products per catalog page                    |
| `allowed_file_types` | Permitted package extensions               |
| `max_file_size`    | Max upload size (KB)                          |
| `support_email`    | Footer / contact email                        |

---

## 🗂️ Project Structure

```
app/
├── Http/
│   ├── Controllers/        # Storefront, auth, cart, checkout, dashboard
│   │   └── Admin/          # Admin CRUD controllers
│   └── Middleware/         # EnsureUserIsAdmin
├── Models/                 # User, Category, Product, Order, OrderItem, Review, Wishlist
├── Observers/              # ProductObserver (auto-slug)
└── Services/               # CartService (session cart)
config/marketplace.php      # Marketplace settings
database/
├── migrations/             # Full schema
└── seeders/                # Demo data
resources/views/
├── layouts/                # app, dashboard, admin
├── components/             # product-card, price, star-rating, status-badge, ...
├── partials/               # head, flash
├── admin/                  # Admin panel views
├── dashboard/              # Customer dashboard views
└── products/, cart/, checkout/, auth/, home.blade.php
routes/web.php              # All routes
```

---

## 📜 License

MIT — free for personal and commercial use.
