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
                'value' => '2017-11-11T08:00:00+08:00'
            ],
            [
                'key' => 'end_time',
                'value' => '2017-11-13T08:00:00+08:00'
            ],
            [
                'key' => 'flag_prefix',
                'value' => 'hctf{'
            ],
            [
                'key' => 'flag_suffix',
                'value' => '}'
            ]
        ];

        DB::table('config')->insert($config);
    }
}