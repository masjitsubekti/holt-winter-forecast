<?php
// Path to run ./vendor/bin/phpunit --bootstrap vendor/autoload.php Tests/HoltWintersTest.php
require_once dirname(__FILE__) . "/../holt-winters.php";

use PHPUnit\Framework\TestCase;
class HoltWintersTest extends TestCase
{
    public function setUp()
    {
        $alpha = 0.088;
        $beta = 0.90;
        $gamma = 0.62;
        $L = 7;
        $series = array(
            1987.3998,
            3764.5446,
            4393.6553,
            3971.7485,
            3036.0817,
            2561.6444,
            2547.6655,
            2959.3183,
            3563.061,
            3617.4088,
            3462.2207,
            3151.7703,
            3120.3494,
            1990.912,
            2090.9543,
            3165.8431,
            3122.6219,
            3217.2984,
            3037.2325,
            2341.7996,
            1674.2432,
            2001.9765,
            2240.6456,
            3082.8125,
            2623.7677,
            2410.4282,
            2555.9663,
            1957.4731,
            2010.3246,
            3051.5427,
            3294.7149,
            3451.9702,
        );

        $this->h = new HoltWinters($alpha, $beta, $gamma, $L, $series);
    }

    public function testForecast()
    {
        $expected_forecast = array(
            32 => 3145.85777893561,
            33 => 3208.76867228701,
            34 => 2505.45777780566,
            35 => 2735.59344075677,
            36 => 3936.37849031645,
            37 => 4529.20228784049,
            38 => 4544.88642642984,
            39 => 4202.23468508737,
            40 => 4236.94788765385,
            41 => 3273.13573112581,
            42 => 3538.63494929676,
            43 => 5045.40567197515,
            44 => 5755.87880993405,
            45 => 5729.95911359811,
            46 => 5258.61159123913,
            47 => 5265.1271030207,
            48 => 4040.81368444595,
            49 => 4341.67645783675,
            50 => 6154.43285363386,
        );

        foreach ($expected_forecast as $k => $f) {
            $this->assertEquals($f, $this->h->forecast($k), "incorrect forecast (k=$k)", 0.01);
        }
    }
}
