<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'What can e-Linx help my business build?',
                'answer' => 'e-Linx works on websites and web platforms, custom software, mobile applications, product design, YouTube automation, and digital growth projects. The right scope depends on your requirements.',
            ],
            [
                'question' => 'Can you improve an existing website or product?',
                'answer' => 'Yes. You can use the project proposal form to describe an existing product, system, or website and explain the changes or improvements you need.',
            ],
            [
                'question' => 'How do I request a project proposal?',
                'answer' => 'Submit the project brief form with your contact details, service of interest, project overview, preferred timeline, and budget range. You can submit an enquiry even if some details are still undecided.',
            ],
            [
                'question' => 'How are project cost and timeline determined?',
                'answer' => 'Cost and schedule depend on the agreed scope and project requirements. Share your expectations in the project brief so the team can review them and discuss appropriate next steps.',
            ],
            [
                'question' => 'What if I am not sure which service I need?',
                'answer' => 'Choose “Not sure yet — please advise” in the proposal form and describe the outcome you are looking for. The team can review your enquiry and help identify a suitable direction.',
            ],
            [
                'question' => 'Where is e-Linx based?',
                'answer' => 'e-Linx is based in Pakistan and serves clients worldwide.',
            ],
        ];

        foreach ($faqs as $index => $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                $faq + ['sort_order' => $index + 1, 'status' => true],
            );
        }
    }
}
