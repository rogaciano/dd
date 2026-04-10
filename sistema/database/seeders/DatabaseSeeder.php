<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PapeisSeeder::class,
            GruposAssuntoSeeder::class,
            AssuntosSeeder::class,
            TiposResultadoSeeder::class,
        ]);

        $adminRole = \App\Models\Papel::where('slug', 'administrador')->first();

        // Seed a default admin if it doesn't exist
        $user = User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Administrador',
            'password' => bcrypt('admin123'),
        ]);

        if ($adminRole && !$user->papeis()->where('papel_id', $adminRole->id)->exists()) {
            $user->papeis()->attach($adminRole->id);
        }

        $this->call([
            DevDummySeeder::class,
        ]);
    }
}
