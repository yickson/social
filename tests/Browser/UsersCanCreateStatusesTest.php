<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UsersCanCreateStatusesTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     *
     * @test
     * @throws \Throwable
     */
    public function users_can_create_statuses()
    {
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->type('body', 'Mi primer status')
                    ->press('#create-status')
                    ->waitForText('Mi primer status')
                    ->assertSee('Mi primer status')
                    ->assertSee($user->name)
            ;
        });
    }

    /**
     * @test
     * @throws \Throwable
     */
    public function users_can_see_statuses_in_real_time()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this->browse(function (Browser $browser1, Browser $browser2) use ($user1, $user2) {
            $browser1->loginAs($user1)
                ->visit('/');

            $browser2->loginAs($user2)
                ->visit('/')
                ->type('body', 'Mi primer status')
                ->press('#create-status')
                ->waitForText('Mi primer status')
                ->assertSee('Mi primer status')
                ->assertSee($user2->name)
            ;

            $browser1->waitForText('Mi primer status')
                ->assertSee('Mi primer status')
                ->assertSee($user2->name);
        });
    }
}
