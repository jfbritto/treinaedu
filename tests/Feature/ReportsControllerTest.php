<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Training, Group, User, TrainingView};

class ReportsControllerTest extends TestCase
{
    public function test_reports_page_loads()
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('reports.index'))
            ->assertStatus(200);
    }

    public function test_filter_endpoint_returns_json()
    {
        $training = Training::factory()->create();
        TrainingView::factory(5)->create(['training_id' => $training->id]);

        $this->actingAs(User::factory()->admin()->create())
            ->getJson(route('reports.filter', ['training_id' => $training->id]))
            ->assertStatus(200)
            ->assertJsonStructure(['stats', 'data', 'tab']);
    }

    public function test_global_stats_reflect_all_data()
    {
        TrainingView::factory(10)->create();
        TrainingView::factory(5)->create(['completed_at' => now()]);

        $this->actingAs(User::factory()->admin()->create())
            ->getJson(route('reports.filter'))
            ->assertJsonPath('stats.total', 15)
            ->assertJsonPath('stats.completed', 5)
            ->assertJsonPath('stats.pending', 10);
    }
}
