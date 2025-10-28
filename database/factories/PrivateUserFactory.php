<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrivateUser>
 */
class PrivateUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "employer_id" => User::where("user_acl_role_id", 3)->get()->random()->first()->id,
            "name" =>  fake()->name("male"),
            "email" => fake()->safeEmail(),
            "mobile" => fake()->phoneNumber()
        ];
    }

    /**
     * Indicate that the model's employer id .
     *
     * @return static
     */
    public function employer(int $id)
    {
        return $this->state(fn (array $attributes) => [
            'employer_id' => $id,
        ]);
    }
}