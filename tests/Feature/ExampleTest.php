<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_public(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/ar')->assertStatus(200);
    }

    public function test_dashboard_requires_auth(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_public_calculator_page_loads(): void
    {
        $this->get(route('calculator.index'))->assertStatus(200);
    }

    public function test_public_comparison_page_loads(): void
    {
        $this->get(route('comparison.index'))->assertStatus(200);
    }
}
