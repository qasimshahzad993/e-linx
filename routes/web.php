<?php

use App\Http\Controllers\AdminBlogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminCourseController;
use App\Http\Controllers\AdminNewsletterController;
use App\Http\Controllers\AdminTrustContentController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::post('/newsletter', [NewsletterController::class, 'store'])->middleware('throttle:5,1')->name('newsletter.store');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'confirmUnsubscribe'])->whereAlphaNumeric('token')->name('newsletter.unsubscribe');
Route::post('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->whereAlphaNumeric('token')->name('newsletter.unsubscribe.submit');
Route::post('/chatbot/reply', [ChatbotController::class, 'reply'])->middleware('throttle:10,1')->name('chatbot.reply');

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/faqs', [FaqController::class, 'index'])->name('faqs');
Route::get('/services', [SiteController::class, 'services'])->name('services');
Route::get('/services/{service:slug}', [SiteController::class, 'service'])->name('service.show');
Route::get('/courses', [SiteController::class, 'courses'])->name('courses.index');
Route::get('/courses/{course:slug}', [SiteController::class, 'course'])->name('courses.show');
Route::post('/courses/{course:slug}/enquiry', [SiteController::class, 'courseEnquiryStore'])->middleware('throttle:6,1')->name('courses.enquiry');
Route::get('/products', [SiteController::class, 'products'])->name('products');
Route::get('/portfolio', [SiteController::class, 'portfolio'])->name('portfolio');
Route::get('/portfolio/{project}', [PortfolioController::class, 'show'])->whereNumber('project')->name('portfolio.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::post('/contact', [SiteController::class, 'contactStore'])->middleware('throttle:6,1')->name('contact.store');
Route::get('/quote', [SiteController::class, 'quote'])->name('quote');
Route::post('/quote', [SiteController::class, 'quoteStore'])->middleware('throttle:6,1')->name('quote.store');

Route::post('/admin/access', [AdminController::class, 'beginLogin'])->name('admin.access');
Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'authenticate'])->middleware('throttle:5,1')->name('admin.authenticate');

Route::prefix('admin')->name('admin.')->middleware('admin')->group(function (): void {
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/leads', [AdminController::class, 'leads'])->name('leads.index');
    Route::patch('/leads/{lead}', [AdminController::class, 'updateLead'])->name('leads.update');
    Route::delete('/leads/{lead}', [AdminController::class, 'deleteLead'])->name('leads.delete');
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
    Route::get('/newsletter', [AdminNewsletterController::class, 'index'])->name('newsletter.index');
    Route::delete('/newsletter/{subscriber}', [AdminNewsletterController::class, 'destroy'])->name('newsletter.delete');
    Route::get('/content/{type}', [AdminController::class, 'content'])->whereIn('type', ['services', 'products', 'portfolio'])->name('content.index');
    Route::post('/content/{type}', [AdminController::class, 'saveContent'])->whereIn('type', ['services', 'products', 'portfolio'])->name('content.store');
    Route::delete('/content/{type}/{id}', [AdminController::class, 'deleteContent'])->whereIn('type', ['services', 'products', 'portfolio'])->name('content.delete');
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
    Route::post('/courses', [AdminCourseController::class, 'save'])->name('courses.store');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'delete'])->name('courses.delete');
    Route::get('/blog', [AdminBlogController::class, 'index'])->name('blog.index');
    Route::post('/blog', [AdminBlogController::class, 'store'])->name('blog.store');
    Route::put('/blog/{post}', [AdminBlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [AdminBlogController::class, 'destroy'])->name('blog.destroy');
    Route::get('/trust/{type}', [AdminTrustContentController::class, 'index'])->whereIn('type', ['faqs', 'testimonials'])->name('trust.index');
    Route::post('/trust/{type}', [AdminTrustContentController::class, 'save'])->whereIn('type', ['faqs', 'testimonials'])->name('trust.store');
    Route::delete('/trust/{type}/{id}', [AdminTrustContentController::class, 'delete'])->whereIn('type', ['faqs', 'testimonials'])->name('trust.delete');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [AdminController::class, 'updatePassword'])->name('password.update');
});
