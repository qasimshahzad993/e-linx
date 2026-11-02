# e-Linx Website and Admin Guide

**User documentation | Updated: 13 October 2026**

## 1. About the website

e-Linx is a technology services and digital products website. Visitors can learn about the company, browse services, products, portfolio projects, articles, FAQs, and approved client feedback, and submit a contact enquiry or project proposal. Authorized administrators use the admin panel to manage website content and enquiries.

The active application is built with Laravel 12. The `legacy-php` folder contains files from the former standalone PHP website; those files are not used to run the current Laravel website.

## 2. Public website pages

- **Home (`/`)** — services, selected products and projects, recent articles, and links to contact or proposal forms.
- **About (`/about`)** — an introduction to e-Linx.
- **FAQs (`/faqs`)** — published answers to common questions, with structured data for search engines.
- **Services (`/services`)** — active services grouped under Digital Marketing, Website Solutions, Mystery Shopping, and AI Solutions. Each service has its own detail page and a preselected proposal form.
- **Courses (`/courses`)** — published digital skills training outlines and course enquiry forms.
- **Products (`/products`)** — a product catalog with search/category controls and product-related enquiries.
- **Portfolio (`/portfolio`)** — published project entries and their optional detailed case studies.
- **Blog (`/blog`)** — published articles; each article has its own page.
- **Contact (`/contact`)** — a general enquiry form.
- **Request a proposal (`/quote`)** — a project enquiry form that collects scope and planning details.
- **`/robots.txt` and `/sitemap.xml`** — generated files for search engines. Their absolute URLs depend on the `APP_URL` setting.

Contact, proposal, and course enquiries are saved to the database and appear in the admin lead inbox. Mystery Shopping and AI proposals include optional service-specific scope fields. Email notifications require a working mail configuration; see the deployment checklist before relying on email delivery.

The site footer also has an opt-in newsletter form. Subscribers and their personal unsubscribe links are managed in the admin **Newsletter** section. Newsletter messages are not sent automatically: use the opted-in addresses for manual updates and include each subscriber's unsubscribe link. The form only subscribes an address after the visitor explicitly checks the consent box.

Social icons are also shown in the footer. Their current Facebook, Instagram, LinkedIn, and YouTube links are demo links to each platform's home page. Replace `SOCIAL_FACEBOOK_URL`, `SOCIAL_INSTAGRAM_URL`, `SOCIAL_LINKEDIN_URL`, and `SOCIAL_YOUTUBE_URL` in `.env` with e-Linx's official profile URLs before deployment.

An AI assistant widget can answer questions using published e-Linx services, FAQs, products, and courses. It requires an OpenAI-compatible provider API key in the server `.env` (`CHATBOT_API_KEY`); configure `CHATBOT_API_URL` and `CHATBOT_MODEL` if needed, and clear the configuration cache. Until a key is configured, the chat shows an honest setup notice and visitors can still use the contact or WhatsApp links. Chat content is sent to the configured AI provider and is not stored in the e-Linx database; avoid entering sensitive information, and review provider charges, data handling, and the site's privacy notice before enabling it.

## 3. Run the website locally (Windows / XAMPP)

1. Start Apache and MySQL in XAMPP.
2. Open a terminal in `C:\xampp\htdocs\e-linx`.
3. For a first-time setup, install Composer dependencies and create the environment file:

   ```powershell
   cd C:\xampp\htdocs\e-linx
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   ```

4. Set the MySQL connection in `.env`: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.
5. Set `ADMIN_NAME`, `ADMIN_EMAIL`, and a unique `ADMIN_PASSWORD` in `.env` for the first admin account. The password must be at least 12 characters long.
6. Create the database tables and initial admin:

   ```powershell
   php artisan migrate --seed
   ```

7. Start the Laravel development server:

   ```powershell
   php artisan serve
   ```

8. Open the URL printed in the terminal, usually `http://127.0.0.1:8000`. The admin login is at `http://127.0.0.1:8000/admin/login`.

To display blog cover images uploaded through the admin panel, create Laravel's public storage link:

```powershell
php artisan storage:link
```

The `ADMIN_PASSWORD` setting is used by the seeder only when creating the first admin. If an admin already exists, the seeder does not replace that account. Never share or commit `.env`.

## 4. Admin sign-in and sign-out

1. Open `/admin/login`.
2. Enter the admin email address or admin name and password, then select **Sign in to dashboard**.
3. Use the dashboard sidebar to open Leads, Newsletter, Services, Products, Portfolio, Courses, FAQs, Testimonials, or Blog.
4. Open the profile menu to edit your profile or change your password.
5. To sign out, use **Log out** in the profile dropdown or sidebar. Your session is cleared and a confirmation appears on the login page.

Admin pages require an authenticated session. Login attempts are rate-limited. Changing a password requires the current password. The new password must be at least 12 characters and include uppercase and lowercase letters, a number, and a symbol. There is no automated password-reset screen; contact the hosting/database administrator if the password is lost.

## 5. Dashboard and routine tasks

### Leads and enquiries

Enquiries submitted through the contact form and proposal requests appear in the **Leads** inbox. A record includes contact details, the message, enquiry type, and status. A proposal may also include the company and project names, requested service and features, project stage, timeline, budget, target audience, and a reference link.

Each lead can be marked `New`, `Contacted`, or `Closed`, or deleted when it is no longer needed. Restrict access to the lead inbox to authorized staff because it contains personal and project information.

### Newsletter subscribers

