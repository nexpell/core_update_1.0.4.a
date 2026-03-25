<?php
declare(strict_types=1);

class ServerGeoIp
{
    public static function getCountry(string $ip): string
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return '';
        }

        $json = @file_get_contents("https://ipinfo.io/{$ip}/country");

        if (!$json) {
            return '';
        }

        return strtoupper(trim($json));
    }
}
