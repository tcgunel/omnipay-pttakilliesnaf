<?php

namespace Omnipay\PttAkilliEsnaf\Helpers;

class Helper
{
    public static function generateHash($apiPass, $clientId, $apiUser, $rnd, $timeString): string
    {
        $hashString = $apiPass . $clientId . $apiUser . $rnd . $timeString;
        $hashingbytes = hash('sha512', ($hashString), true);
        return base64_encode($hashingbytes);
    }
}
