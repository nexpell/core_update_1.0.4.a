<?php
declare(strict_types=1);

class ServerGeoIp
{
    public static function getCountry(string $ip): string
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return '';
        }

        // Private / lokale IPs ausschließen
        if (
            filter_var(
                $ip,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            ) === false
        ) {
            return '';
        }

        // ⚠️ Externer Dienst – nur Server-IP
        $json = @file_get_contents(
            "https://ip-api.com/json/{$ip}?fields=countryCode"
        );

        if (!$json) {
            return '';
        }

        $data = json_decode($json, true);
        return strtoupper((string)($data['countryCode'] ?? ''));
    }
}
