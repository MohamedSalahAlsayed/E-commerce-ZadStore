<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\StoreSetting;

class SettingsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_settings_via_api(): void
    {
        // Seed a setting
        StoreSetting::create([
            'key' => 'storeName',
            'value' => 'ZadStore Test',
            'group' => 'general'
        ]);

        $response = $this->getJson('/api/settings');

        $response->assertStatus(200)
                 ->assertJsonFragment(['storeName' => 'ZadStore Test']);
    }

    public function test_api_returns_expected_structure(): void
    {
        // Seed required keys
        StoreSetting::create(['key' => 'storeName', 'value' => 'Zad', 'group' => 'g']);
        StoreSetting::create(['key' => 'currency', 'value' => 'EGP', 'group' => 'g']);
        StoreSetting::create(['key' => 'primaryColor', 'value' => '#000', 'group' => 'g']);

        $response = $this->getJson('/api/settings');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'storeName',
                     'currency',
                     'primaryColor'
                 ]);
    }
}
