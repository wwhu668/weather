<?php


namespace Wwhu\Weather;


use GuzzleHttp\Client;
use Wwhu\Weather\Exceptions\HttpException;
use Wwhu\Weather\Exceptions\InvalidArgumentException;

class Weather
{
    protected $key;

    protected $guzzleOptions = [];

    /**
     * Weather constructor.
     * @param $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * @param array $guzzleOptions
     */
    public function setGuzzleOptions(array $guzzleOptions)
    {
        $this->guzzleOptions = $guzzleOptions;
    }

    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        if (!in_array(strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): '. $type);
        }

        if (!in_array(strtolower($format), ['json', 'xml'])) {
            throw new InvalidArgumentException('Invalid response format: '. $format);
        }

        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'extensions' => $type,
            'output' => $format,
        ]);

        try {
            $request = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();

            return $format === 'json' ? json_decode($request, true) : $request;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}