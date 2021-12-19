<?php

namespace App\Console\Commands;

use App\Components\ExternalAPIs\OpenWeatherMap\V2_5\OpenWeatherMap;
use App\Models\City;
use App\Models\Weather;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class FetchWeatherData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect weather data from external API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // fetch weather data from Open-Weather-Map API
        $openWeatherMap = new OpenWeatherMap();
        $weatherData = $openWeatherMap->fetchCurrentWeatherDataRectangleZone(12, 32, 15, 37, 10);
        $filteredData = $openWeatherMap->filterKeys($weatherData['list'], ['temp', 'humidity', 'description']);

        // bulk insert cities
        $citiesDataToInsert = [];
        foreach ($filteredData as $cityName => $cityData) {
            $citiesDataToInsert[] = ['name' => $cityName, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
        }
        City::insertOrIgnore($citiesDataToInsert);
        Log::info('Trying to insert Cities', $citiesDataToInsert);

        // bulk insert weather
        $allCities = City::all()->toArray();
        $weatherDataToInsert = [];
        foreach ($filteredData as $cityName => $cityData) {
            $weatherDataToInsert[] = ['city_id' => City::getCityIdByName($allCities, $cityName), 'temp' => $cityData['temp'], 'humidity' => $cityData['humidity'], 'description' => $cityData['description'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
        }
        Weather::insert($weatherDataToInsert);
        Log::info('Inserted weather data', $weatherDataToInsert);

        return Command::SUCCESS;
    }
}
