<?php

namespace App\Helper;

class HakAksesHelper
{
    // getAkses
    public static function getAkses($model, $userId)
    {
        $rawAkses = $model::where('user_id', $userId)->first();
        if(!$rawAkses){
            return [];
        }

        return explode(",", $rawAkses->akses);
    }
}