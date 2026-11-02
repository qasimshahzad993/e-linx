<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTrustContentController extends Controller
{
    private const CONTENT_TYPES = [
        'faqs' => Faq::class,
        'testimonials' => Testimonial::class,
    ];

    public function index(Request $request, string $type): View
    {
        $model = self::CONTENT_TYPES[$type];

        return view('admin.trust-content', [
            'type' => $type,
            'items' => $model::orderBy('sort_order')->orderByDesc('id')->get(),
            'edit' => $request->integer('edit') ? $model::find($request->integer('edit')) : null,
        ]);
    }

    public function save(Request $request, string $type): RedirectResponse
    {
        $model = self::CONTENT_TYPES[$type];
        $id = $request->integer('id');
        $item = $id ? $model::findOrFail($id) : new $model;

        $rules = $type === 'faqs'
            ? [
                'question' => ['required', 'string', 'max:255'],
                'answer' => ['required', 'string', 'max:10000'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ]
            : [
                'client_name' => ['required', 'string', 'max:120'],
                'client_role' => ['nullable', 'string', 'max:160'],
                'company_name' => ['nullable', 'string', 'max:180'],
                'service' => ['nullable', 'string', 'max:160'],
                'quote' => ['required', 'string', 'max:3000'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ];

        $data = $request->validate($rules);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['status'] = $request->boolean('status');
        $item->fill($data)->save();

        return redirect()->route('admin.trust.index', $type)
            ->with('success', ($type === 'faqs' ? 'FAQ' : 'Testimonial').' saved.');
    }

    public function delete(string $type, int $id): RedirectResponse
    {
        $model = self::CONTENT_TYPES[$type];
        $model::findOrFail($id)->delete();

        return redirect()->route('admin.trust.index', $type)
            ->with('success', ($type === 'faqs' ? 'FAQ' : 'Testimonial').' deleted.');
    }
}
