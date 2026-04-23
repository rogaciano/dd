<?php

namespace Tests\Feature;

use App\Models\Denuncia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_paginates_denuncias(): void
    {
        $user = User::factory()->create();
        Denuncia::factory()->count(30)->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')
                ->has('denuncias.data', 25)
                ->where('metricas.total', 30)
            );
    }
}
