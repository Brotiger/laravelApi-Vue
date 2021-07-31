<?php

namespace Database\Factories;

use App\Models\DeskList;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Desk;

class DeskListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DeskList::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $desk = Desk::all();

        return [
            'name' => $this->faker->text(20),
            'desk_id' => $desk[rand(0, count($desk) - 1)]
        ];
    }
}
