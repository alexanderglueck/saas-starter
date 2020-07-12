<?php

namespace Tests\Feature\Http\Controllers;

use App\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PlanController
 */
class PlanControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function index_displays_view()
    {
        $plans = factory(Plan::class, 3)->create();

        $response = $this->get(route('plan.index'));

        $response->assertOk();
        $response->assertViewIs('plan.index');
        $response->assertViewHas('plans');
    }
}
