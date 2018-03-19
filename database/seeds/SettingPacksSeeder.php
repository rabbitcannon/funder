<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Eos\Common\SettingPack;

class SettingPacksSeeder extends Seeder {

    public function run()
    {
        SettingPack::create(['quantum_start' => Carbon::now(),
                             'quantum_end' => (new Carbon())->addYear(10),
                             'pack' => [] ]);
    }
}