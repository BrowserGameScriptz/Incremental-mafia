<?php

namespace App\Libraries;
use Faker\Factory as Faker;
use App\Soldier;

class SoldierFactory
{
    public static function produceSoldier($soldierType,$userID){
        if($soldierType == "picciotto")
            return SoldierFactory::producePicciotto($userID);
    }

    private static function producePicciotto($userID){
        $faker = Faker::create();
        $soldier = new Soldier;
        $soldier->setAttribute('name', $faker->name);
        $soldier->setAttribute('influence_per_minute', UtilityFunctions::float_rand(\Config::get('constants.picciotto_min_IPM'),\Config::get('constants.picciotto_max_IPM')));
        $soldier->setAttribute('user_id',$userID);
        return $soldier;
    }
}