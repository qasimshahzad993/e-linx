# e-Linx

The project root is the Laravel 12 application. The previous standalone PHP site and admin CMS have been preserved in [`legacy-php/`](legacy-php/README.md) and are not part of the active application.

## Website documentation

The Roman Urdu website and admin guide is available as [`docs/e-linx-website-guide.pdf`](docs/e-linx-website-guide.pdf), with an editable Markdown source at [`docs/e-linx-website-guide.md`](docs/e-linx-website-guide.md). Regenerate the PDF after editing the guide with:

```powershell
php docs\generate-pdf.php docs\e-linx-website-guide.md docs\e-linx-website-guide.pdf
```

## XAMPP quick start

1. Start Apache and MySQL in XAMPP.
2. Open a terminal at `C:\xampp\htdocs\e-linx`:

   ```powershell
   cd C:\xampp\htdocs\e-linx
   php artisan serve
   ```

3. Open the URL printed in the terminal, normally `http://127.0.0.1:8000`.
4. The admin login is at `/admin/login`.

Composer dependencies are included in `vendor/`. To restore them if needed, run `composer install` from the project root. Laravel reads settings from the root `.env`; do not commit that file.

## Database

Configure the MySQL connection in `.env` and run `php artisan migrate --seed`. The migration preserves existing e-Linx tables and records; the admin seeder does not replace an existing administrator. For a fresh database, configure `ADMIN_EMAIL` and a unique `ADMIN_PASSWORD` of at least 12 characters in `.env` first.

`legacy-php/database/elinx_db.sql` is the original SQL dump. **It drops and recreates tables when imported**; back up any database with existing data before importing it.

## Deployment checklist

**This repository is not production-ready until the owner supplies and verifies the live business and hosting details below.** The local `.env` is for development and must never be uploaded or copied directly into a production release.

### Before launch

