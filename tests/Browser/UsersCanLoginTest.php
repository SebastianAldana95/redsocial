<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class UsersCanLoginTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @test
     * @throws Throwable
     */
    public function registered_users_can_login()
    {
        User::factory()->create(['email' => 'sebastian@email.com']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'sebastian@email.com')
                    ->type('password', 'password')
                    ->press('@login-btn')
                    ->assertPathIs('/')
                    ->assertAuthenticated();
        });
    }

    /** @test
     * @throws Throwable
     */
    public function user_cannot_login_with_invalid_information()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', '')
                ->press('@login-btn')
                ->assertPathIs('/login')
                ->assertPresent('@errors')
            ;
        });
    }
}
