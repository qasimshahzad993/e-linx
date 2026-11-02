<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSolutionsSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'category' => 'Website Solutions',
                'title' => 'Website Design & Development',
                'short_description' => 'Plan, build, and improve responsive websites around your content, audience, and business requirements.',
                'description' => 'Discuss a new website or a substantial redesign. The proposal can cover information architecture, responsive interface design, development, agreed integrations, testing, and handover.',
                'icon' => 'bi bi-window',
            ],
            [
                'category' => 'Website Solutions',
                'title' => 'E-commerce Store Design',
                'short_description' => 'Plan an online store with product discovery, checkout, and the integrations your team needs.',
                'description' => 'Share your catalogue, sales channels, payment and delivery requirements, and existing systems so the right store scope can be discussed.',
                'icon' => 'bi bi-bag-check',
            ],
            [
                'category' => 'Website Solutions',
                'title' => 'Website Audit',
                'short_description' => 'Request a structured review of a website’s usability, technical foundations, and key customer journeys.',
                'description' => 'The audit scope is agreed before work begins and may cover usability, accessibility, performance, search foundations, and priority recommendations.',
                'icon' => 'bi bi-clipboard2-data',
            ],
            [
                'category' => 'Mystery Shopping',
                'title' => 'Mystery Shopping',
                'short_description' => 'Discuss a customer-experience assessment across the locations or channels that matter to your organization.',
                'description' => 'Share your goals, locations, customer touchpoints, and preferred reporting needs. Any assessment plan, access, approvals, and delivery schedule must be agreed before work begins.',
                'icon' => 'bi bi-person-walking',
            ],
            [
                'category' => 'Mystery Shopping',
                'title' => 'Video Mystery Shopping',
                'short_description' => 'Discuss whether an approved, video-based observation approach fits your customer-experience goals.',
                'description' => 'Scope, permissions, participant safety, privacy, and applicable local requirements must be reviewed and approved before any video activity.',
                'icon' => 'bi bi-camera-video',
            ],
            [
                'category' => 'Mystery Shopping',
                'title' => 'Telephone Mystery Shopping',
                'short_description' => 'Review the goals and criteria for assessing customer conversations through agreed telephone touchpoints.',
                'description' => 'Discuss the call journey, evaluation criteria, operating permissions, and reporting requirements. Recording or monitoring is never assumed and requires prior approval.',
                'icon' => 'bi bi-telephone',
            ],
            [
                'category' => 'Mystery Shopping',
                'title' => 'Digital Mystery Shopping',
                'short_description' => 'Review website, app, or online enquiry journeys from a customer-experience perspective.',
                'description' => 'Identify the digital journeys and criteria to review. The scope will specify access, test conditions, privacy requirements, and the findings format.',
                'icon' => 'bi bi-phone',
            ],
            [
                'category' => 'Mystery Shopping',
                'title' => 'Competitive Mystery Shopping',
                'short_description' => 'Discuss a compliant, publicly available customer-journey comparison for your market.',
                'description' => 'Any comparison is limited to lawful, ethical observations of customer-facing experiences. It does not involve impersonation to obtain confidential information or unauthorized access.',
                'icon' => 'bi bi-bar-chart',
            ],
            [
                'category' => 'AI Solutions',
                'title' => 'AI Agent Development',
                'short_description' => 'Scope an AI assistant for a defined business workflow, with human review and clear fallback paths.',
                'description' => 'Start with the task, knowledge sources, systems, data boundaries, and escalation needs. Feasibility, provider, hosting, and safeguards are reviewed before implementation is proposed.',
                'icon' => 'bi bi-robot',
            ],
            [
                'category' => 'AI Solutions',
                'title' => 'AI Chatbot Solutions',
                'short_description' => 'Discuss a customer-facing or internal assistant grounded in your approved information.',
                'description' => 'Define supported questions, approved content, integrations, privacy requirements, human handoff, and out-of-scope responses before selecting an implementation approach.',
                'icon' => 'bi bi-chat-square-dots',
            ],
            [
                'category' => 'AI Solutions',
                'title' => 'AI Workflow Automation',
                'short_description' => 'Explore whether AI can assist a repeatable workflow while keeping people in control of important decisions.',
                'description' => 'Map the current process, data sources, exception handling, and approval points. Automation scope and human oversight are agreed before development.',
                'icon' => 'bi bi-diagram-3',
            ],
            [
                'category' => 'AI Solutions',
                'title' => 'AI Marketing Solutions',
                'short_description' => 'Explore carefully scoped AI assistance for marketing research, planning, or content workflows.',
                'description' => 'Define the tasks, approved sources, human review, and platform rules first. Publishing, advertising claims, audience data, and customer communications remain subject to owner approval and applicable platform policies.',
                'icon' => 'bi bi-megaphone',
            ],
            [
                'category' => 'AI Solutions',
                'title' => 'CRM Solutions',
                'short_description' => 'Discuss a customer-management system, sales workflow, or CRM integration tailored to your team.',
                'description' => 'Share your customer journey, user roles, existing data sources, and reporting needs so a suitable CRM scope and migration approach can be assessed.',
                'icon' => 'bi bi-people',
            ],
        ];

        foreach ($services as $index => $attributes) {
            $service = Service::firstOrCreate(
                ['slug' => Str::slug($attributes['title'])],
                $attributes + [
                    'slug' => Str::slug($attributes['title']),
                    'sort_order' => 40 + $index,
                    'status' => true,
                ],
            );

            if (! $service->category) {
                $service->update(['category' => $attributes['category']]);
            }
        }
    }
}
