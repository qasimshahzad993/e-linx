<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public const UPDATED_AT = null;

    public const CATEGORIES = [
        'Digital Marketing' => 'digital-marketing',
        'Website Solutions' => 'website-solutions',
        'Mystery Shopping' => 'mystery-shopping',
        'AI Solutions' => 'ai-solutions',
    ];

    protected $fillable = [
        'title', 'slug', 'category', 'short_description', 'description', 'icon', 'sort_order', 'status',
    ];

    public function artworkPath(): string
    {
        $service = strtolower($this->slug.' '.$this->title);

        return match (true) {
            str_contains($service, 'mobile') => 'assets/img/stock/mobile-apps.jpg',
            str_contains($service, 'design'), str_contains($service, 'ux'), str_contains($service, 'ui') => 'assets/img/stock/product-design.jpg',
            str_contains($service, 'youtube'), str_contains($service, 'automation') => 'assets/img/services/youtube-automation.jpg',
            str_contains($service, 'seo'), str_contains($service, 'growth'), str_contains($service, 'marketing'), str_contains($service, 'ppc'), str_contains($service, 'advertising'), str_contains($service, 'geo'), str_contains($service, 'aeo'), str_contains($service, 'lead generation'), str_contains($service, 'digital pr') => 'assets/img/stock/digital-growth.jpg',
            str_contains($service, 'mystery'), str_contains($service, 'shopping') => 'assets/img/stock/insight.jpg',
            str_contains($service, 'ai agent'), str_contains($service, 'ai chatbot'), str_contains($service, 'workflow automation') => 'assets/img/stock/insight.jpg',
            str_contains($service, 'laravel'), str_contains($service, 'php') => 'assets/img/stock/laravel-development.jpg',
            str_contains($service, 'web'), str_contains($service, 'saas') => 'assets/img/stock/web-platforms.jpg',
            default => 'assets/img/stock/web-platforms.jpg',
        };
    }

    public function deliverables(): array
    {
        $service = strtolower($this->slug.' '.$this->title);

        return match (true) {
            $this->category === 'AI Solutions' => [
                'Use case, knowledge sources, and system requirements review',
                'A scoped solution plan with human review and handoff points',
                'Testing and operational considerations for the agreed scope',
            ],
            str_contains($service, 'conversion rate optimization'), str_contains($service, 'landing page') => [
                'Review of the selected customer journey and available evidence',
                'Prioritized usability and measurement recommendations',
                'A scoped test or improvement plan for owner approval',
            ],
            str_contains($service, 'mobile') => [
                'App screens for the agreed iOS and Android scope',
                'API connections and key user flows',
                'Device testing and release handoff',
            ],
            str_contains($service, 'design'), str_contains($service, 'ux'), str_contains($service, 'ui') => [
                'User flows and low-fidelity wireframes',
                'A reusable visual design system',
                'Responsive screen designs and handoff assets',
            ],
            str_contains($service, 'youtube'), str_contains($service, 'automation') => [
                'YouTube channel strategy and content calendar',
                'Video scripting, titles, thumbnails, and publishing workflow',
                'Channel analytics and performance reporting',
            ],
            str_contains($service, 'local seo') => [
                'Local search visibility and business information review',
                'Location page and business profile recommendations',
                'Local search measurement and prioritized improvements',
            ],
            str_contains($service, 'e-commerce seo') => [
                'Store technical and indexability review',
                'Product category and product page recommendations',
                'Search analytics and performance measurement',
            ],
            str_contains($service, 'seo'), str_contains($service, 'growth') => [
                'Technical SEO review and prioritized fixes',
                'Search-friendly page structure and metadata',
                'Analytics and conversion tracking setup',
            ],
            str_contains($service, 'geo'), str_contains($service, 'aeo') => [
                'Website content and entity structure review',
                'Useful answer-focused content recommendations',
                'Technical discoverability and analytics review',
            ],
            str_contains($service, 'social media audit') => [
                'Review of the social channels and goals you select',
                'Observations on profile, content, and publishing practices',
                'A prioritized set of possible improvements',
            ],
            str_contains($service, 'social media') => [
                'Audience and channel review',
                'Social content themes and publishing plan',
                'Agreed channel and campaign performance review',
            ],
            str_contains($service, 'paid advertising'), str_contains($service, 'ppc') => [
                'Campaign objective and audience planning',
                'Platform-ready campaign and creative coordination',
                'Performance measurement against agreed targets',
            ],
            str_contains($service, 'influencer'), str_contains($service, 'creator marketing') => [
                'Creator and audience fit review',
                'Campaign brief and content coordination',
                'Agreed campaign performance reporting',
            ],
            str_contains($service, 'video production'), str_contains($service, 'video editing') => [
                'Agreed video format, brief, and production plan',
                'Editing and review workflow for the approved source materials',
                'Final assets prepared for the agreed channels',
            ],
            str_contains($service, 'marketplace listing') => [
                'Review of selected product listings and marketplace rules',
                'Recommendations for product content and catalogue structure',
                'An agreed review plan for listing quality and performance',
            ],
            str_contains($service, 'google maps advertising') => [
                'Review of location coverage and campaign eligibility',
                'Campaign objectives and approved location targeting',
                'A plan for measuring enquiries and advertising spend',
            ],
            str_contains($service, 'content') => [
                'Audience-focused content planning',
                'Agreed content briefs and publishing workflow',
                'Content performance review and recommendations',
            ],
            str_contains($service, 'email marketing') => [
                'Permission-based audience and email journey planning',
                'Campaign content and send workflow',
                'Engagement and deliverability measurement',
            ],
            str_contains($service, 'digital pr') => [
                'Relevant publisher and outreach opportunity review',
                'Editorial and campaign brief development',
                'Coverage and referral measurement where available',
            ],
            str_contains($service, 'lead generation') => [
                'Target audience and enquiry journey review',
                'Landing page and campaign recommendations',
                'Lead tracking and conversion measurement plan',
            ],
            str_contains($service, 'mystery shopping') => [
                'Agreed customer journey, channels, locations, and evaluation criteria',
                'A scoped observation or test plan for the selected touchpoints',
                'A findings summary for review with your team',
            ],
            str_contains($service, 'ai agent'), str_contains($service, 'ai chatbot'), str_contains($service, 'workflow automation') => [
                'Use case, knowledge sources, and system requirements review',
                'A scoped assistant or workflow plan with human handoff points',
                'Testing and operational considerations for the agreed scope',
            ],
            str_contains($service, 'laravel'), str_contains($service, 'php') => [
                'Laravel application and database schema',
                'Authentication, roles, and agreed integrations',
                'Tested code and deployment handoff',
            ],
            default => [
                'Responsive website or SaaS interface',
                'Backend, APIs, and agreed integrations',
                'Tested pages and deployment handoff',
            ],
        };
    }

    public function deliverySteps(): array
    {
        if ($this->category === 'Mystery Shopping') {
            return [
                ['title' => 'Agree the scope', 'description' => 'Define locations, channels, objectives, evaluation criteria, and any operating constraints.'],
                ['title' => 'Plan the assessment', 'description' => 'Confirm the proposed approach, schedule, reporting format, and approvals before any activity begins.'],
                ['title' => 'Review findings', 'description' => 'Organize observations into a clear summary for your team to review.'],
                ['title' => 'Plan next steps', 'description' => 'Discuss practical follow-up actions based on the agreed scope and findings.'],
            ];
        }

        if ($this->category === 'AI Solutions') {
            return [
                ['title' => 'Choose a use case', 'description' => 'Identify the task, users, information sources, and success measures to evaluate.'],
                ['title' => 'Review safeguards', 'description' => 'Agree integrations, data boundaries, human review, escalation, and fallback requirements.'],
                ['title' => 'Define a prototype', 'description' => 'Scope a suitable assistant or workflow and test it against agreed examples.'],
                ['title' => 'Plan deployment', 'description' => 'Confirm hosting, monitoring, ownership, and ongoing review before any production launch.'],
            ];
        }

        return [
            ['title' => 'Discovery', 'description' => 'Confirm requirements, users, success measures, and technical considerations.'],
            ['title' => 'Implementation', 'description' => 'Build the agreed experience, application, integrations, and supporting systems.'],
            ['title' => 'Quality assurance', 'description' => 'Validate key workflows, accessibility, and compatibility across agreed devices.'],
            ['title' => 'Launch and handover', 'description' => 'Prepare deployment, documentation, and a clear handover for your team.'],
        ];
    }
}
