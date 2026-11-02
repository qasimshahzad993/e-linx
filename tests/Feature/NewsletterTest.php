<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_newsletter_form_requires_valid_email_and_explicit_consent(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Occasional e-Linx updates')
            ->assertSee('action="'.route('newsletter.store').'"', false);

        $this->from('/')->post('/newsletter', [
            'email' => 'reader@example.com',
        ])->assertRedirect('/')
            ->assertSessionHasErrors('consent');

        $this->assertDatabaseCount('newsletter_subscribers', 0);

        $this->from('/')->post('/newsletter', [
            'email' => 'not-an-email',
            'consent' => '1',
        ])->assertRedirect('/')
            ->assertSessionHasErrors('email');
    }

    public function test_newsletter_subscriptions_are_normalized_and_duplicate_submits_do_not_duplicate_records(): void
    {
        foreach ([' Reader@Example.com ', 'reader@example.com'] as $email) {
            $this->from('/')->post('/newsletter', [
                'email' => $email,
                'consent' => '1',
                'website' => '',
            ])->assertRedirect('/')
                ->assertSessionHas('success');
        }

        $this->assertDatabaseCount('newsletter_subscribers', 1);
        $subscriber = NewsletterSubscriber::firstOrFail();
        $this->assertSame('reader@example.com', $subscriber->email);
        $this->assertSame(64, strlen($subscriber->unsubscribe_token));
        $this->assertNotNull($subscriber->subscribed_at);
        $this->assertNull($subscriber->unsubscribed_at);
    }

    public function test_subscribers_can_confirm_unsubscribe_without_mutating_on_get(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'reader@example.com',
            'unsubscribe_token' => str_repeat('a', 64),
            'subscribed_at' => now(),
        ]);

        $this->get('/newsletter/unsubscribe/'.$subscriber->unsubscribe_token)
            ->assertOk()
            ->assertSee('Confirm unsubscribe');
        $this->assertNull($subscriber->fresh()->unsubscribed_at);

        $this->post('/newsletter/unsubscribe/'.$subscriber->unsubscribe_token)
            ->assertRedirect('/newsletter/unsubscribe/'.$subscriber->unsubscribe_token);
        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);

        $this->flushSession();
        $this->get('/newsletter/unsubscribe/'.$subscriber->unsubscribe_token)
            ->assertOk()
            ->assertSee('already unsubscribed');
    }

    public function test_newsletter_list_requires_admin_session_and_admin_can_remove_subscribers(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'reader@example.com',
            'unsubscribe_token' => str_repeat('b', 64),
            'subscribed_at' => now(),
        ]);

        $this->get('/admin/newsletter')->assertRedirect('/admin/login');

        $admin = Admin::create([
            'name' => 'Newsletter Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('a-secure-password'),
        ]);

        $this->withSession(['admin_id' => $admin->id])
            ->get('/admin/newsletter')
            ->assertOk()
            ->assertSee('Newsletter subscribers')
            ->assertSee('reader@example.com')
            ->assertSee(route('newsletter.unsubscribe', $subscriber->unsubscribe_token));

        $this->withSession(['admin_id' => $admin->id])
            ->delete('/admin/newsletter/'.$subscriber->id)
            ->assertRedirect('/admin/newsletter');

        $this->assertDatabaseMissing('newsletter_subscribers', ['id' => $subscriber->id]);
    }
}
