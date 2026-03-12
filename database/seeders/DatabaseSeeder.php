<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // L'ordre est important : les Users doivent exister avant les Taches (clé étrangère)
        $this->call([
            UserSeeder::class,
            TacheSeeder::class,
        ]);
    }
}