Open **Newsletter** in the admin sidebar to view subscribed and unsubscribed addresses. Include each subscriber's personal unsubscribe link in manually sent updates. Remove an address from the records if it should no longer be retained. Newsletter updates are sent manually; sign-up does not trigger automatic email delivery.

### Services, products, and portfolio

Open the relevant section in the admin sidebar to add, update, or delete an item. For services, choose a navigation category; it controls both the public dropdown and the grouped services page. Only active or published items appear on the public website. Check service ordering and descriptions before saving. New Mystery Shopping and AI service outlines are enquiry starting points: confirm capability, required approvals, safeguards, schedule, and scope with the customer before agreeing to deliver them. Product photographs are illustrative stock images, not screenshots of actual e-Linx software.

Portfolio entries support optional challenge, approach, and outcome case study sections. Add only owner-approved, verifiable details. The database migration adds these fields without replacing project records.

### FAQs and testimonials

Use **FAQs** in the admin sidebar to add, edit, reorder, publish, or hide answers. Published FAQs appear on the homepage and `/faqs`; the page emits FAQ structured data for search engines. Default FAQ entries are added by `php artisan db:seed` and can be edited or removed.

Use **Testimonials** to manage customer feedback. New testimonials are hidden until published. Only publish accurate customer quotes, names, roles, organizations, and service references when you have permission to share them. No sample customer reviews or logos are included.

### Courses and training

Use **Courses** in the admin sidebar to maintain course summaries, descriptions, curriculum, duration, delivery mode, and pricing. Starter outlines are drafts. Verify actual availability, delivery details, and price before publishing. Course enquiries are saved as course leads in the Leads inbox and use the configured contact-email notification.

### Blog

In the blog editor, enter a title, category, excerpt, body, author, and optional cover image. A `Draft` article is not public. A `Published` article appears on the public blog; if its publish date is in the future, it remains hidden until then. Cover images may be JPG, PNG, or WebP and must be no larger than 5 MB. Add alternative text to describe an image for accessibility.

### Profile

The profile section lets an administrator update their name and email address or change their password. Do not share account credentials with unauthorized people.

## 6. Forms and stored data

The contact form requires a name and valid email address; phone number and subject are optional. The proposal form asks for the requested service, message, project stage, timeline, and budget; additional project details are optional. Mystery Shopping requests can record locations, touchpoints, and frequency. AI requests can record the workflow, systems, and human review requirements. The server validates submissions, and form requests are rate-limited.

Form data and newsletter email addresses are stored in the database. Newsletter sign-up requires explicit consent and each subscriber has a personal unsubscribe link. Lead email notifications and the public contact email use `MAIL_ADMIN_ADDRESS`; the current business contact address is `adminelinx@gmail.com`. Configure and test production SMTP delivery before launch. Before launch, the business should review its privacy notice, data retention period, access rules, and contact details. The floating WhatsApp button, contact page, and homepage consultation link use `WHATSAPP_NUMBER`, currently set to the supplied e-Linx number `923037004442` (international digits without `+`, spaces, or punctuation). Leave it blank to hide the WhatsApp links.

## 7. Troubleshooting

- **Database connection error:** Check the `.env` `DB_*` values and make sure the XAMPP MySQL service is running.
- **Admin login fails:** Check the email/name and password. For a fresh database, set `ADMIN_EMAIL` and a strong `ADMIN_PASSWORD`, then run `php artisan migrate --seed`.
- **Seeder does not update an existing admin:** This is intentional. Manage existing admin credentials from profile/password settings.
- **Blog cover image is missing:** Run `php artisan storage:link` and verify that `storage/app/public` is writable.
- **CSS changes are not visible:** Hard-refresh the browser; asset URLs may use a version query string.
- **No contact email arrived:** Check that `MAIL_ADMIN_ADDRESS` is `adminelinx@gmail.com`, verify the production mail credentials and sender authorization, then clear cached configuration. The enquiry remains in the admin Leads inbox if delivery fails.
- **Sitemap has the wrong domain:** Correct `APP_URL` in `.env` and refresh the configuration cache.

Application logs are in `storage/logs`. For production issues, back up the database and uploads first. Do not run `migrate:fresh` on a production database.

## 8. Technical and production notes

The application requires PHP 8.2 or later and Laravel 12. Composer manages PHP dependencies. The web document root must be `C:\xampp\htdocs\e-linx\public`, not the project root.

Before production use, configure HTTPS, `APP_DEBUG=false`, secure session cookies, a unique `APP_KEY`, a protected `.env`, restricted database credentials, writable `storage` and `bootstrap/cache` directories, database backups, and a tested restore process.

The public frontend loads fonts and some libraries from third-party CDNs. Production network and Content Security Policy settings must allow those hosts, or the assets should be hosted locally. The legacy SQL dump at `legacy-php/database/elinx_db.sql` drops and recreates tables; back up any database before importing it.

## 9. Important URLs

| Task | URL |
| --- | --- |
| Public website | `/` |
| FAQs | `/faqs` |
| Courses | `/courses` |
| Contact form | `/contact` |
| Proposal form | `/quote` |
| Admin sign-in | `/admin/login` |
| Admin dashboard | `/admin` |
| Admin leads | `/admin/leads` |
| Admin blog | `/admin/blog` |
| Admin FAQs | `/admin/trust/faqs` |
| Admin testimonials | `/admin/trust/testimonials` |
| Admin courses | `/admin/courses` |
| Sitemap | `/sitemap.xml` |
| Robots rules | `/robots.txt` |

**Support:** Contact your authorized website or hosting administrator for application or hosting issues.
