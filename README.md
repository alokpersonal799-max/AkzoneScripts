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


---

## Latest Features (v1.x)

### Buyer experience
- **PDF invoices** — branded, downloadable receipt for every order (via dompdf), with a thank-you message. "Download Invoice" on the order + My Purchases pages.
- **Product changelog / versions** — admins log version history per product; buyers see a version timeline on the product page.
- **Auto avatars** — every user gets an instant, offline-safe initials avatar (inline SVG) when they haven't uploaded a photo.
- **Mobile number with country code** — on the profile page and at checkout (197 countries with dial codes). Buyer phone shows on the admin order view.

### Manual payment (offline)
- When a buyer chooses **Manual payment**, they pick one of up to **3 methods** (each toggleable in admin):
  - **UPI / QR** — UPI ID + scannable QR
  - **Bank** — bank transfer details
  - **Crypto** — wallet address(es) + optional crypto QR
- Details + a **10-minute countdown** appear **only after a method is selected**. The transaction ID + screenshot must be submitted before the timer expires, or the form locks with a **Restart** option.

### Custom pages
- Create pages with **plain text or raw HTML/CSS/JS**.
- **Pre-built templates** (Privacy Policy, Refund Policy, Terms, About, FAQ, Contact Info) — pick one, then edit.
- Live **preview** before publishing. Modern typography on the public page.

### Promotions & announcements
- **Announcement bar** — 5 priority styles (offer/info/success/warning/alert), custom/auto link, dismissible (remembered per visitor).
- **Hero promotion** — featured products / custom message / up to 2 countdown offers, rendered inside the hero preview.
- **Popup** — custom message, product card, or countdown offer; auto-close timer; one-time or every-visit.

### Advertisement banners
- **Google AdSense / Meta** code, or **manual banners** (upload/URL).
- Layout styles: **1 (full-width) / 2 / 3 / 4 / 6 / 8** per row.
- Per-page enable: marketplace, cart, checkout, dashboard, purchases, wishlist, support, home (free & below reviews), custom pages, contact.

### Themes
- **9 themes**: Default, Emerald, Ocean, Sunset, Midnight, Festival, Christmas, Valentine, Prime Sale.
- **Festive effects**: confetti (Festival), snow (Christmas), floating hearts (Valentine), corner SALE ribbon (Prime Sale).
- **Theme scheduling** — auto-activate a theme between two dates.

### Telegram bot
- Multiple bots, per-event toggles: registrations, product/category added, promotions, purchases, reviews, free downloads, auto promo, custom broadcast.
- **Auto product promotion** on a schedule (cron or traffic-triggered) with a purchase-focused message + buttons (2 per row).
- Live message previews in admin.

### Admin health & system
- **Dashboard health widget** + full **System page**: PHP, DB, storage, cache, SMTP, error-log size, disk space, session driver, debug mode.
- **Error log viewer** with clear button; cache-clear tools.
- **Storage providers**: Local, Amazon S3, DigitalOcean Spaces, Cloudflare R2 (runtime switch).

---

## Cron (optional, for scheduled tasks)

For auto Telegram promotion and scheduled themes to run reliably, add this cron entry on your host:

```
* * * * * cd /path/to/AkzoneScripts && php artisan schedule:run >> /dev/null 2>&1
```

Without cron, auto-promotion still runs based on site traffic.
