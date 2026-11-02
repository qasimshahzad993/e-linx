<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Course;
use App\Models\Lead;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    private const CONTENT_TYPES = [
        'services' => Service::class,
        'products' => Product::class,
        'portfolio' => Portfolio::class,
    ];

    public function beginLogin(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->noStore(redirect()->route('admin.login'));
    }

    public function login(): Response
    {
        if (session()->has('admin_id') && Admin::whereKey(session('admin_id'))->exists()) {
            return $this->noStore(redirect()->route('admin.dashboard'));
        }

        return $this->noStore(response()->view('admin.login'));
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:190'],
            'password' => ['required', 'string', 'max:255'],
        ]);
        $admin = Admin::where('email', $credentials['login'])
            ->orWhere('name', $credentials['login'])
            ->first();

        if (! $admin || ! $this->passwordMatches($admin, $credentials['password'])) {
            return back()->withErrors(['login' => 'Email or password is incorrect.'])
                ->onlyInput('login');
        }

        if (password_get_info($admin->password)['algo'] === null) {
            $admin->password = Hash::make($credentials['password']);
            $admin->save();
        }

        $request->session()->regenerate();
        $request->session()->put([
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'admin_email' => $admin->email,
        ]);

        return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back, '.$admin->name.'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->noStore(redirect()->route('admin.login')->with('status', 'You have been securely signed out.'));
    }

    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'newLeads' => Lead::where('status', 'new')->count(),
            'leadCount' => Lead::count(),
            'weeklyLeads' => Lead::where('created_at', '>=', now()->startOfWeek())->count(),
            'contactedLeads' => Lead::where('status', 'contacted')->count(),
            'serviceCount' => Service::count(),
            'productCount' => Product::count(),
            'projectCount' => Portfolio::count(),
            'courseCount' => Course::count(),
            'recentLeads' => Lead::latest()->take(6)->get(),
        ]);
    }

    public function leads(Request $request): View
    {
        $status = $request->query('status');
        abort_if($status && ! in_array($status, ['new', 'contacted', 'closed'], true), 404);
        $statusCounts = Lead::selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('admin.leads', [
            'leads' => Lead::when($status, fn ($query) => $query->where('status', $status))
                ->latest()->paginate(20)->withQueryString(),
            'status' => $status,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function updateLead(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(['new', 'contacted', 'closed'])]]);
        $lead->update($data);

        return back()->with('success', 'Lead marked as '.$data['status'].'.');
    }

    public function deleteLead(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return back()->with('success', 'Lead deleted.');
    }

    public function notifications(Request $request): JsonResponse
    {
        $data = $request->validate(['after' => ['required', 'integer', 'min:0']]);
        $leads = Lead::where('id', '>', $data['after'])
            ->orderBy('id')
            ->limit(20)
            ->get(['id', 'name', 'subject', 'type', 'created_at']);

        return response()->json([
            'leads' => $leads,
            'new_count' => Lead::where('status', 'new')->count(),
        ]);
    }

    public function content(Request $request, string $type): View
    {
        $model = self::CONTENT_TYPES[$type];
        $query = $model::query();
        if ($type === 'services') {
            $query->orderBy('sort_order');
        } else {
            $query->latest();
        }

        return view('admin.content', [
            'type' => $type,
            'items' => $query->get(),
            'edit' => $request->integer('edit') ? $model::find($request->integer('edit')) : null,
        ]);
    }

    public function saveContent(Request $request, string $type): RedirectResponse
    {
        $model = self::CONTENT_TYPES[$type];
        $id = $request->integer('id');
        $item = $id ? $model::findOrFail($id) : new $model;
        $rules = match ($type) {
            'services' => [
                'title' => ['required', 'string', 'max:160'],
                'category' => ['nullable', Rule::in(array_keys(Service::CATEGORIES))],
                'short_description' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:10000'],
                'icon' => ['nullable', 'string', 'max:100'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ],
            'products' => [
                'name' => ['required', 'string', 'max:160'],
                'category' => ['nullable', 'string', 'max:100'],
                'description' => ['nullable', 'string', 'max:10000'],
                'icon' => ['nullable', 'string', 'max:100'],
            ],
            default => [
                'title' => ['required', 'string', 'max:180'],
                'category' => ['required', 'string', 'max:100'],
                'description' => ['nullable', 'string', 'max:10000'],
                'challenge' => ['nullable', 'string', 'max:10000'],
                'solution' => ['nullable', 'string', 'max:10000'],
                'outcome' => ['nullable', 'string', 'max:10000'],
                'url' => ['nullable', 'url', 'max:500'],
                'icon' => ['nullable', 'string', 'max:100'],
            ],
        };

        $data = $request->validate($rules);
        $data['icon'] = filled($data['icon'] ?? null) ? $data['icon'] : 'bi bi-'.($type === 'products' ? 'box-seam' : 'window');
        $data['status'] = $request->boolean('status');

        if ($type === 'services') {
            $data['slug'] = $this->uniqueSlug($data['title'], $item);
            $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        }

        $item->fill($data)->save();

        return redirect()->route('admin.content.index', $type)->with('success', ucfirst(rtrim($type, 's')).' saved.');
    }

    public function deleteContent(string $type, int $id): RedirectResponse
    {
        $model = self::CONTENT_TYPES[$type];
        $model::findOrFail($id)->delete();

        return redirect()->route('admin.content.index', $type)->with('success', 'Content item deleted.');
    }

    public function profile(): View
    {
        return view('admin.profile', ['admin' => Admin::findOrFail(session('admin_id'))]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $admin = Admin::findOrFail(session('admin_id'));
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('admins')->ignore($admin->id)],
        ]);
        $admin->update($data);
        session()->put(['admin_name' => $admin->name, 'admin_email' => $admin->email]);

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current' => ['required', 'string', 'max:255'],
            'new' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);
        $admin = Admin::findOrFail(session('admin_id'));

        if (! $this->passwordMatches($admin, $data['current'])) {
            return back()->withErrors(['current' => 'Current password is incorrect.'])->withFragment('password');
        }

        $admin->update(['password' => Hash::make($data['new'])]);
        $request->session()->regenerate();

        return back()->with('success', 'Password changed.');
    }

    private function noStore(Response $response): Response
    {
        return $response
            ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function passwordMatches(Admin $admin, string $password): bool
    {
        return password_get_info($admin->password)['algo'] !== null
            ? Hash::check($password, $admin->password)
            : hash_equals($admin->password, md5($password));
    }

    private function uniqueSlug(string $title, Model $item): string
    {
        $slug = Str::slug($title) ?: 'service';
        $base = $slug;
        $suffix = 2;

        while (Service::where('slug', $slug)
            ->when($item->exists, fn ($query) => $query->whereKeyNot($item->getKey()))
            ->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
