<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'firstname' => fake()->firstName("male"),
            'lastname' => fake()->lastName("male"),
            'email' => fake()->email(),
            'login' => fake()->userName(),
            'password' => Hash::make("password"),
            'active' => User::USER_STATUS_ACTIVE,
            'user_acl_role_id' => User::USER_ROLE_LOCUM,
            'user_acl_profession_id' => User::USER_PROFESSION_DEFAULT,
            'user_acl_package_id' => 4,
            "email_verified_at" => now()
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the type of user is employer.
     *
     * @return static
     */
    public function employer()
    {
        return $this->state(fn (array $attributes) => [
            'user_acl_role_id' => User::USER_ROLE_EMPLOYER,
        ]);
    }
}