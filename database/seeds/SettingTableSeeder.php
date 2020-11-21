<?php

use Illuminate\Database\Seeder;
use App\Models\Setting;
class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting    =   Setting::find(1);
        if (!$setting){
            Setting::create([
                'credit_affiliate'  =>  10,
                'customer_point'  =>  10,
            ]);
        }
    }
}
