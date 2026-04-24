<?php

namespace Tests\Feature;

use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_can_subscribe_to_newsletter()
    {
        $response = $this->postJson('/api/newsletter/subscribe', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'تم الاشتراك بنجاح في القائمة البريدية!']);

        $this->assertDatabaseHas('newsletters', [
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function newsletter_subscription_requires_valid_email()
    {
        $response = $this->postJson('/api/newsletter/subscribe', [
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function cannot_subscribe_with_existing_email()
    {
        Newsletter::create(['email' => 'duplicate@example.com']);

        $response = $this->postJson('/api/newsletter/subscribe', [
            'email' => 'duplicate@example.com'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function admin_can_view_subscribers()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Newsletter::create(['email' => 'sub1@example.com']);

        $response = $this->actingAs($admin)
                         ->getJson('/api/admin/subscribers');

        $response->assertStatus(200)
                 ->assertJsonFragment(['email' => 'sub1@example.com']);
    }

    /** @test */
    public function admin_can_delete_subscriber()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subscriber = Newsletter::create(['email' => 'delete-me@example.com']);

        $response = $this->actingAs($admin)
                         ->deleteJson("/api/admin/subscribers/{$subscriber->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('newsletters', ['email' => 'delete-me@example.com']);
    }
}
