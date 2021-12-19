<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'created_at', 'updated_at'
    ];

    public static function getCityIdByName(array $citiesArr, $cityName)
    {
        foreach ($citiesArr as $key => $city) {
            if( $city['name'] == $cityName ){
                return $city['id'];
            }
        }
    }

}
