<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminNewsletterController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'active');
        abort_unless(in_array($status, ['active', 'unsubscribed', 'all'], true), 404);

        return view('admin.newsletter', [
            'subscribers' => NewsletterSubscriber::query()
                ->when($status === 'active', fn ($query) => $query->whereNull('unsubscribed_at'))
                ->when($status === 'unsubscribed', fn ($query) => $query->whereNotNull('unsubscribed_at'))
                ->latest('subscribed_at')
                ->paginate(20)
                ->withQueryString(),
            'status' => $status,
            'activeCount' => NewsletterSubscriber::whereNull('unsubscribed_at')->count(),
            'unsubscribedCount' => NewsletterSubscriber::whereNotNull('unsubscribed_at')->count(),
            'allCount' => NewsletterSubscriber::count(),
        ]);
    }

    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return back()->with('success', 'Newsletter subscriber removed.');
    }
}
