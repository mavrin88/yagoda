<?php

namespace App\Services;

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKey;
use phpseclib3\Crypt\PublicKeyLoader;

class EncryptionCardNumber
{
    public $key = 'MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEArMoOdwezmRAS9C5uONYc
TYutmNgk2pdkX+T2BqsWvTY/m8cmnYYECtcfHiWnrvGpH9RIZgxk0X3nAl2NckKT
yetHJZgsMUxffJ3w5LzUOlNgvDzxaTrMyyRbAQTS2hJA0sEFLy2Hlhe2ekqRmlb6
Fj5uG7xSOvYaybJAqAYZYVTVXUaTaEzqqKjH8aOvLN3XkG335H8pBdRMvEfsaiK/
/pxl37DuaXr9lrv+vL+Y6bImYAzD1sFl9Z62Zg2RePhCDynyDU9LpaS0ukMT9YyO
ae+/L6kDdIh70n0UDZBXBnwLD6CaJMK6YL1RyVJOCkJ95h7pxMpOm6yDGh/yn5zx
3vJSu8SMQp3fhQiMeLS9RuB3wWTV68ou8vnQFHwvWgVGXm2Kr7CUNDvt/Hkyu7Aw
RP4ccBBprHhYz5sZwCiv5UQCYbMPQ7/ud+XFVFOTgdiku3vaiFbroJOk9VIwkDct
0A43x87Ly1WmWq4c4sxU64M09Msj1xJC1lgTunkiQ3pPcIZvi6RVXiZqbq3SrpCd
Mcc0U8SHFANGaEvC6aKvvq+difgh1M4sPycjC10250+L/239CnsKwG5m3DEHP68d
EEyhd1fliWqaTjf2M/XLS75CjB7dx+9oEE2vxUOM4E58o3lA5ga1PAl05F8+hSd+
UU8Q7we5m/0ok4z3tov/Ur0CAwEAAQ==';
    public function encryptCardNumber($cardNumber)
    {
        $rsa = RSA::load($this->key);

        $encrypted = $rsa->encrypt($cardNumber);

        return base64_encode($encrypted);
    }

    function rsaEncrypt(string $message): string
    {
        $publicKey = "-----BEGIN PUBLIC KEY-----
            MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEArMoOdwezmRAS9C5uONYc
            TYutmNgk2pdkX+T2BqsWvTY/m8cmnYYECtcfHiWnrvGpH9RIZgxk0X3nAl2NckKT
            yetHJZgsMUxffJ3w5LzUOlNgvDzxaTrMyyRbAQTS2hJA0sEFLy2Hlhe2ekqRmlb6
            Fj5uG7xSOvYaybJAqAYZYVTVXUaTaEzqqKjH8aOvLN3XkG335H8pBdRMvEfsaiK/
            /pxl37DuaXr9lrv+vL+Y6bImYAzD1sFl9Z62Zg2RePhCDynyDU9LpaS0ukMT9YyO
            ae+/L6kDdIh70n0UDZBXBnwLD6CaJMK6YL1RyVJOCkJ95h7pxMpOm6yDGh/yn5zx
            3vJSu8SMQp3fhQiMeLS9RuB3wWTV68ou8vnQFHwvWgVGXm2Kr7CUNDvt/Hkyu7Aw
            RP4ccBBprHhYz5sZwCiv5UQCYbMPQ7/ud+XFVFOTgdiku3vaiFbroJOk9VIwkDct
            0A43x87Ly1WmWq4c4sxU64M09Msj1xJC1lgTunkiQ3pPcIZvi6RVXiZqbq3SrpCd
            Mcc0U8SHFANGaEvC6aKvvq+difgh1M4sPycjC10250+L/239CnsKwG5m3DEHP68d
            EEyhd1fliWqaTjf2M/XLS75CjB7dx+9oEE2vxUOM4E58o3lA5ga1PAl05F8+hSd+
            UU8Q7we5m/0ok4z3tov/Ur0CAwEAAQ==
            -----END PUBLIC KEY-----";

        $rsaPublicKey = PublicKeyLoader::load($publicKey)->withPadding(RSA::ENCRYPTION_OAEP);

        $encrypted = $rsaPublicKey->encrypt($message);

        return base64_encode($encrypted);
    }
}
