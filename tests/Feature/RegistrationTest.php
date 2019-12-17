<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_register()
    {
        $this->get(route('register'))->assertSuccessful();

        $response = $this->post(route('register'), $this->userValidData());

        $response->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'name' => 'JorgeGarcia2',
            'first_name' => 'Jorge',
            'last_name' => 'García',
            'email' => 'jorge@email.com',
        ]);

        $this->assertTrue(
            Hash::check('secret', User::first()->password),
            'The password needs to be hashed'
        );
    }

    /** @test */
    public function the_name_is_required()
    {
        $this->post(
            route('register'),
            $this->userValidData(['name' => null])
        )->assertSessionHasErrors('name');
    }

    /** @test */
    public function the_name_must_be_a_string()
    {
        $this->post(
            route('register'),
            $this->userValidData(['name' => 1234])
        )->assertSessionHasErrors('name');
    }

    /** @test */
    public function the_name_may_not_be_greater_than_60_characters()
    {
        $this->post(
            route('register'),
            $this->userValidData(['name' => str_random(61)])
        )->assertSessionHasErrors('name');
    }

    /** @test */
    public function the_name_must_be_at_least_3_characters()
    {
        $this->post(
            route('register'),
            $this->userValidData(['name' => 'as'])
        )->assertSessionHasErrors('name');
    }

    /** @test */
    public function the_name_must_be_unique()
    {
        factory(User::class)->create(['name' => 'JorgeGarcia']);

        $this->post(
            route('register'),
            $this->userValidData(['name' => 'JorgeGarcia'])
        )->assertSessionHasErrors('name');
    }

    /** @test */
    public function the_name_may_only_contain_letters_and_numbers()
    {
        $this->post(
            route('register'),
            $this->userValidData(['name' => 'Jorge Garcia'])
        )->assertSessionHasErrors('name');

        $this->post(
            route('register'),
            $this->userValidData(['name' => 'JorgeGarcia<>'])
        )->assertSessionHasErrors('name');
    }

    /** @test */
    public function the_first_name_is_required()
    {
        $this->post(
            route('register'),
            $this->userValidData(['first_name' => null])
        )->assertSessionHasErrors('first_name');
    }

    /** @test */
    public function the_first_name_must_be_a_string()
    {
        $this->post(
            route('register'),
            $this->userValidData(['first_name' => 1234])
        )->assertSessionHasErrors('first_name');
    }

    /** @test */
    public function the_first_name_may_not_be_greater_than_60_characters()
    {
        $this->post(
            route('register'),
            $this->userValidData(['first_name' => str_random(61)])
        )->assertSessionHasErrors('first_name');
    }

    /** @test */
    public function the_first_name_must_be_at_least_3_characters()
    {
        $this->post(
            route('register'),
            $this->userValidData(['first_name' => 'as'])
        )->assertSessionHasErrors('first_name');
    }

    /** @test */
    public function the_first_name_may_only_contain_letters()
    {
        $this->post(
            route('register'),
            $this->userValidData(['first_name' => 'Jorge2'])
        )->assertSessionHasErrors('first_name');

        $this->post(
            route('register'),
            $this->userValidData(['first_name' => 'Jorge<>'])
        )->assertSessionHasErrors('first_name');
    }

    /** @test */
    public function the_last_name_is_required()
    {
        $this->post(
            route('register'),
            $this->userValidData(['last_name' => null])
        )->assertSessionHasErrors('last_name');
    }

    /** @test */
    public function the_last_name_must_be_a_string()
    {
        $this->post(
            route('register'),
            $this->userValidData(['last_name' => 1234])
        )->assertSessionHasErrors('last_name');
    }

    /** @test */
    public function the_last_name_may_not_be_greater_than_60_characters()
    {
        $this->post(
            route('register'),
            $this->userValidData(['last_name' => str_random(61)])
        )->assertSessionHasErrors('last_name');
    }

    /** @test */
    public function the_last_name_must_be_at_least_3_characters()
    {
        $this->post(
            route('register'),
            $this->userValidData(['last_name' => 'as'])
        )->assertSessionHasErrors('last_name');
    }

    /** @test */
    public function the_last_name_may_only_contain_letters()
    {
        $this->post(
            route('register'),
            $this->userValidData(['last_name' => 'Jorge2'])
        )->assertSessionHasErrors('last_name');

        $this->post(
            route('register'),
            $this->userValidData(['last_name' => 'Jorge<>'])
        )->assertSessionHasErrors('last_name');
    }

    /** @test */
    public function the_email_is_required()
    {
        $this->post(
            route('register'),
            $this->userValidData(['email' => null])
        )->assertSessionHasErrors('email');
    }

    /** @test */
    public function the_email_must_be_a_valid_email_address()
    {
        $this->post(
            route('register'),
            $this->userValidData(['email' => 'invalidemail'])
        )->assertSessionHasErrors('email');
    }

    /** @test */
    public function the_email_must_be_unique()
    {
        factory(User::class)->create(['email' => 'jorge@email.com']);

        $this->post(
            route('register'),
            $this->userValidData(['email' => 'jorge@email.com'])
        )->assertSessionHasErrors('email');
    }

    /** @test */
    public function the_password_is_required()
    {
        $this->post(
            route('register'),
            $this->userValidData(['password' => null])
        )->assertSessionHasErrors('password');
    }

    /** @test */
    public function the_password_must_be_a_string()
    {
        $this->post(
            route('register'),
            $this->userValidData(['password' => 1234])
        )->assertSessionHasErrors('password');
    }

    /** @test */
    public function the_password_must_be_at_least_6_characters()
    {
        $this->post(
            route('register'),
            $this->userValidData(['password' => 'asdfg'])
        )->assertSessionHasErrors('password');
    }

    /** @test */
    public function the_password_must_be_confirmed()
    {
        $this->post(
            route('register'),
            $this->userValidData([
                'password' => 'secret',
                'password_confirmation' => null
            ])
        )->assertSessionHasErrors('password');
    }

    /**
     * @param array $overrides
     * @return array
     */
    protected function userValidData($overrides = []): array
    {
        return array_merge([
            'name' => 'JorgeGarcia2',
            'first_name' => 'Jorge',
            'last_name' => 'García',
            'email' => 'jorge@email.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ], $overrides);
    }
}
