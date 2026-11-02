<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = $request->session()->has('admin_id')
            ? Admin::find($request->session()->get('admin_id'))
            : null;

        if (! $admin) {
            $request->session()->forget(['admin_id', 'admin_name', 'admin_email']);

            return $this->preventCaching(redirect()->route('admin.login'));
        }

        $request->session()->put([
            'admin_name' => $admin->name,
            'admin_email' => $admin->email,
        ]);

        return $this->preventCaching($next($request));
    }

    private function preventCaching(Response $response): Response
    {
        return $response
            ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
