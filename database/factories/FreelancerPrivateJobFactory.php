<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=FreelancerPrivateJob>
 */
class FreelancerPrivateJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "freelancer_id" => User::where("user_acl_role_id", 2)->get()->random()->first()->id,
            "emp_name" => fake()->name(),
            "emp_email" => fake()->email(),
            "job_title" => fake()->jobTitle(),
            "job_rate" => random_int(100, 500),
            "job_location" => fake()->address(),
            "job_date" => fake()->dateTimeInInterval("+2 days", "+20 days"),
            "status" => 0
        ];
    }

    /**
     * Indicate that the model's user id .
     *
     * @return static
     */
    public function freelancer(int $id)
    {
        return $this->state(fn (array $attributes) => [
            'freelancer_id' => $id,
        ]);
    }
    /**
     * Indicate that the model's user id .
     *
     * @return static
     */
    public function status(int $satus)
    {
        return $this->state(fn (array $attributes) => [
            'satus' => $satus,
        ]);
    }
}