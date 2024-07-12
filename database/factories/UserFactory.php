<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

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

    /** Usuários para testes */

    public function definition(): array
    {
        $faker = \Faker\Factory::create();
        return [
            'name' => $faker->name(),
            'role_id' => 1,
            'email' => $faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('admin1234'),
            'address' => 'asdasd',
            'nif' => rand(100000000, 999999999),
            'tel' => rand(100000000, 999999999),
            'birth_date' => '2000-01-01',
            'remember_token' => Str::random(10),
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'ADMIN',
                'role_id' => 3,
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => Hash::make('admin1234'),
                'address' => 'asdasd',
                'nif' => 123123123,
                'tel' => 123123123,
                'birth_date' => '2000-01-01',
                'remember_token' => Str::random(10),
            ];
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }



}

