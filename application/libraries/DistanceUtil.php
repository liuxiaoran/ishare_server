<?php
/**
 * Created by PhpStorm.
 * User: Zhan
 * Date: 2015/5/11
 * Time: 21:43
 */

class DistanceUtil {

    public static function getMilesBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2) {
        $theta = $longitude1 - $longitude2;
        $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
//        $feet = $miles * 5280;
//        $yards = $feet / 3;
//        $kilometers = $miles * 1.609344;
//        $meters = $kilometers * 1000;
        return $miles;
    }

    public static function getKilometersBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2) {
        $miles = self::getMilesBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2);
        return $miles * 1.609344;
    }

    public static function getMetersBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2) {
        $miles = self::getMilesBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2);
        return $miles * 1609.344;
    }

    public static function getFeetBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2) {
        $miles = self::getMilesBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2);
        return $miles * 5280;
    }

    public static function getYardsBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2) {
        $miles = self::getMilesBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2);
        return $miles * 5280 / 3;
    }
}