<?php

use Illuminate\Database\Seeder;

class ConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('config')->delete();

        $config = [
            [
                'key' => 'start_time',
                'value' => '2017-12-30T08:00:00+08:00'
            ],
            [
                'key' => 'end_time',
                'value' => '2018-01-06T08:00:00+08:00'
            ],
            [
                'key' => 'flag_prefix',
                'value' => 'hgame{'
            ],
            [
                'key' => 'flag_suffix',
                'value' => '}'
            ],
            [
                'key' => 'ctf_pattern',
                'value' => 'hgame'
            ]
        ];

        DB::table('config')->insert($config);
    }
}