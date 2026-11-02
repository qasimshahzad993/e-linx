<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::where('status', true)->orderBy('sort_order')->get();

        return view('faqs', [
            'faqs' => $faqs,
            'faqSchema' => $faqs->isEmpty() ? [] : [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $faqs->map(fn (Faq $faq): array => [
                    '@type' => 'Question',
                    'name' => $faq->question,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq->answer,
                    ],
                ])->values()->all(),
            ],
        ]);
    }
}
