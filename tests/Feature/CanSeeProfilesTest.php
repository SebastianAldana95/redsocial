<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CanSeeProfilesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_see_profiles_test()
    {
        $this->withoutExceptionHandling();

        User::factory()->create(['name' => 'Sebastian']);

        $this->get('@Sebastian')->assertSee('Sebastian');
    }
}
