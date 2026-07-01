# AkzoneScripts — Digital Marketplace Platform

A complete, production-ready **single-vendor digital marketplace** built with **Laravel 12** for selling scripts, templates, UI kits, and design assets. Modern UI, instant delivery, and a powerful admin dashboard.

> **Created by [@i_am2_black](https://instagram.com/i_am2_black)** — DM for custom orders.

---

## Features

### Storefront
- Beautiful, responsive landing page with animated hero section
- Product catalog with category filtering, search, and sorting
- Product detail pages with gallery, reviews, changelog, and related products
- Shopping cart + multi-gateway checkout (Stripe, PayPal, Razorpay, Manual)
- Free product downloads (login required)
- User dashboard (purchases, downloads, wishlist, tickets)
- Multi-currency support with visitor currency switching
- Contact form (works for guests and members)
- Custom pages (About, Terms, Privacy, Refund — fully editable HTML)

### Admin Dashboard
- Revenue analytics, sales charts, top products, recent activity
- Health monitoring (PHP, DB, storage, cache, SMTP, disk, errors)
- System information + cache clear tools
- Error log viewer with clear button

### Product Management
- Rich product editor with gallery, tags, versioning
- **Delivery options**: file upload OR external hosted link
- Per-buyer download limits + link expiry (signed URLs)
- Product changelog/versions (public timeline)
- Sale availability toggle + WhatsApp/Telegram contact per product
- Live preview link support

### Storage Providers
- Local, Amazon S3, DigitalOcean Spaces, Cloudflare R2
- Runtime disk switching (no code changes needed)
- Admin credentials page with provider-specific hints

### Promotions & Marketing
- **Announcement bar** — 5 priority styles (offer/info/success/warning/alert), dismissible, custom link
- **Hero promotion** — featured products / custom message / countdown offers (up to 2)
- **Popup system** — custom message, product card, or countdown offer with auto-close timer
- **Telegram bot integration** — multi-bot, per-event toggles, auto product promotion scheduler

### Telegram Bot
- Post beautiful updates: new users, products, categories, purchases, reviews, free downloads
- Auto product promotion on a schedule (minutes/hours/days)
- Custom broadcast messages
- Message previews in admin
- Multiple bots with per-event control

### Orders & Payments
- Stripe, PayPal, Razorpay (enable/disable per gateway)
- Manual payment (UPI, bank, crypto, QR) with screenshot verification
- PDF invoice download with site branding
- Order status management + email receipts

### Users & Communication
- Email verification (toggle on/off)
- Forgot password flow
- Google/Firebase captcha on login/registration
- Support tickets with file attachments
- Contact messages with auto-delete retention
- Admin notifications bell (all user activity)

### Settings
- General (branding, logo, timezone, socials)
- Homepage section toggles
- Hero section content
- Footer customization
- SEO (title, description, OG image, analytics)
- Payment gateways
- Manual payment methods
- Email/SMTP
- Login & captcha
- Integrations (Tawk.to)
- Maintenance mode
- Currencies (multi-currency with exchange rates)
- Custom pages (HTML/CSS/JS editor with templates)

---

## Requirements

- PHP 8.2+
- MySQL 5.7+ / MariaDB 10.3+
- Composer 2.x
- Apache or Nginx (mod_rewrite enabled)
- XAMPP / Laragon / any PHP hosting (Hostinger, Namecheap, etc.)

---

## Installation

### Local (XAMPP / Windows)

```powershell
cd C:\xampp\htdocs
git clone https://github.com/alokpersonal799-max/AkzoneScripts.git
cd AkzoneScripts
composer install
composer require barryvdh/laravel-dompdf
php artisan migrate
php artisan db:seed --class=DemoSeeder
php artisan storage:link
php artisan view:clear
php artisan config:clear
```

Then visit: `http://localhost/AkzoneScripts/public/`

### Hosting (Hostinger, Namecheap, etc.)

1. Upload the project files to your hosting
2. Point your domain to the `public/` directory
3. Create a MySQL database and update `.env`
4. Run via SSH:
   ```bash
   composer install
   composer require barryvdh/laravel-dompdf
   php artisan migrate
   php artisan db:seed --class=DemoSeeder
   php artisan storage:link
   ```
5. For auto Telegram promotion, add cron:
   ```
   * * * * * cd /path/to/AkzoneScripts && php artisan schedule:run >> /dev/null 2>&1
   ```

### Web Installer

The script includes a built-in 6-step web installer at `/install` that handles:
- Environment check
- Database configuration
- Admin account creation
- Site settings
- Demo data (optional)
- Finalization

---

## Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@akzone.com | password |
| Customer | user@akzone.com | password |

---

## Admin Panel

Access at `/admin` after logging in with an admin account.

### Navigation
- Dashboard (with health monitoring)
- Products
- Categories
- Orders
- Customers
- Coupons
- Reviews
- Support (tickets)
- Reports
- Messages (contact form)
- TG Connection (Telegram bots)
- Promotions
- Storage
- Currencies
- Settings
- System Information
- Log out

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12 (PHP 8.2+) |
| Frontend | Tailwind CSS (CDN) + Alpine.js |
| Database | MySQL / MariaDB |
| PDF | barryvdh/laravel-dompdf |
| Cloud Storage | league/flysystem-aws-s3-v3 (optional) |
| Fonts | Plus Jakarta Sans + Inter (Google Fonts) |

---

## File Structure

```
app/
├── Http/Controllers/       # 40 controllers (admin + public)
├── Models/                 # 18 Eloquent models
├── Services/               # TelegramService, CartService, CurrencyService
├── Support/                # TelegramMessages builder
├── Mail/                   # OrderReceiptMail
├── Providers/              # AppServiceProvider (runtime config)
config/
├── marketplace.php         # Platform settings
database/
├── migrations/             # 29 migrations (full schema)
├── seeders/                # CoreSeeder + DemoSeeder
resources/views/            # 95 Blade templates
routes/
├── web.php                 # All web routes
├── console.php             # Scheduler (auto TG promo)
```

---

## Configuration

All settings are manageable from the admin panel. Key `.env` values:

| Key | Description |
|-----|-------------|
| APP_NAME | Site name |
| DB_CONNECTION | mysql |
| DB_DATABASE | Your database name |
| MAIL_* | SMTP settings (also configurable in admin) |

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| `storage:link already exists` | Safe to ignore — the link is working |
| `&&` not working in PowerShell | Run commands one per line, or use `;` |
| Blank page after pull | Run `php artisan view:clear; php artisan config:clear` |
| Telegram not sending | Check token + bot is admin of channel + chat ID format |
| PDF not generating | Run `composer require barryvdh/laravel-dompdf` |
| S3 not working | Run `composer require league/flysystem-aws-s3-v3` |

---

## License

This is a commercial script. All rights reserved.

---

## Support

- Instagram: [@i_am2_black](https://instagram.com/i_am2_black)
- Email: Configure in Admin → Settings → General
