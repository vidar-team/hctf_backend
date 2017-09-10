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
//        $team = new Team;
//        $team->team_name = 'Vidar';
//        $team->email = 'aklis@vidar.club';
//        $team->password = bcrypt(hash('sha256', 'aklis@hctf'));
//        $team->signUpTime = Carbon::now('Asia/Shanghai');
//        $team->lastLoginTime = Carbon::now('Asia/Shanghai');
//        $team->save();

        DB::table('teams')->delete();

        $teams = [
            array(
                'team_name' => 'Vidar',
                'email' => 'aklis@vidar.club',
                'password' => bcrypt(hash('sha256', 'aklis@hctf')),
                'signUpTime' => Carbon::now('Asia/Shanghai'),
                'lastLoginTime' => Carbon::now('Asia/Shanghai'),
            ),
            array(
                'team_name' => 'HDUISA',
                'email' => 'birdway@vidar.club',
                'password' => bcrypt(hash('sha256', 'aklis@hctf')),
                'signUpTime' => Carbon::now('Asia/Shanghai'),
                'lastLoginTime' => Carbon::now('Asia/Shanghai'),
            ),
        ];

        DB::table('teams')->insert($teams);
    }
}