- Set production `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-real-domain`, and generate a unique `APP_KEY`. Enforce HTTPS and set `SESSION_SECURE_COOKIE=true`.
- Configure a production MySQL database with restricted credentials, then back it up before running `php artisan migrate --force`. Do not use `migrate:fresh` on a database with data.
- Contact messages and project briefs are saved in the admin lead inbox and trigger an email notification to `MAIL_ADMIN_ADDRESS` (currently `adminelinx@gmail.com`; it falls back to `ADMIN_EMAIL`). The public contact page and footer display this configured notification address. Gmail SMTP requires `MAIL_MAILER=smtp`, `MAIL_SCHEME=smtp`, `MAIL_HOST=smtp.gmail.com`, and `MAIL_PORT=587`; use the authenticated Gmail account for `MAIL_USERNAME` and `MAIL_FROM_ADDRESS`, and a Google App Password for `MAIL_PASSWORD` (never the account's regular password). Create the App Password after enabling 2-Step Verification. Alternatively, use Resend with `MAIL_MAILER=resend`, `RESEND_API_KEY`, and a sender on a verified domain. For local development without delivery, use `MAIL_MAILER=log`. Never commit credentials. Clear cached configuration after changing mail settings with `php artisan optimize:clear`. If delivery fails, the lead remains saved and the form reports that the admin should check the leads inbox.
- Verify the actual business legal name, contact details (the current public email is `adminelinx@gmail.com` and the supplied WhatsApp is `+92 303 7004442`), and owner-reviewed privacy/terms information. The forms store the name, email, optional phone, subject/service, message, budget and enquiry status; retention and applicable legal wording have not been supplied.
- The footer's Facebook, Instagram, LinkedIn, and YouTube URLs are demo platform home links from `SOCIAL_FACEBOOK_URL`, `SOCIAL_INSTAGRAM_URL`, `SOCIAL_LINKEDIN_URL`, and `SOCIAL_YOUTUBE_URL`. Replace them in production `.env` with e-Linx's own profile URLs before launch.
- The AI assistant uses an OpenAI-compatible chat API. Set `CHATBOT_API_KEY` only in the server's private `.env`; optionally set `CHATBOT_API_URL` and `CHATBOT_MODEL`. Without an API key the widget clearly directs visitors to email or WhatsApp and does not pretend to answer. The chat sends recent messages and published E-Linx services, FAQs, products, and courses to the configured provider; it does not store transcripts in the E-Linx database. Provider charges and provider-side data handling apply, so review your privacy notice and provider terms before enabling it. Clear cached configuration after setting deployment values.
- Review all service, product, and portfolio entries in the admin CMS. Product-page photos are licensed stock images illustrating product categories, not screenshots of e-Linx software.
- Review blog posts for accuracy, attribution, and image rights before publishing. Uploaded blog cover images are stored on Laravel's public disk; run `php artisan storage:link` and ensure `storage/app/public` is writable.
- Configure real social links only if supplied. Do not add unverified client names, testimonials, numerical outcomes, or claims.
- Test contact/quote submissions, password reset/profile processes, admin access, backups, log rotation, and restore procedures on the target host.

### Apache and Laravel setup

Configure Apache's document root to `C:\xampp\htdocs\e-linx\public`, not the project root, and enable HTTPS. Keep `.env` outside the web root, provide write access to `storage/` and `bootstrap/cache/`, and run commands from the Laravel project root:

```powershell
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
```

Set the live values before running `php artisan optimize`; after any environment/configuration change, refresh the cached configuration (`php artisan optimize:clear`, then `php artisan optimize`). The public layout currently loads fonts and front-end libraries from third-party CDNs, so production must permit those hosts or move the assets to the application's own host.

`robots.txt` and `sitemap.xml` are generated by Laravel and use the configured `APP_URL`. Check both on the production hostname after deployment; do not ship a static `public/robots.txt` alongside the dynamic route.

## Stock image sources

The locally stored photographs in `public/assets/img/stock/` and `public/assets/img/services/` come from Unsplash and are used under the [Unsplash License](https://unsplash.com/license), which permits free commercial use. They are illustrative stock photography, not customer, employee, project, or product screenshots. The original custom SVG artwork remains in `public/assets/img/products/` and `public/assets/img/services/`.

| Local image | Unsplash photo source |
| --- | --- |
| `web-platforms.jpg` | [photo-1498050108023-c5249f4df085](https://images.unsplash.com/photo-1498050108023-c5249f4df085) |
| `laravel-development.jpg` | [photo-1555066931-4365d14bab8c](https://images.unsplash.com/photo-1555066931-4365d14bab8c) |
| `mobile-apps.jpg` | [photo-1551650975-87deedd944c3](https://images.unsplash.com/photo-1551650975-87deedd944c3) |
| `product-design.jpg` | [photo-1558655146-d09347e92766](https://images.unsplash.com/photo-1558655146-d09347e92766) |
| `cloud-devops.jpg` | [photo-1451187580459-43490279c0fa](https://images.unsplash.com/photo-1451187580459-43490279c0fa) |
| `digital-growth.jpg` | [photo-1551288049-bebda4e38f71](https://images.unsplash.com/photo-1551288049-bebda4e38f71) |
| `crm.jpg` | [photo-1552664730-d307ca884978](https://images.unsplash.com/photo-1552664730-d307ca884978) |
| `commerce.jpg` | [photo-1556742049-0cfed4f6a45d](https://images.unsplash.com/photo-1556742049-0cfed4f6a45d) |
| `desk.jpg` | [photo-1611224923853-80b023f02d71](https://images.unsplash.com/photo-1611224923853-80b023f02d71) |
| `insight.jpg` | [photo-1460925895917-afdab827c52f](https://images.unsplash.com/photo-1460925895917-afdab827c52f) |
| `services/youtube-automation.jpg` | [photo-1492619375914-88005aa9e8fb](https://images.unsplash.com/photo-1492619375914-88005aa9e8fb) |

## Included functionality

### Publishing articles

Sign in at `/admin/login`, then open **Blog** in the admin sidebar. Create an article with a title, summary, body, and author; choose **Draft** to keep it private or **Published** to make it public. A future publish date schedules a published post to appear at that time. Cover images accept JPG, PNG, or WebP files up to 5 MB. The editor stores plain text with paragraph breaks (not HTML), and the public article page escapes content for safe display. The public disk link is required for uploaded covers:

```powershell
php artisan storage:link
```

- Public home, about, FAQs, digital skills courses, services, service details, products and portfolio pages
- Validated contact and quote forms
- Detailed project briefs collect the requested service, project overview and scope, project stage, timeline, budget, target audience, and reference links; structured answers appear in the admin lead inbox.
- Admin login and lead management
- Service, product, portfolio and profile management
- Admin-managed FAQs and customer testimonials. FAQs are seeded with editable general information; testimonials stay hidden by default and should only be published after customer approval.
- Digital marketing, search, paid-campaign, creative, content and email services can be maintained alongside e-Linx's software services. Course outlines are managed in the admin CMS and remain drafts until confirmed and published.
- Public blog and article pages, with admin drafting, scheduling, publishing, editing, and cover-image uploads
- Toastify admin notifications and live lead/quote alerts, polled every five seconds
- Licensed local service/product photography, product search/category filtering, and product-aware quote forms
- XML sitemap, robots directives, accessibility improvements, and rate-limited enquiry/login forms
- Feature tests for public pages, submissions, admin workflows and migration safety
