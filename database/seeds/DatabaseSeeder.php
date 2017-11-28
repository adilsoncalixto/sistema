<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\User;
use Bican\Roles\Models\Role;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(UsersTableSeeder::class);
        $this->call(RulesAndPemissionsSeeder::class);

        Model::reguard();
    }
}


class UsersTableSeeder extends  Seeder {

    public function run() {

        DB::table('users')->insert([
            'name' => 'Administrador',
            'email' => 'admin@dominio.com.br',
            'password' =>  bcrypt('admin@123'),
        ]);

        DB::table('parametros')->insert([
            'name' => 'path_imagens',
            'description' => 'Caminho das imagens',
            'value' => '/home/marcelo/PhpstormProjects/liga_hortolandia/portal/public/imagens/'
        ]);

    }
}

class RulesAndPemissionsSeeder extends Seeder {

    public function run() {

        $adminRole = Role::create([
            'name' => 'Administrador',
            'slug' => 'admin',
            'description' => 'Rule de administrador',
            'level' => 1,
        ]);

        $dirigenteRole = Role::create([
            'name' => 'Dirigente',
            'slug' => 'dirigente',
            'description' => 'Rule de dirigente',
            'level' => 1,
        ]);

        $equipeRole = Role::create([
            'name' => 'Equipe',
            'slug' => 'equipe',
            'description' => 'Rule de equipe',
            'level' => 1,
        ]);

        $jogadorRole = Role::create([
            'name' => 'Jogador',
            'slug' => 'jogador',
            'description' => 'Rule de jogador',
            'level' => 1,
        ]);

        $user = User::find(1);

        $user->attachRole($adminRole);
    }



}