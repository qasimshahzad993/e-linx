<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_widget_shows_setup_notice_when_ai_provider_is_not_configured(): void
    {
        config(['services.chatbot.api_key' => null]);

        $this->get('/')
            ->assertOk()
            ->assertSee('e-Linx Assistant')
            ->assertSee('data-chatbot-configured="false"', false)
            ->assertSee('Messages are processed by our AI provider');

        $this->postJson('/chatbot/reply', [
            'messages' => [['role' => 'user', 'content' => 'What services do you offer?']],
        ])->assertStatus(503)
            ->assertJsonPath('message', 'The AI assistant is not connected yet. Please contact our team by email or WhatsApp.');

        Http::assertNothingSent();
    }

    public function test_chatbot_sends_published_elinx_information_to_the_configured_provider(): void
    {
        config([
            'services.chatbot.api_key' => 'test-chatbot-key',
            'services.chatbot.api_url' => 'https://api.openai.com/v1/chat/completions',
            'services.chatbot.model' => 'gpt-4o-mini',
        ]);
        Service::create([
            'title' => 'Website Design',
            'slug' => 'website-design',
            'short_description' => 'Responsive business websites.',
            'status' => true,
        ]);
        Service::create([
            'title' => 'Private Draft Service',
            'slug' => 'private-draft-service',
            'short_description' => 'Not published.',
            'status' => false,
        ]);
        Faq::create([
            'question' => 'Where is e-Linx based?',
            'answer' => 'e-Linx is based in Pakistan.',
            'sort_order' => 1,
            'status' => true,
        ]);
        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'We build responsive business websites.']],
                ],
            ]),
        ]);

        $this->postJson('/chatbot/reply', [
            'messages' => [
                ['role' => 'user', 'content' => 'What can you build?'],
            ],
        ])->assertOk()
            ->assertJsonPath('reply', 'We build responsive business websites.');

        Http::assertSent(function (Request $request): bool {
            $systemPrompt = $request->data()['messages'][0]['content'];

            return $request->url() === 'https://api.openai.com/v1/chat/completions'
                && $request->hasHeader('Authorization', 'Bearer test-chatbot-key')
                && $request->data()['model'] === 'gpt-4o-mini'
                && str_contains($systemPrompt, 'Website Design: Responsive business websites.')
                && str_contains($systemPrompt, 'Where is e-Linx based? e-Linx is based in Pakistan.')
                && ! str_contains($systemPrompt, 'Private Draft Service');
        });
    }

    public function test_chatbot_rejects_invalid_history_and_surfaces_provider_errors(): void
    {
        config([
            'services.chatbot.api_key' => 'test-chatbot-key',
            'services.chatbot.api_url' => 'https://api.openai.com/v1/chat/completions',
        ]);

        $this->postJson('/chatbot/reply', [
            'messages' => [['role' => 'assistant', 'content' => 'This is not a visitor question.']],
        ])->assertStatus(422)
            ->assertJsonPath('message', 'Please send a question to continue the chat.');

        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response(['error' => 'provider unavailable'], 500),
        ]);

        $this->postJson('/chatbot/reply', [
            'messages' => [['role' => 'user', 'content' => 'Can you help?']],
        ])->assertStatus(502)
            ->assertJsonPath('message', 'The AI assistant could not prepare a reply. Please try again or contact our team.');
    }
}
