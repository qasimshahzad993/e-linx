<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Course;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\Testimonial;
use Database\Seeders\FaqSeeder;
use Database\Seeders\CourseSeeder;
use Database\Seeders\MarketingServicesSeeder;
use Database\Seeders\ServiceSolutionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TrustContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_faq_seeder_is_idempotent_and_preserves_admin_edits(): void
    {
        $seeder = app(FaqSeeder::class);
        $seeder->run();

        $this->assertSame(6, Faq::count());
        $faq = Faq::firstOrFail();
        $faq->update(['answer' => 'Owner-edited answer.']);

        $seeder->run();

        $this->assertSame(6, Faq::count());
        $this->assertSame('Owner-edited answer.', $faq->fresh()->answer);
        $this->assertSame(0, Testimonial::count());
    }

    public function test_digital_marketing_services_are_added_idempotently_without_changing_branding(): void
    {
        $seeder = app(MarketingServicesSeeder::class);
        app(FaqSeeder::class)->run();
        $seeder->run();
        $seeder->run();

        $this->assertSame(17, Service::count());
        $this->assertDatabaseHas('services', [
            'title' => 'Social Media Marketing',
            'slug' => 'social-media-marketing',
            'status' => true,
        ]);
        $this->assertDatabaseHas('services', [
            'title' => 'Paid Advertising & PPC',
            'slug' => 'paid-advertising-ppc',
            'status' => true,
            'category' => 'Digital Marketing',
        ]);
        $this->get('/')
            ->assertOk()
            ->assertSee('e-Linx Technology logo')
            ->assertSee('Social Media Marketing')
            ->assertSee('Paid Advertising & PPC')
            ->assertSee('class="faq-section-heading"', false);
        $this->get('/services')
            ->assertOk()
            ->assertSee('Paid Advertising & PPC')
            ->assertSee('Influencer & Creator Marketing')
            ->assertSee('Social Media Audit')
            ->assertSee('Video Production & Editing')
            ->assertSee('Marketplace Listing Optimization')
            ->assertSee('Google Maps Advertising');
        $this->get('/quote')
            ->assertOk()
            ->assertSee('Social Media Marketing')
            ->assertSee('Paid Advertising & PPC');
    }

    public function test_service_families_have_navigation_and_scoped_proposal_intake(): void
    {
        app(MarketingServicesSeeder::class)->run();
        $seeder = app(ServiceSolutionsSeeder::class);
        $seeder->run();
        $seeder->run();

        $this->assertSame(3, Service::where('category', 'Website Solutions')->count());
        $this->assertSame(17, Service::where('category', 'Digital Marketing')->count());
        $this->assertSame(5, Service::where('category', 'Mystery Shopping')->count());
        $this->assertSame(5, Service::where('category', 'AI Solutions')->count());

        $this->get('/')
            ->assertOk()
            ->assertSee('Digital Marketing')
            ->assertSee('Website Solutions')
            ->assertSee('Mystery Shopping')
            ->assertSee('AI Solutions')
            ->assertSee('AI Marketing Solutions')
            ->assertSee('CRM Solutions')
            ->assertSee('Request a Proposal');
        $this->get('/services#mystery-shopping')
            ->assertOk()
            ->assertSee('Video Mystery Shopping')
            ->assertSee('Competitive Mystery Shopping');
        $this->get('/services/ai-agent-development')
            ->assertOk()
            ->assertSee('human review')
            ->assertSee('Request a proposal');

        $this->get('/quote?service=Mystery%20Shopping')
            ->assertOk()
            ->assertSee('data-category="Mystery Shopping"', false)
            ->assertSee('Mystery Shopping scope')
            ->assertSee('Customer touchpoints to review')
            ->assertSee('service_details[channels][]');
        $this->get('/quote?service=AI%20Agent%20Development')
            ->assertOk()
            ->assertSee('AI solution scope')
            ->assertSee('service_details[ai_workflow]');

        $this->from('/quote')->post('/quote', [
            'name' => 'Mystery Shopping Lead',
            'email' => 'mystery@example.com',
            'service' => 'Mystery Shopping',
            'message' => 'We want to review customer journeys in three locations.',
            'project_stage' => 'New idea',
            'timeline' => '1–3 months',
            'budget' => 'Not sure yet',
            'service_details' => [
                'locations' => 'Three branches in Lahore',
                'channels' => ['In-person visit', 'Telephone enquiry'],
                'frequency' => 'Recurring assessment',
            ],
            'company_website' => '',
        ])->assertRedirect('/quote');

        $lead = Lead::where('email', 'mystery@example.com')->firstOrFail();
        $this->assertSame('Three branches in Lahore', $lead->service_details['locations']);
        $this->assertSame(['In-person visit', 'Telephone enquiry'], $lead->service_details['channels']);

        $this->from('/quote')->post('/quote', [
            'name' => 'AI Solution Lead',
            'email' => 'ai@example.com',
            'service' => 'AI Agent Development',
            'message' => 'We want to scope an assistant for routine booking questions.',
            'project_stage' => 'New idea',
            'timeline' => 'Flexible / still planning',
            'budget' => 'Not sure yet',
            'service_details' => [
                'ai_workflow' => 'Answer booking questions and send complex requests to staff.',
                'ai_systems' => 'Approved booking FAQs.',
                'human_review' => 'Staff should handle exceptions and uncertain answers.',
            ],
            'company_website' => '',
        ])->assertRedirect('/quote');

        $aiLead = Lead::where('email', 'ai@example.com')->firstOrFail();
        $this->assertSame('Answer booking questions and send complex requests to staff.', $aiLead->service_details['ai_workflow']);
        $this->assertSame('Approved booking FAQs.', $aiLead->service_details['ai_systems']);

        $admin = Admin::create([
            'name' => 'Service Admin',
            'email' => 'service-admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);
        $this->withSession([
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'admin_email' => $admin->email,
        ])->get('/admin/leads')
            ->assertOk()
            ->assertSee('Service-specific requirements')
            ->assertSee('Three branches in Lahore')
            ->assertSee('In-person visit, Telephone enquiry')
            ->assertSee('Answer booking questions and send complex requests to staff.')
            ->assertSee('Staff should handle exceptions and uncertain answers.');
    }

    public function test_courses_can_be_published_and_course_enquiries_are_saved_as_leads(): void
    {
        app(CourseSeeder::class)->run();
        $this->assertSame(4, Course::count());
        $this->assertSame(0, Course::where('status', true)->count());
        $this->assertDatabaseHas('courses', [
            'title' => 'Digital Marketing Fundamentals',
            'status' => false,
        ]);

        $course = Course::where('title', 'Digital Marketing Fundamentals')->firstOrFail();
        $course->update(['status' => true]);

        $this->get('/courses')
            ->assertOk()
            ->assertSee('Digital Marketing Fundamentals')
            ->assertSee('e-Linx Technology logo');
        $this->get('/courses/'.$course->slug)
            ->assertOk()
            ->assertSee('Audience research and campaign objectives')
            ->assertSee('Send course enquiry');

        $this->from('/courses/'.$course->slug)->post('/courses/'.$course->slug.'/enquiry', [
            'name' => 'Course Learner',
            'email' => 'learner@example.com',
            'phone' => '+92 300 1234567',
            'message' => 'I would like to ask about the course schedule.',
            'company_website' => '',
        ])->assertRedirect('/courses/'.$course->slug);

        $this->assertDatabaseHas('leads', [
            'name' => 'Course Learner',
            'email' => 'learner@example.com',
            'subject' => 'Training enquiry · Digital Marketing Fundamentals',
            'type' => 'course',
            'message' => 'I would like to ask about the course schedule.',
        ]);
        $this->assertInstanceOf(Lead::class, Lead::where('email', 'learner@example.com')->firstOrFail());

        $course->update(['status' => false]);
        $this->get('/courses/'.$course->slug)->assertNotFound();
    }

    public function test_admin_can_create_edit_and_delete_course_outlines(): void
    {
        $admin = Admin::create([
            'name' => 'Course Admin',
            'email' => 'course-admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);
        $session = ['admin_id' => $admin->id, 'admin_name' => $admin->name, 'admin_email' => $admin->email];

        $this->withSession($session)
            ->get('/admin/courses')
            ->assertOk()
            ->assertSee('Training courses');

        $this->withSession($session)->post('/admin/courses', [
            'title' => 'Web Publishing Basics',
            'category' => 'Web Development',
            'summary' => 'Learn the foundations of web publishing.',
            'description' => 'An introductory course outline.',
            'curriculum' => "HTML basics\nPublishing a website",
            'duration' => 'Add confirmed duration',
            'delivery_mode' => 'Online',
            'price' => '',
            'sort_order' => 1,
            'status' => '1',
        ])->assertRedirect('/admin/courses');

        $course = Course::where('slug', 'web-publishing-basics')->firstOrFail();
        $this->assertTrue($course->status);
        $this->get('/courses/'.$course->slug)->assertOk()->assertSee('Learn the foundations');

        $this->withSession($session)->post('/admin/courses', [
            'id' => $course->id,
            'title' => 'Web Publishing Basics',
            'category' => 'Web Development',
            'summary' => 'Updated course summary.',
            'description' => 'An introductory course outline.',
            'curriculum' => "HTML basics\nPublishing a website",
            'sort_order' => 1,
        ])->assertRedirect('/admin/courses');
        $this->assertFalse($course->fresh()->status);

        $this->withSession($session)->delete('/admin/courses/'.$course->id)
            ->assertRedirect('/admin/courses');
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_portfolio_case_studies_publish_approved_details_and_hide_unpublished_projects(): void
    {
        $project = Portfolio::create([
            'title' => 'Approved campaign landing page',
            'category' => 'Digital Growth',
            'description' => 'A scoped campaign landing page.',
            'challenge' => 'The campaign needed a clearer enquiry path.',
            'solution' => 'The team delivered a focused responsive landing page.',
            'outcome' => 'The delivered page was handed off with analytics events configured.',
            'status' => true,
        ]);
        $draft = Portfolio::create([
            'title' => 'Private draft project',
            'category' => 'Software',
            'description' => 'This project is not ready to publish.',
            'status' => false,
        ]);

        $this->get('/portfolio')
            ->assertOk()
            ->assertSee('Read project story');
        $this->get('/portfolio/'.$project->id)
            ->assertOk()
            ->assertSee('The campaign needed a clearer enquiry path.')
            ->assertSee('The team delivered a focused responsive landing page.')
            ->assertSee('analytics events configured');
        $this->get('/portfolio/'.$draft->id)->assertNotFound();
        $this->get('/sitemap.xml')->assertOk()->assertSee(route('portfolio.show', $project))
            ->assertDontSee(route('portfolio.show', $draft));

        $admin = Admin::create([
            'name' => 'Portfolio Admin',
            'email' => 'portfolio-admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);
        $this->withSession([
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'admin_email' => $admin->email,
        ])->get('/admin/content/portfolio?edit='.$project->id)
            ->assertOk()
            ->assertSee('Client challenge')
            ->assertSee('Our approach')
            ->assertSee('Outcome (optional)');
    }

    public function test_public_faq_page_and_homepage_show_only_published_content(): void
    {
        Faq::create([
            'question' => 'How do I start a project?',
            'answer' => 'Send us a project brief.',
            'sort_order' => 1,
            'status' => true,
        ]);
        Faq::create([
            'question' => 'Internal draft question',
            'answer' => 'This answer is not public.',
            'sort_order' => 2,
            'status' => false,
        ]);
        Testimonial::create([
            'client_name' => 'A Client',
            'company_name' => 'Example Company',
            'quote' => 'The delivered project matched our agreed scope.',
            'status' => true,
        ]);
        Testimonial::create([
            'client_name' => 'Private Client',
            'quote' => 'This feedback is not yet approved.',
            'status' => false,
        ]);

        $this->get('/faqs')
            ->assertOk()
            ->assertSee('How do I start a project?')
            ->assertSee('Send us a project brief.')
            ->assertDontSee('Internal draft question')
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('"@type":"Question"', false);

        $this->get('/')
            ->assertOk()
            ->assertSee('How do I start a project?')
            ->assertSee('A Client')
            ->assertSee('Example Company')
            ->assertDontSee('Internal draft question')
            ->assertDontSee('Private Client');

        $this->get('/sitemap.xml')->assertOk()->assertSee(route('faqs'));
    }

    public function test_admin_can_create_hide_and_delete_faqs_and_testimonials(): void
    {
        $admin = Admin::create([
            'name' => 'Content Admin',
            'email' => 'content-admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);
        $session = [
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'admin_email' => $admin->email,
        ];

        $this->withSession($session)
            ->get('/admin/trust/faqs')
            ->assertOk()
            ->assertSee('Answer common questions');

        $this->withSession($session)
            ->post('/admin/trust/faqs', [
                'question' => 'Where can I see e-Linx services?',
                'answer' => 'Visit the services page.',
                'sort_order' => 1,
                'status' => '1',
            ])
            ->assertRedirect('/admin/trust/faqs');

        $faq = Faq::where('question', 'Where can I see e-Linx services?')->firstOrFail();
        $this->assertTrue($faq->status);
        $this->get('/faqs')->assertSee('Where can I see e-Linx services?');

        $this->withSession($session)
            ->post('/admin/trust/testimonials', [
                'client_name' => 'Approved Client',
                'client_role' => 'Product Lead',
                'company_name' => 'Example Studio',
                'service' => 'Web platform',
                'quote' => 'We received a clear project handoff.',
                'sort_order' => 1,
                'status' => '1',
            ])
            ->assertRedirect('/admin/trust/testimonials');

        $testimonial = Testimonial::where('client_name', 'Approved Client')->firstOrFail();
        $this->get('/')->assertSee('Approved Client')->assertSee('We received a clear project handoff.');

        $this->withSession($session)
            ->post('/admin/trust/testimonials', [
                'id' => $testimonial->id,
                'client_name' => 'Approved Client',
                'client_role' => 'Product Lead',
                'company_name' => 'Example Studio',
                'service' => 'Web platform',
                'quote' => 'We received a clear project handoff.',
                'sort_order' => 1,
            ])
            ->assertRedirect('/admin/trust/testimonials');
        $this->assertFalse($testimonial->fresh()->status);
        $this->get('/')->assertDontSee('Approved Client');

        $this->withSession($session)
            ->delete('/admin/trust/faqs/'.$faq->id)
            ->assertRedirect('/admin/trust/faqs');
        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }
}
