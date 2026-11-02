<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class MarketingServicesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Social Media Marketing',
                'category' => 'Digital Marketing',
                'short_description' => 'Social strategy, content planning, and channel management shaped around your business and audience.',
                'description' => 'Plan and improve your social media presence with audience-focused strategy, publishing workflows, and practical performance review.',
                'icon' => 'bi bi-megaphone',
            ],
            [
                'title' => 'Paid Advertising & PPC',
                'category' => 'Digital Marketing',
                'short_description' => 'Campaign planning, ad creative coordination, and measurement across agreed advertising platforms.',
                'description' => 'Set clear campaign objectives, prepare platform-ready campaigns, and review performance against agreed measures. Media spend and platform access are agreed separately.',
                'icon' => 'bi bi-badge-ad',
            ],
            [
                'title' => 'Branding & Creative Design',
                'category' => 'Digital Marketing',
                'short_description' => 'Brand identity, campaign graphics, and reusable creative assets for digital channels.',
                'description' => 'Create a consistent digital brand presence through visual direction, campaign assets, and practical design systems.',
                'icon' => 'bi bi-palette',
            ],
            [
                'title' => 'Content Creation & Marketing',
                'category' => 'Digital Marketing',
                'short_description' => 'Content planning and creation to support your website, campaigns, and social channels.',
                'description' => 'Plan useful content for your target audiences, coordinate agreed creative formats, and align publishing with your wider digital goals.',
                'icon' => 'bi bi-file-earmark-richtext',
            ],
            [
                'title' => 'Local SEO',
                'category' => 'Digital Marketing',
                'short_description' => 'Improve local search visibility through technical checks, location pages, and business profile guidance.',
                'description' => 'Review local search foundations and prioritize improvements to business information, location content, and measurement.',
                'icon' => 'bi bi-geo-alt',
            ],
            [
                'title' => 'E-commerce SEO',
                'category' => 'Digital Marketing',
                'short_description' => 'Search optimization for online stores, product categories, and product pages.',
                'description' => 'Improve discoverability of your online store through technical review, category and product content recommendations, and analytics.',
                'icon' => 'bi bi-bag-check',
            ],
            [
                'title' => 'GEO & AEO',
                'category' => 'Digital Marketing',
                'short_description' => 'Structure useful website content for clear answers across evolving search experiences.',
                'description' => 'Review content structure, entity information, and technical foundations to support discoverability across search and answer experiences. Results depend on third-party platforms and are not guaranteed.',
                'icon' => 'bi bi-search',
            ],
            [
                'title' => 'Email Marketing',
                'category' => 'Digital Marketing',
                'short_description' => 'Email content and campaign workflows for permission-based customer communications.',
                'description' => 'Plan audience segments, useful email content, and campaign measurement while respecting consent and applicable email rules.',
                'icon' => 'bi bi-envelope-paper',
            ],
            [
                'title' => 'Digital PR & Outreach',
                'category' => 'Digital Marketing',
                'short_description' => 'Plan relevant digital outreach and publication opportunities for your brand.',
                'description' => 'Identify relevant outreach opportunities and prepare a clear content-led approach. Placements and coverage depend on independent publishers.',
                'icon' => 'bi bi-broadcast',
            ],
            [
                'title' => 'Lead Generation Strategy',
                'category' => 'Digital Marketing',
                'short_description' => 'Improve campaign journeys, enquiry paths, and measurement around your business goals.',
                'description' => 'Review target audiences, landing-page journeys, and campaign measurement to develop a practical approach to generating qualified enquiries.',
                'icon' => 'bi bi-funnel',
            ],
            [
                'title' => 'Influencer & Creator Marketing',
                'category' => 'Digital Marketing',
                'short_description' => 'Plan creator partnerships and campaign briefs aligned with your audience and brand.',
                'description' => 'Coordinate creator discovery, campaign briefs, content review, and agreed performance tracking. Creator participation and campaign outcomes depend on the creators and platforms involved.',
                'icon' => 'bi bi-person-video3',
            ],
            [
                'title' => 'Landing Page & Conversion Design',
                'category' => 'Digital Marketing',
                'short_description' => 'Plan clear landing-page experiences around a campaign, product, or business objective.',
                'description' => 'Review the user journey and design focused landing-page content and interface recommendations. Outcomes depend on audience, offer, traffic, and testing.',
                'icon' => 'bi bi-window-fullscreen',
            ],
            [
                'title' => 'Social Media Audit',
                'category' => 'Digital Marketing',
                'short_description' => 'Request a structured review of your social profiles, publishing approach, and audience touchpoints.',
                'description' => 'Review the channels and goals you choose, then agree a practical list of observations and improvement priorities.',
                'icon' => 'bi bi-clipboard2-data',
            ],
            [
                'title' => 'Video Production & Editing',
                'category' => 'Digital Marketing',
                'short_description' => 'Scope video planning, production, editing, and delivery for your agreed digital channels.',
                'description' => 'Share the audience, format, channels, and source materials you have. Production needs, permissions, review rounds, and deliverables are agreed before work begins.',
                'icon' => 'bi bi-camera-reels',
            ],
            [
                'title' => 'Marketplace Listing Optimization',
                'category' => 'Digital Marketing',
                'short_description' => 'Improve the clarity and discoverability of agreed product listings across selected marketplaces.',
                'description' => 'Discuss your marketplace, product catalogue, content requirements, and measurement needs. Platform rules and listing performance remain subject to the marketplace.',
                'icon' => 'bi bi-shop',
            ],
            [
                'title' => 'Conversion Rate Optimization',
                'category' => 'Digital Marketing',
                'short_description' => 'Review key customer journeys and identify evidence-led opportunities to improve conversion.',
                'description' => 'Prioritize a measurable user journey, review available analytics and accessibility considerations, and agree any testing approach before changes are made.',
                'icon' => 'bi bi-graph-up-arrow',
            ],
            [
                'title' => 'Google Maps Advertising',
                'category' => 'Digital Marketing',
                'short_description' => 'Discuss location-focused advertising goals and a measurement plan for eligible local campaigns.',
                'description' => 'Review location coverage, business profile readiness, platform eligibility, spend ownership, and lead measurement. Ad approval and delivery depend on Google policies.',
                'icon' => 'bi bi-geo-alt',
            ],
        ];

        foreach ($services as $index => $attributes) {
            $service = Service::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($attributes['title'])],
                $attributes + [
                    'slug' => \Illuminate\Support\Str::slug($attributes['title']),
                    'sort_order' => 20 + $index,
                    'status' => true,
                ],
            );

            if (! $service->category) {
                $service->update(['category' => $attributes['category']]);
            }
        }
    }
}
