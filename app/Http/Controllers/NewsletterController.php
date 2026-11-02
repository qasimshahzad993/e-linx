<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        $data = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'consent' => ['accepted'],
            'website' => ['prohibited'],
        ]);

        $subscriber = NewsletterSubscriber::firstOrNew(['email' => $data['email']]);
        $subscriber->unsubscribe_token ??= Str::random(64);
        $subscriber->subscribed_at = now();
        $subscriber->unsubscribed_at = null;
        $subscriber->save();

        return back()->with('success', 'Thanks for subscribing to e-Linx updates. You can unsubscribe using your personal link in any update.');
    }

    public function confirmUnsubscribe(string $token): View
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->firstOrFail();

        return view('newsletter.unsubscribe', compact('subscriber'));
    }

    public function unsubscribe(Request $request, string $token): RedirectResponse
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->firstOrFail();
        $subscriber->update(['unsubscribed_at' => now()]);

        return redirect()->route('newsletter.unsubscribe', $token)
            ->with('success', 'Your e-Linx newsletter subscription has been cancelled.');
    }
}
