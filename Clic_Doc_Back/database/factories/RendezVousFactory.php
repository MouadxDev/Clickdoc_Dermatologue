<?php

namespace Database\Factories;

use App\Models\RendezVous;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class RendezVousFactory extends Factory
{
    protected $model = RendezVous::class;

    public function definition()
    {
        return [
            'patient_id' => $this->faker->numberBetween(1, 10),
            'doctor_id' => $this->faker->numberBetween(1, 5),
            'type' => 'consultation',
            'date' => Carbon::today()->format('Y-m-d'),
            'heure' => $this->faker->time($format = 'H:i:s', $max = 'now'),
            'statut' => 'scheduled',
            'color' => 'blue',
        ];
    }
}
