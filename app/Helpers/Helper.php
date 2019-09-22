<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class Helper
{

    /**
     * Unset object keys in json response
     *
     * @param $var
     * @return mixed
     */
    public static function unsetKeys($var)
    {
        if (!is_array($var)) {
            $var = $var->toArray();
        }
        foreach ($var as $k => &$v) {
            if (is_null($v)) {
                //unset($var[$k]);
                $v = "";
            }
        }
        return $var;
    }

    /**
     * Generate random string
     *
     * @param int $length
     * @return string
     */
    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Generate verification code
     *
     * @return int
     */
    public static function generateVerificationCode()
    {
        $digits = 4;
        $num = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        return $num;
    }

    /**
     * Escapes special characters in a string for use in an SQL statemen
     *
     * @param $inp
     * @return array|mixed
     */
    public static function mysql_escape($inp)
    {
        if (is_array($inp)) return array_map(__METHOD__, $inp);

        if (!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    }

    /**
     * Get cities
     *
     * @param $state
     * @return mixed
     */
    public static function getCities($state)
    {
        $cities = DB::table('cities as c')
            ->join('states as s', 's.id', '=', 'c.state_id')
            ->where('s.name', '=', $state)
            ->select('c.name')
            ->lists('c.name', 'c.name');

        return $cities;
    }

    /**
     * Get latitude and longitude from address and zip code
     *
     * @param $address
     * @param $zip_code
     * @return array
     */
    public static function getGeocode($address, $zip_code)
    {
        $apiKey = config('constants.api-key');
        $address = str_replace(" ", "+", $address);
        $jsonResponse = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&components=country:IN|postal_code:$zip_code&key=$apiKey");
        $geoCode = json_decode($jsonResponse);
        return array('latitude' => $geoCode->{'results'}[0]->{'geometry'}->{'location'}->{'lat'}, 'longitude' => $geoCode->{'results'}[0]->{'geometry'}->{'location'}->{'lng'});
    }
}