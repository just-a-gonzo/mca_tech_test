<?php

namespace App\Components\ExternalAPIs\OpenWeatherMap\V2_5;

use Illuminate\Support\Facades\Http;

/**
 * @author Josif Gjorgjijevski <josifg@internal.zzz>
 * Class OpenWeatherMap
 * @package App\Components\ExternalAPIs\OpenWeatherMap\V2_5
 */
class OpenWeatherMap
{
    /**
     * @author Josif Gjorgjijevski <josifg@internal.zzz>
     * @var
     */
    public $request;
    /**
     * @author Josif Gjorgjijevski <josifg@internal.zzz>
     * @var mixed
     */
    public $key;

    /**
     *
     */
    public function __construct()
    {
        $this->key = env('OPEN_WEATHER_MAP_KEY');
    }

    /**
     * @param int $lon_left
     * @param int $lat_bottom
     * @param int $lon_right
     * @param int $lat_top
     * @param int $zoom
     * @param string $additionalQuery
     * @return array|mixed
     * @author Josif Gjorgjijevski <josifg@internal.zzz>
     * Makes API request to OpenWeatherMap for weather
     * https://openweathermap.org/current#cities
     */
    public function fetchCurrentWeatherDataRectangleZone(int $lon_left, int $lat_bottom, int $lon_right, int $lat_top, int $zoom, $additionalQuery = '')
    {
        $url = "api.openweathermap.org/data/2.5/box/city?bbox=$lon_left,$lat_bottom,$lon_right,$lat_top,$zoom&appid=$this->key&" . $additionalQuery;

        $response = Http::get($url);

        return $response->json();
    }

    /**
     * @param array $arr
     * @param array $keys
     * @return array
     * @author Josif Gjorgjijevski <josifg@internal.zzz>
     * Returns array of passed keys
     */
    public function filterKeys(array $arr, array $keys)
    {
        $result = [];

        foreach ($arr as $index => $item) {
            $temp = [];
            foreach ($keys as $i => $key) {
                $temp[$key] = $this->nestedArrayKeySearch($item, $key);
            }

            $cityName = $this->nestedArrayKeySearch($item, 'name');
            $result[$cityName] = $temp;
        }

        return $result;
    }


    /**
     * @param array $array
     * @param $key
     * @return false|mixed
     * @author Josif Gjorgjijevski <josifg@internal.zzz>
     * Returns value of the found property else false
     */
    public function nestedArrayKeySearch(array $array, $key)
    {
        foreach ($array as $elementKey => $element) {
            if ($elementKey === $key) {
                return $element;
            }
            if (is_array($element)) {
                $nested = $this->nestedArrayKeySearch($element, $key);
                // If the recursive call returns a value
                if ($nested !== false) {
                    return $nested;
                }
            }
        }
        // Not found, return false
        return false;
    }

}

?>
