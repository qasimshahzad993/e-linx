<?php

namespace App\Http\Controllers;

use App\Mail\LeadNotification;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class SiteController extends Controller
{
    private const QUOTE_FEATURES = [
        'Website or web portal',
        'Mobile application',
        'Online payments or e-commerce',
        'Customer or staff dashboard',
        'Booking or scheduling',
        'Reports and analytics',
        'Connect existing systems or APIs',
        'Ongoing support and improvements',
    ];

    private const PROJECT_STAGES = [
        'New idea',
        'Improve an existing product',
        'Replace an existing system',
        'Add a feature to an existing product',
    ];

    private const PROJECT_TIMELINES = [
        'As soon as possible',
        'Within 1 month',
        '1–3 months',
        '3–6 months',
        'Flexible / still planning',
    ];

    private const PROJECT_BUDGETS = [
        'Not sure yet',
        'Under PKR 100,000',
        'PKR 100,000–300,000',
        'PKR 300,000–700,000',
        'PKR 700,000+',
    ];

    private const MYSTERY_SHOPPING_CHANNELS = [
        'In-person visit',
        'Video observation',
        'Telephone enquiry',
        'Website journey',
        'Mobile app journey',
    ];

    private const MYSTERY_SHOPPING_FREQUENCIES = [
        'One-time assessment',
        'Recurring assessment',
        'Not sure yet',
    ];

    public function home(): View
    {
        return view('home', [
            'services' => Service::where('status', true)->orderBy('sort_order')->take(9)->get(),
            'projects' => Portfolio::where('status', true)->latest()->take(3)->get(),
            'courses' => Course::where('status', true)->orderBy('sort_order')->take(3)->get(),
            'products' => Product::where('status', true)->latest()->take(3)->get(),
            'latestPosts' => BlogPost::published()->orderByDesc('published_at')->take(3)->get(),
            'faqs' => Faq::where('status', true)->orderBy('sort_order')->take(4)->get(),
            'testimonials' => Testimonial::where('status', true)->orderBy('sort_order')->take(3)->get(),
        ]);
    }

    public function about(): View
    {
        return view('about');
    }

    public function services(): View
    {
        $services = Service::where('status', true)->orderBy('sort_order')->orderBy('title')->get();

        return view('services', [
            'serviceGroups' => $services->groupBy(fn (Service $service): string => $service->category ?: 'Website Solutions'),
        ]);
    }

    public function service(Service $service): View
    {
        abort_unless($service->status, 404);

        return view('service', compact('service'));
    }

    public function products(): View
    {
        return view('products', [
            'products' => Product::where('status', true)->latest()->get(),
        ]);
    }

    public function portfolio(): View
    {
        return view('portfolio', [
            'projects' => Portfolio::where('status', true)->latest()->get(),
        ]);
    }

    public function courses(): View
    {
        return view('courses.index', [
            'courses' => Course::where('status', true)->orderBy('sort_order')->orderBy('title')->get(),
        ]);
    }

    public function course(Course $course): View
    {
        abort_unless($course->status, 404);

        return view('courses.show', compact('course'));
    }

    public function contact(): View
    {
        return view('contact');
    }

    public function contactStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:80'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'company_website' => ['prohibited'],
        ]);

        $lead = Lead::create($data + ['type' => 'contact', 'status' => 'new']);

        $redirect = back();

        return $this->notifyAdmin($lead)
            ? $redirect->with('success', 'Thank you for your enquiry. Our team will review your message and contact you shortly.')
            : $redirect->with('warning', 'Your enquiry was received, but the admin email notification could not be sent. Our team can still review it in the admin leads inbox.');
    }

    public function courseEnquiryStore(Request $request, Course $course): RedirectResponse
    {
        abort_unless($course->status, 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:80'],
            'message' => ['required', 'string', 'max:10000'],
            'company_website' => ['prohibited'],
        ]);
        $lead = Lead::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => 'Training enquiry · '.$course->title,
            'message' => $data['message'],
            'type' => 'course',
            'status' => 'new',
        ]);
        $redirect = back();

        return $this->notifyAdmin($lead)
            ? $redirect->with('success', 'Thank you for your course enquiry. Our team will follow up with you.')
            : $redirect->with('warning', 'Your course enquiry was received, but the admin email notification could not be sent. Our team can still review it in the admin leads inbox.');
    }

    public function quote(Request $request): View
    {
        $context = $request->validate([
            'service' => ['nullable', 'string', 'max:160'],
            'product' => ['nullable', 'string', 'max:160'],
        ]);
        $services = Service::where('status', true)->orderBy('sort_order')->get();
        $selectedService = $services->firstWhere('title', $context['service'] ?? '');
        $serviceOptions = $services->isNotEmpty()
            ? $services
            : collect([
                (object) ['title' => 'Web & SaaS Engineering', 'short_description' => 'Responsive websites, business portals, dashboards, and SaaS platforms.', 'icon' => 'bi bi-window-stack'],
                (object) ['title' => 'Laravel & PHP Development', 'short_description' => 'Custom web applications, APIs, and system integrations.', 'icon' => 'bi bi-code-slash'],
                (object) ['title' => 'Mobile App Development', 'short_description' => 'iOS and Android applications designed around customer needs.', 'icon' => 'bi bi-phone'],
                (object) ['title' => 'UI/UX & Product Design', 'short_description' => 'User research, journey mapping, wireframes, and interface design.', 'icon' => 'bi bi-palette'],
                (object) ['title' => 'YouTube Automation', 'short_description' => 'Channel strategy, video publishing workflows, and audience growth insights.', 'icon' => 'bi bi-youtube'],
                (object) ['title' => 'SEO & Digital Growth', 'short_description' => 'Technical SEO and improvements to your digital visibility.', 'icon' => 'bi bi-graph-up-arrow'],
            ]);

        return view('quote', [
            'service' => $context['service'] ?? '',
            'serviceCategory' => $selectedService?->category,
            'product' => $context['product'] ?? '',
            'serviceOptions' => $serviceOptions,
            'featureOptions' => self::QUOTE_FEATURES,
            'projectStages' => self::PROJECT_STAGES,
            'projectTimelines' => self::PROJECT_TIMELINES,
            'budgetOptions' => self::PROJECT_BUDGETS,
            'mysteryShoppingChannels' => self::MYSTERY_SHOPPING_CHANNELS,
            'mysteryShoppingFrequencies' => self::MYSTERY_SHOPPING_FREQUENCIES,
        ]);
    }

    public function quoteStore(Request $request): RedirectResponse
    {
        $serviceOptions = Service::where('status', true)->pluck('title')->all();
        if ($serviceOptions === []) {
            $serviceOptions = [
                'Web & SaaS Engineering',
                'Laravel & PHP Development',
                'Mobile App Development',
                'UI/UX & Product Design',
                'YouTube Automation',
                'SEO & Digital Growth',
            ];
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:80'],
            'company_name' => ['nullable', 'string', 'max:180'],
            'service' => ['required', Rule::in([...$serviceOptions, 'Not sure yet — please advise', 'Product enquiry'])],
            'product' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:10000'],
            'project_name' => ['nullable', 'string', 'max:180'],
            'project_stage' => ['required', Rule::in(self::PROJECT_STAGES)],
            'timeline' => ['required', Rule::in(self::PROJECT_TIMELINES)],
            'target_audience' => ['nullable', 'string', 'max:500'],
            'required_features' => ['nullable', 'array', 'max:8'],
            'required_features.*' => ['string', Rule::in(self::QUOTE_FEATURES)],
            'reference_url' => ['nullable', 'url:http,https', 'max:500'],
            'service_details' => ['nullable', 'array'],
            'service_details.locations' => ['nullable', 'string', 'max:500'],
            'service_details.channels' => ['nullable', 'array', 'max:5'],
            'service_details.channels.*' => ['string', Rule::in(self::MYSTERY_SHOPPING_CHANNELS)],
            'service_details.frequency' => ['nullable', Rule::in(self::MYSTERY_SHOPPING_FREQUENCIES)],
            'service_details.ai_workflow' => ['nullable', 'string', 'max:3000'],
            'service_details.ai_systems' => ['nullable', 'string', 'max:1000'],
            'service_details.human_review' => ['nullable', 'string', 'max:1000'],
            'budget' => ['required', Rule::in(self::PROJECT_BUDGETS)],
            'company_website' => ['prohibited'],
        ]);

        $product = $data['product'] ?? null;
        $lead = Lead::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company_name' => $data['company_name'] ?? null,
            'project_name' => $data['project_name'] ?? null,
            'project_stage' => $data['project_stage'],
            'timeline' => $data['timeline'],
            'target_audience' => $data['target_audience'] ?? null,
            'required_features' => $data['required_features'] ?? [],
            'reference_url' => $data['reference_url'] ?? null,
            'service_details' => array_filter($data['service_details'] ?? [], static fn ($value): bool => filled($value)),
            'subject' => filled($product)
                ? 'Product enquiry · '.$product
                : 'Project Quote · '.$data['service'],
            'message' => $data['message'],
            'budget' => $data['budget'],
            'type' => 'quote',
            'status' => 'new',
        ]);
        $redirect = back();

        return $this->notifyAdmin($lead)
            ? $redirect->with('success', 'Thank you for your project enquiry. Our team will review your brief and contact you shortly.')
            : $redirect->with('warning', 'Your project enquiry was received, but the admin email notification could not be sent. Our team can still review it in the admin leads inbox.');
    }

    private function notifyAdmin(Lead $lead): bool
    {
        $recipient = config('mail.notifications.to');

        if (! is_string($recipient) || ! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            Log::error('Lead notification email was not sent because the admin email address is missing or invalid.', [
                'lead_id' => $lead->id,
            ]);

            return false;
        }

        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            Log::warning('Lead notification email is not deliverable with the configured mailer.', [
                'lead_id' => $lead->id,
                'mailer' => config('mail.default'),
            ]);

            return false;
        }

        if (config('mail.default') === 'resend' && ! filled(config('services.resend.key'))) {
            Log::error('Lead notification email was not sent because the Resend API key is missing.', [
                'lead_id' => $lead->id,
            ]);

            return false;
        }

        if (config('mail.default') === 'smtp'
            && (! filled(config('mail.mailers.smtp.username')) || ! filled(config('mail.mailers.smtp.password')))) {
            Log::error('Lead notification email was not sent because SMTP credentials are missing.', [
                'lead_id' => $lead->id,
            ]);

            return false;
        }

        try {
            Mail::to($recipient)->send(new LeadNotification($lead));

            return true;
        } catch (Throwable $exception) {
            Log::error('Failed to send lead notification email.', [
                'lead_id' => $lead->id,
                'exception' => $exception,
            ]);

            return false;
        }
    }
}
