<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_guests_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
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
