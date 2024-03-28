<?php

namespace Database\Factories;

use App\Models\FileType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->filePath(),
            'uri' => $this->faker->filePath(),
            'size' => random_int(0, 10000),
            'file_type_id' => FileType::all()->random()->id,
        ];
    }
}
