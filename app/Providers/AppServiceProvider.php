<?php

namespace App\Providers;

use App\Models\Service;
use App\Models\Course;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.site', function ($view): void {
            $services = Service::where('status', true)
                ->whereIn('category', array_keys(Service::CATEGORIES))
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get()
                ->groupBy('category');

            $view->with('serviceGroups', $services);
            $view->with('navCourses', Course::where('status', true)->orderBy('sort_order')->orderBy('title')->limit(6)->get());
        });
    }
}
