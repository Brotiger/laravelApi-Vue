<?php

namespace Database\Factories;

use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DeskList;

class CardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Card::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $list = DeskList::all();

        return [
            'name' => $this->faker->text(20),
            'desk_list_id' => $list[rand(0, count($list) - 1)]
        ];
    }
}
