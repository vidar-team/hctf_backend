<?php

use Illuminate\Database\Seeder;
use App\Team;
use Carbon\Carbon;

class TeamTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('teams')->delete();

        $teams = [
            [
                'team_name' => 'Vidar',
                'email' => 'aklis@vidar.club',
                'password' => bcrypt(hash('sha256', 'aklis@hctf')),
                'signUpTime' => Carbon::now('Asia/Shanghai'),
                'admin' => true,
                'banned' => false,
                'token' => str_random("32"),
                'lastLoginTime' => Carbon::now('Asia/Shanghai'),
            ],
            [
                'team_name' => 'HDUISA',
                'email' => 'birdway@vidar.club',
                'password' => bcrypt(hash('sha256', 'aklis@hctf')),
                'signUpTime' => Carbon::now('Asia/Shanghai'),
                'admin' => false,
                'banned' => false,
                'token' => str_random("32"),
                'lastLoginTime' => Carbon::now('Asia/Shanghai'),
            ],
        ];

        DB::table('teams')->insert($teams);
    }
}
