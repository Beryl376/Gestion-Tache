<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tache;

class TacheSeeder extends Seeder
{
    public function run()
    {
        Tache::create([
            'title' => 'Créer le dashboard',
            'descriptions' => 'Développer le tableau de bord admin',
            'completed' => 0,
            'user_id' => 1
        ]);

        Tache::create([
            'title' => 'Créer API',
            'descriptions' => 'Développer API REST pour les tâches',
            'completed' => 0,
            'user_id' => 1
        ]);

        Tache::create([
            'title' => 'Tester application',
            'descriptions' => 'Faire les tests fonctionnels',
            'completed' => 1,
            'user_id' => 2
        ]);
    }
}