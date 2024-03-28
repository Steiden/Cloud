<?php

namespace Database\Factories;

use App\Models\AccessType;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UsersFile>
 */
class UsersFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'file_id' => File::all()->random()->id,
            'access_type_id' => AccessType::all()->random()->id
        ];
    }
}
