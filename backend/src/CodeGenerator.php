<?php

// builds the random short codes, e.g. k7Rm2p
class CodeGenerator
{
    // left out 0/O and 1/l/I so codes are easy to read and type
    private const ALPHABET = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public static function make(int $length): string
    {
        $max = strlen(self::ALPHABET) - 1;
        $code = '';

        // pick one random character at a time until the code is long enough.
        // random_int is used over rand() because it's cryptographically secure
        for ($i = 0; $i < $length; $i++) {
            $code .= self::ALPHABET[random_int(0, $max)];
        }

        return $code;
    }
}