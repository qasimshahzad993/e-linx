<?php

namespace Tests\Feature;

use App\Mail\LeadNotification;
use App\Models\Admin;
use App\Models\BlogPost;
use App\Models\Lead;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteAndAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('assets/css/brand-theme.css')
            ->assertSee('<meta name="theme-color" content="#0b1320">', false)
            ->assertSee('assets/img/elinxlogo.png')
            ->assertSee('elinx-cursor')
            ->assertSee('e-Linx Technology logo');
        $this->assertStringContainsString('Occasional e-Linx updates', $this->get('/')->getContent());
        $this->get('/about')->assertOk();
        $this->get('/faqs')->assertOk()->assertSee('Our FAQs are being updated.');
        $this->get('/services')->assertOk();
        $this->get('/products')->assertOk();
        $this->get('/portfolio')->assertOk();
        $this->get('/blog')->assertOk()->assertSee('New articles are on the way.');
        $this->get('/contact')->assertOk();
        $this->get('/quote')->assertOk();
        $this->get('/quote')
            ->assertSee('Tell us about your project')
            ->assertSee('Web & SaaS Engineering')
            ->assertSee('YouTube Automation')
            ->assertDontSee('Cloud &amp; DevOps')
            ->assertSee('What might the project include?')
            ->assertSee('When would you like to start?')
            ->assertSee('Who will use it?');
        $this->get('/')
            ->assertOk()
            ->assertSee('> Admin</button>', false)
            ->assertSee('action="'.route('admin.access').'"', false);
    }

    public function test_public_contact_details_use_the_configured_admin_email(): void
    {
        config(['mail.notifications.to' => 'adminelinx@gmail.com']);
        config([
            'services.social.facebook' => 'https://www.facebook.com/',
            'services.social.instagram' => 'https://www.instagram.com/',
            'services.social.linkedin' => 'https://www.linkedin.com/',
            'services.social.youtube' => 'https://www.youtube.com/',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('mailto:adminelinx@gmail.com')
            ->assertSee('> adminelinx@gmail.com</a>', false)
            ->assertSee('https://www.facebook.com/')
            ->assertSee('https://www.instagram.com/')
            ->assertSee('https://www.linkedin.com/')
            ->assertSee('https://www.youtube.com/')
            ->assertSee('demo link; update before launch')
            ->assertDontSee('hello@elinx.pk');
        $this->get('/contact')
            ->assertOk()
            ->assertSee('mailto:adminelinx@gmail.com')
            ->assertSee('>adminelinx@gmail.com</a>', false);
    }

    public function test_whatsapp_contact_button_requires_a_configured_number_and_uses_international_digits(): void
    {
        config(['services.whatsapp.number' => null]);
        $this->get('/')->assertOk()->assertDontSee('https://wa.me/');

        config(['services.whatsapp.number' => '+92 (300) 123-4567']);
        $this->get('/')
            ->assertOk()
            ->assertSee('https://wa.me/923001234567')
            ->assertSee('target="_blank" rel="noopener noreferrer"', false)
            ->assertSee('Chat with us on WhatsApp')
            ->assertSee('Chat with e-Linx on WhatsApp')
            ->assertSee('class="whatsapp-float"', false)
            ->assertSee('class="whatsapp-icon"', false)
            ->assertDontSee('<span>Chat with us</span>', false);
        $this->get('/contact')
            ->assertOk()
            ->assertSee('https://wa.me/923001234567')
            ->assertSee('Chat with us on WhatsApp');
    }

    public function test_only_published_blog_posts_are_visible_and_article_pages_render(): void
    {
        $published = BlogPost::create([
            'title' => 'Building dependable web products',
            'slug' => 'building-dependable-web-products',
            'category' => 'Engineering',
            'excerpt' => 'A practical guide to making web products reliable.',
            'body' => "## Start with clear requirements\n\nTest the important user journeys.\n\n<script>alert('unsafe')</script>\n\n[unsafe link](javascript:alert(1))",
            'author' => 'e-Linx Team',
            'author_title' => 'Technology Editor',
            'tags' => ['reliability', 'web products'],
            'seo_title' => 'Build dependable web products | e-Linx',
            'seo_description' => 'Learn practical steps for making web products more reliable.',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        BlogPost::create([
            'title' => 'Draft article for later',
            'slug' => 'draft-article-for-later',
            'excerpt' => 'This draft must remain private.',
            'body' => 'Draft content.',
            'author' => 'e-Linx Team',
            'status' => 'draft',
        ]);
        BlogPost::create([
            'title' => 'Future article',
            'slug' => 'future-article',
            'excerpt' => 'This scheduled post is not public yet.',
            'body' => 'Scheduled content.',
            'author' => 'e-Linx Team',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $this->get('/blog')
            ->assertOk()
            ->assertSee('Building dependable web products')
            ->assertDontSee('Draft article for later')
            ->assertDontSee('Future article');
        $this->get('/blog/'.$published->slug)
            ->assertOk()
            ->assertSee('Start with clear requirements')
            ->assertSee('<h2>Start with clear requirements</h2>', false)
            ->assertDontSee("<script>alert('unsafe')</script>", false)
            ->assertDontSee('href="javascript:alert(1)"', false)
            ->assertSee('<meta name="description" content="Learn practical steps for making web products more reliable.">', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('"@type":"BlogPosting"', false)
            ->assertSee('Technology Editor')
            ->assertSee('reliability')
            ->assertSee('1 min read');
        $this->get('/blog/draft-article-for-later')->assertNotFound();
        $this->get('/blog/future-article')->assertNotFound();
        $this->get('/')
            ->assertOk()
            ->assertSee('Ideas worth sharing.')
            ->assertSee('Building dependable web products');
        $this->get('/sitemap.xml')->assertOk()->assertSee('/blog/'.$published->slug);
    }

    public function test_admin_can_create_update_and_delete_blog_posts_with_cover_images(): void
    {
        Storage::fake('public');
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);

        $this->withSession(['admin_id' => $admin->id])
            ->get('/admin/blog')
            ->assertOk()
            ->assertSee('Create an article')
            ->assertSee('Article URL')
            ->assertSee('Tags')
            ->assertSee('Author role')
            ->assertSee('Search and social preview')
            ->assertSee('Select text and use the toolbar')
            ->assertSee('Professional writing checklist')
            ->assertSee('data-markdown-editor', false)
            ->assertSee('data-markdown-action="heading"', false)
            ->assertSee('blog-writing-stats', false);

        $this->withSession(['admin_id' => $admin->id])
            ->post('/admin/blog', [
                'title' => 'Useful product planning',
                'slug' => 'product-planning-guide',
                'category' => 'Product',
                'tags' => 'planning, product strategy',
                'excerpt' => 'A thoughtful introduction to product planning.',
                'body' => "## A complete article\n\nA complete article body for readers.",
                'author' => 'e-Linx Team',
                'author_title' => 'Product Editor',
                'seo_title' => 'Product planning guide',
                'seo_description' => 'A practical introduction to product planning.',
                'status' => 'published',
                'cover_image' => UploadedFile::fake()->create('cover.jpg', 20, 'image/jpeg'),
                'cover_image_alt' => 'A product team planning a project',
            ])
            ->assertRedirect('/admin/blog');

        $post = BlogPost::where('slug', 'product-planning-guide')->firstOrFail();
        $oldImage = $post->cover_image;
        Storage::disk('public')->assertExists($oldImage);
        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertSame(['planning', 'product strategy'], $post->tags);
        $this->assertSame('Product Editor', $post->author_title);
        $this->get('/blog/'.$post->slug)->assertSee('<h2>A complete article</h2>', false);

        $this->withSession(['admin_id' => $admin->id])
            ->put('/admin/blog/'.$post->id, [
                'title' => 'Useful product planning',
                'slug' => 'product-planning-guide',
                'category' => 'Product',
                'excerpt' => 'An updated introduction to product planning.',
                'body' => 'An updated article body for readers.',
                'author' => 'e-Linx Team',
                'author_title' => 'Senior Editor',
                'tags' => 'planning, business',
                'seo_title' => 'Updated planning guide',
                'seo_description' => 'Updated search summary.',
                'status' => 'draft',
                'cover_image_alt' => 'Updated cover description',
            ])
            ->assertRedirect('/admin/blog');

        $post->refresh();
        $this->assertSame($oldImage, $post->cover_image);
        $this->assertSame('draft', $post->status);
        $this->assertSame(['planning', 'business'], $post->tags);
        $this->assertSame('Updated planning guide', $post->seo_title);
        $this->get('/blog/product-planning-guide')->assertNotFound();

        $this->withSession(['admin_id' => $admin->id])
            ->delete('/admin/blog/'.$post->id)
            ->assertRedirect('/admin/blog');
        $this->assertDatabaseMissing('blog_posts', ['id' => $post->id]);
        Storage::disk('public')->assertMissing($oldImage);
    }

    public function test_homepage_replaces_the_hero_with_illustrated_service_deliverables(): void
    {
        Service::create([
            'title' => 'Mobile App Development',
            'slug' => 'mobile-app-development',
            'short_description' => 'Mobile products for customer journeys.',
            'description' => 'Build mobile products.',
            'icon' => 'bi bi-phone',
            'status' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('assets/css/button-polish.css')
            ->assertSee('class="site-hero"', false)
            ->assertSee('assets/img/stock/web-platforms.jpg')
            ->assertSee('site-hero-carousel')
            ->assertSee('Show slide 3')
            ->assertSee('assets/img/stock/mobile-apps.jpg')
            ->assertSee('assets/img/stock/product-design.jpg')
            ->assertSee('Digital solutions, delivered end to end.')
            ->assertSee('Empowering digital')
            ->assertSee('growth for your business.')
            ->assertSee('digital experiences that convert.')
            ->assertSee('software built to scale.')
            ->assertSee('data-rotating-phrases', false)
            ->assertSee('OUR TOOLKIT')
            ->assertSee('The right tools for reliable digital products.')
            ->assertSee('Laravel')
            ->assertSee('JavaScript')
            ->assertSee('AWS')
            ->assertSee('site-footer')
            ->assertSee('Discuss your project')
            ->assertSee('Typical deliverables')
            ->assertSee('App screens for the agreed iOS and Android scope')
            ->assertSee('assets/img/stock/mobile-apps.jpg')
            ->assertSee('e-Linx Technology logo')
            ->assertSee('class="skip-link"', false)
            ->assertDontSee('artificial intelligence', false)
            ->assertDontSee('Engineering ideas into scalable digital products')
            ->assertDontSee('DIGITAL PRODUCT STUDIO');

        $this->get('/services')
            ->assertOk()
            ->assertSee('assets/img/stock/mobile-apps.jpg')
            ->assertSee('Device testing and release handoff')
            ->assertDontSee('artificial intelligence', false);

        $this->get('/services/mobile-app-development')
            ->assertOk()
            ->assertSee('Typical deliverables')
            ->assertSee('API connections and key user flows');
    }

    public function test_all_service_artwork_exists_locally(): void
    {
        $services = [
            new Service(['title' => 'Web & SaaS Engineering', 'slug' => 'web-saas-engineering']),
            new Service(['title' => 'Laravel & PHP Development', 'slug' => 'laravel-php-development']),
            new Service(['title' => 'Mobile App Development', 'slug' => 'mobile-app-development']),
            new Service(['title' => 'UI/UX & Product Design', 'slug' => 'ui-ux-product-design']),
            new Service(['title' => 'YouTube Automation', 'slug' => 'youtube-automation']),
            new Service(['title' => 'SEO & Digital Growth', 'slug' => 'seo-digital-growth']),
        ];

        foreach ($services as $service) {
            $this->assertFileExists(public_path($service->artworkPath()));
        }

        $youtubeAutomation = $services[4];
        $this->assertSame('assets/img/services/youtube-automation.jpg', $youtubeAutomation->artworkPath());
        $this->assertContains('YouTube channel strategy and content calendar', $youtubeAutomation->deliverables());

        foreach (['crm.jpg', 'commerce.jpg', 'desk.jpg', 'insight.jpg'] as $image) {
            $this->assertFileExists(public_path('assets/img/stock/'.$image));
        }
    }

    public function test_existing_cloud_service_is_migrated_to_youtube_automation(): void
    {
        Service::create([
            'title' => 'Cloud & DevOps',
            'slug' => 'cloud',
            'short_description' => 'Reliable infrastructure from dev to production.',
            'description' => 'Cloud services and deployment pipelines.',
            'icon' => 'bi bi-cloud-check',
            'status' => true,
        ]);

        $migration = require database_path('migrations/2026_10_12_000001_replace_cloud_service_with_youtube_automation.php');
        $migration->up();

        $this->assertDatabaseHas('services', [
            'title' => 'YouTube Automation',
            'slug' => 'youtube-automation',
            'icon' => 'bi bi-youtube',
        ]);
        $this->assertDatabaseMissing('services', ['slug' => 'cloud']);
    }

    public function test_portfolio_cards_use_local_category_appropriate_artwork(): void
    {
        Portfolio::create([
            'title' => 'Mobile customer platform',
            'category' => 'Mobile App',
            'description' => 'A focused mobile experience.',
            'icon' => 'bi bi-phone',
            'status' => true,
        ]);

        $this->get('/portfolio')
            ->assertOk()
            ->assertSee('Mobile customer platform')
            ->assertSee('assets/img/stock/mobile-apps.jpg')
            ->assertSee('work-card-image')
            ->assertSee('Discuss a similar project')
            ->assertSee('Read project story')
            ->assertDontSee('href="#"');
        $this->assertFileExists(public_path('assets/img/stock/mobile-apps.jpg'));
    }

    public function test_contact_and_quote_forms_create_leads_and_notify_admin(): void
    {
        Mail::fake();
        config([
            'mail.default' => 'smtp',
            'mail.notifications.to' => 'adminelinx@gmail.com',
            'mail.mailers.smtp.username' => 'sender@example.com',
            'mail.mailers.smtp.password' => 'test-app-password',
        ]);

        $this->from('/contact')->post('/contact', [
            'name' => 'Test Contact',
            'email' => 'contact@example.com',
            'message' => 'Please contact me.',
        ])->assertRedirect('/contact');

        Service::create([
            'title' => 'Web / SaaS',
            'slug' => 'web-saas',
            'status' => true,
        ]);
        $this->from('/quote')->post('/quote', [
            'name' => 'Test Quote',
            'email' => 'quote@example.com',
            'service' => 'Web / SaaS',
            'budget' => 'Not sure yet',
            'message' => 'I need a new platform.',
            'project_stage' => 'New idea',
            'timeline' => 'Flexible / still planning',
        ])->assertRedirect('/quote');

        $this->assertDatabaseHas('leads', ['email' => 'contact@example.com', 'type' => 'contact']);
        $this->assertDatabaseHas('leads', ['email' => 'quote@example.com', 'type' => 'quote']);
        Mail::assertSent(LeadNotification::class, 2);
        Mail::assertSent(LeadNotification::class, fn (LeadNotification $mail): bool => $mail->hasTo('adminelinx@gmail.com')
            && $mail->lead->email === 'contact@example.com');
        Mail::assertSent(LeadNotification::class, fn (LeadNotification $mail): bool => $mail->hasTo('adminelinx@gmail.com')
            && $mail->lead->email === 'quote@example.com'
            && $mail->lead->subject === 'Project Quote · Web / SaaS');
    }

    public function test_quote_brief_saves_scope_in_admin_lead_inbox(): void
    {
        Service::create([
            'title' => 'Mobile App Development',
            'slug' => 'mobile-app-development',
            'short_description' => 'Mobile applications for customers.',
            'status' => true,
        ]);
        $this->from('/quote')->post('/quote', [
            'name' => 'Project Owner',
            'email' => 'owner@example.com',
            'company_name' => 'Example Company',
            'phone' => '+923001234567',
            'service' => 'Mobile App Development',
            'project_name' => 'Customer app',
            'message' => 'Customers need an easier way to book services.',
            'required_features' => ['Mobile application', 'Booking or scheduling'],
            'project_stage' => 'Improve an existing product',
            'timeline' => '1–3 months',
            'budget' => 'PKR 300,000–700,000',
            'target_audience' => 'Existing customers',
            'reference_url' => 'https://example.com/current-app',
        ])->assertRedirect('/quote');

        $lead = Lead::where('email', 'owner@example.com')->firstOrFail();
        $this->assertSame('Project Quote · Mobile App Development', $lead->subject);
        $this->assertSame('Example Company', $lead->company_name);
        $this->assertSame('Customer app', $lead->project_name);
        $this->assertSame('Improve an existing product', $lead->project_stage);
        $this->assertSame('1–3 months', $lead->timeline);
        $this->assertSame(['Mobile application', 'Booking or scheduling'], $lead->required_features);
        $this->assertSame('Existing customers', $lead->target_audience);
        $this->assertSame('https://example.com/current-app', $lead->reference_url);

        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);
        $this->withSession(['admin_id' => $admin->id])
            ->get('/admin/leads')
            ->assertOk()
            ->assertSee('Mobile App Development')
            ->assertSee('Project Owner')
            ->assertSee('View full project brief')
            ->assertSee('Scope & planning')
            ->assertSee('Requested features')
            ->assertSee('PKR 300,000–700,000');
    }

    public function test_youtube_automation_quote_is_accepted_by_the_backend(): void
    {
        Service::create([
            'title' => 'YouTube Automation',
            'slug' => 'youtube-automation',
            'status' => true,
        ]);

        $this->from('/quote')->post('/quote', [
            'name' => 'Creator',
            'email' => 'creator@example.com',
            'service' => 'YouTube Automation',
            'message' => 'I need a consistent publishing workflow for my channel.',
            'project_stage' => 'New idea',
            'timeline' => 'Flexible / still planning',
            'budget' => 'Not sure yet',
        ])->assertRedirect('/quote');

        $this->assertDatabaseHas('leads', [
            'email' => 'creator@example.com',
            'subject' => 'Project Quote · YouTube Automation',
            'type' => 'quote',
        ]);
    }

    public function test_quote_form_rejects_services_not_offered_and_invalid_reference_links(): void
    {
        Service::create([
            'title' => 'Mobile App Development',
            'slug' => 'mobile-app-development',
            'status' => true,
        ]);

        $this->from('/quote')->post('/quote', [
            'name' => 'Unqualified Lead',
            'email' => 'unqualified@example.com',
            'service' => 'A service e-Linx does not offer',
            'message' => 'Please build my project.',
            'project_stage' => 'New idea',
            'timeline' => 'Flexible / still planning',
            'budget' => 'Not sure yet',
            'reference_url' => 'javascript:alert(1)',
            'required_features' => 'not-an-array',
        ])->assertRedirect('/quote')
            ->assertSessionHasErrors(['service', 'reference_url', 'required_features']);

        $this->assertDatabaseMissing('leads', ['email' => 'unqualified@example.com']);
    }

    public function test_product_catalog_shows_product_art_and_search_controls(): void
    {
        Product::create([
            'name' => 'e-Linx CRM',
            'category' => 'Business',
            'description' => 'Manage customer relationships.',
            'icon' => 'bi bi-people',
            'status' => true,
        ]);

        $this->get('/products')
            ->assertOk()
            ->assertSee('e-Linx CRM')
            ->assertSee('data-filter="business"', false)
            ->assertSee('assets/img/stock/crm.jpg')
            ->assertSee('Sales pipeline visibility')
            ->assertSee('/quote?product=e-Linx%20CRM');

        $this->assertFileExists(public_path('assets/img/stock/crm.jpg'));
        $this->assertFileExists(public_path('assets/js/products.js'));
    }

    public function test_product_quote_context_is_carried_into_the_saved_lead(): void
    {
        $this->get('/quote?product=e-Linx%20CRM')
            ->assertOk()
            ->assertSee('PRODUCT ENQUIRY')
            ->assertDontSee('AI / Automation')
            ->assertSee('name="product" value="e-Linx CRM"', false);

        $this->from('/quote?product=e-Linx%20CRM')->post('/quote', [
            'name' => 'Product Lead',
            'email' => 'product@example.com',
            'product' => 'e-Linx CRM',
            'service' => 'Laravel & PHP Development',
            'project_stage' => 'New idea',
            'timeline' => 'Flexible / still planning',
            'budget' => 'Not sure yet',
            'message' => 'Please tell me about the CRM.',
        ])->assertRedirect('/quote?product=e-Linx%20CRM');

        $this->assertDatabaseHas('leads', [
            'email' => 'product@example.com',
            'type' => 'quote',
            'subject' => 'Product enquiry · e-Linx CRM',
        ]);
    }

    public function test_contact_and_quote_honeypots_reject_filled_fields(): void
    {
        $this->from('/contact')->post('/contact', [
            'name' => 'Spam Contact',
            'email' => 'spam-contact@example.com',
            'message' => 'Spam.',
            'company_website' => 'https://spam.example',
        ])->assertRedirect('/contact')->assertSessionHasErrors('company_website');

        $this->from('/quote')->post('/quote', [
            'name' => 'Spam Quote',
            'email' => 'spam-quote@example.com',
            'message' => 'Spam.',
            'company_website' => 'https://spam.example',
        ])->assertRedirect('/quote')->assertSessionHasErrors('company_website');

        $this->assertDatabaseMissing('leads', ['email' => 'spam-contact@example.com']);
        $this->assertDatabaseMissing('leads', ['email' => 'spam-quote@example.com']);
    }

    public function test_robots_and_sitemap_routes_use_absolute_urls_and_include_active_services(): void
    {
        $service = Service::create([
            'title' => 'Active Service',
            'slug' => 'active-service',
            'short_description' => 'A public service.',
            'status' => true,
        ]);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertSee('Sitemap: '.route('sitemap'))
            ->assertDontSee('Sitemap: /sitemap.xml');

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('content-type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('home').'</loc>', false)
            ->assertSee('<loc>'.route('service.show', $service).'</loc>', false);
    }

    public function test_contact_form_reports_when_email_is_not_configured(): void
    {
        config(['mail.default' => 'log']);

        $this->from('/contact')->post('/contact', [
            'name' => 'Mail Test',
            'email' => 'mail-test@example.com',
            'message' => 'Please save this contact request.',
        ])->assertRedirect('/contact')
            ->assertSessionHas('warning');

        $this->assertDatabaseHas('leads', ['email' => 'mail-test@example.com', 'type' => 'contact']);
    }

    public function test_resend_notifications_report_when_api_key_is_missing(): void
    {
        config([
            'mail.default' => 'resend',
            'mail.notifications.to' => 'admin@example.com',
            'services.resend.key' => null,
        ]);

        $this->from('/contact')->post('/contact', [
            'name' => 'Resend Test',
            'email' => 'resend-test@example.com',
            'message' => 'Please notify the admin.',
        ])->assertRedirect('/contact')
            ->assertSessionHas('warning');

        $this->assertDatabaseHas('leads', ['email' => 'resend-test@example.com', 'type' => 'contact']);
    }

    public function test_smtp_notifications_report_when_credentials_are_missing(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.notifications.to' => 'admin@example.com',
            'mail.mailers.smtp.username' => 'admin@example.com',
            'mail.mailers.smtp.password' => '',
        ]);

        $this->from('/contact')->post('/contact', [
            'name' => 'SMTP Test',
            'email' => 'smtp-test@example.com',
            'message' => 'Please notify the admin.',
        ])->assertRedirect('/contact')
            ->assertSessionHas('warning');

        $this->assertDatabaseHas('leads', ['email' => 'smtp-test@example.com', 'type' => 'contact']);
    }

    public function test_admin_routes_require_an_admin_session(): void
    {
        $loginPage = $this->get('/admin/login');
        $loginPage
            ->assertOk()
            ->assertSee('Welcome back')
            ->assertSee('SECURE WORKSPACE')
            ->assertSee('Sign in to dashboard')
            ->assertSee('assets/img/elinxlogo.png')
            ->assertSee('assets/css/admin.css?v=5');
        $this->assertStringContainsString('no-store', (string) $loginPage->headers->get('Cache-Control'));
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/leads')->assertRedirect('/admin/login');
        $this->get('/admin/blog')->assertRedirect('/admin/login');
    }

    public function test_footer_admin_entry_always_requires_fresh_login(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);

        $entry = $this->withSession(['admin_id' => $admin->id])
            ->post('/admin/access');

        $entry->assertRedirect('/admin/login');
        $this->assertStringContainsString('no-store', (string) $entry->headers->get('Cache-Control'));
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk()->assertSee('Welcome back');

        $this->post('/admin/login', [
            'login' => 'admin@example.com',
            'password' => 'a-secure-password',
        ])->assertRedirect('/admin');
        $this->get('/admin')->assertOk();
    }

    public function test_admin_logout_invalidates_session_and_protected_pages_are_not_cached(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);

        $leadsPage = $this->withSession(['admin_id' => $admin->id])
            ->get('/admin/leads');
        $leadsPage
            ->assertOk()
            ->assertHeader('Pragma', 'no-cache');
        $this->assertStringContainsString('no-store', (string) $leadsPage->headers->get('Cache-Control'));

        $logout = $this->post('/admin/logout');
        $logout->assertRedirect('/admin/login');
        $this->assertStringContainsString('no-store', (string) $logout->headers->get('Cache-Control'));

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Welcome back')
            ->assertSee('You have been securely signed out.');
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/leads')->assertRedirect('/admin/login');
        $this->get('/admin/profile')->assertRedirect('/admin/login');
    }

    public function test_stale_admin_session_is_rejected_and_cannot_open_login_redirect_loop(): void
    {
        $response = $this->withSession(['admin_id' => 999999])->get('/admin');
        $response->assertRedirect('/admin/login');
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Welcome back');
    }

    public function test_admin_password_change_requires_a_strong_confirmed_password(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);

        $this->withSession(['admin_id' => $admin->id])
            ->put('/admin/password', [
                'current' => 'a-secure-password',
                'new' => 'weakpassword',
                'new_confirmation' => 'weakpassword',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('new');

        $this->withSession(['admin_id' => $admin->id])
            ->put('/admin/password', [
                'current' => 'a-secure-password',
                'new' => 'StrongPassword2026!',
                'new_confirmation' => 'StrongPassword2026!',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('StrongPassword2026!', $admin->fresh()->password));
    }

    public function test_admin_can_log_in_and_render_management_pages(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);

        $this->post('/admin/login', [
            'login' => 'admin@example.com',
            'password' => 'a-secure-password',
        ])->assertRedirect('/admin');

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Recent enquiries')
            ->assertSee('LEAD PIPELINE')
            ->assertSee('QUICK ACCESS')
            ->assertSee('toastify-js@1.12.0');
        $this->assertStringContainsString('admin.css?v=5', $this->get('/admin')->getContent());
        $this->assertStringContainsString('class="danger" type="submit" role="menuitem"', $this->get('/admin')->getContent());
        $this->get('/admin/leads')->assertOk();
        $this->get('/admin/content/services')->assertOk();
        $this->get('/admin/content/products')->assertOk();
        $this->get('/admin/content/portfolio')->assertOk();
        $this->get('/admin/profile')->assertOk();
        $this->assertSame($admin->id, session('admin_id'));
    }

    public function test_admin_can_create_and_update_public_content(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);

        $this->withSession(['admin_id' => $admin->id])
            ->post('/admin/content/services', [
                'title' => 'Laravel Development',
                'short_description' => 'Reliable web platforms',
                'description' => 'Custom Laravel applications.',
                'icon' => 'bi bi-code-slash',
                'sort_order' => 2,
                'status' => '1',
            ])->assertRedirect('/admin/content/services');

        $this->assertDatabaseHas('services', [
            'slug' => 'laravel-development',
            'status' => 1,
            'title' => 'Laravel Development',
        ]);
        $this->get('/services/laravel-development')->assertOk();
    }

    public function test_admin_can_receive_new_lead_notifications(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);
        $lead = Lead::create([
            'name' => 'New Lead',
            'email' => 'lead@example.com',
            'subject' => 'A project',
            'message' => 'A test enquiry.',
            'type' => 'quote',
            'status' => 'new',
        ]);

        $this->withSession(['admin_id' => $admin->id])
            ->getJson('/admin/notifications?after=0')
            ->assertOk()
            ->assertJsonPath('leads.0.id', $lead->id)
            ->assertJsonPath('leads.0.type', 'quote')
            ->assertJsonPath('new_count', 1);
    }
}
