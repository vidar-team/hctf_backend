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
        $team = new Team;
        $team->teamName = 'Vidar';
        $team->email = 'aklis@vidar.club';
        $team->password = bcrypt(hash('sha256', 'aklis@hctf'));
        $team->signUpTime = Carbon::now('Asia/Shanghai');
        $team->lastLoginTime = Carbon::now('Asia/Shanghai');
        $team->save();
    }
}
