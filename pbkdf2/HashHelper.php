<?php
/*
 * Message by havoc AT defuse.ca
 * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
 * $algorithm - The hash algorithm to use. Recommended: SHA256
 * $password - The password.
 * $salt - A salt that is unique to the password.
 * $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
 * $key_length - The length of the derived key in bytes.
 * $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
 * Returns: A $key_length-byte key derived from the password and salt.
 *
 * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
 *
 * This implementation of PBKDF2 was originally created by https://defuse.ca
 * With improvements by http://www.variations-of-shadow.com
 */
/**
 * @name  HashHelper HashHelper
 * @author Paris Qian Sen
 * This module provides secure password hashing and authentication via the PBKDF2 method,
 * which is adapted from the original code by havoc AT defuse.ca(https://github.com/defuse)
 * https://defuse.ca/php-pbkdf2.htm
 * Thanks for the original author and this method only fulfill the gap before PHP5.5. cuz php5.5 added official support for pbkdf2
 * This Implementation is different from Yii-Pbkdf2(https://github.com/therealklanni/yii-pbkdf2).
 *
 * I changed the method name similar with what's written inside CPasswordHelper in Yii 1.1.14++
 * But since they are using completely different algorithm so that this could not extends CPasswordHelper.
 *
 * @require PHP5.3++,MCRYPT support.
 */
class HashHelper
{
    const _algorithm = 'sha256';
    const _iterations = '40000'; // Tested online, about 0.1 sec
    const _salt_bytes = 24; // About 32-characters
    const _hash_bytes = 24; // About 32-characters

    //Hash Code stor Format
    // format:salt:hash
    const HASH_SECTIONS = 2;
    const HASH_SALT_INDEX = 0;
    const HASH_PBKDF2_INDEX = 1;

    /**
     * @param string $password
     * @return string
     */
    public static function hashPassword($password)
    {
        //Require mcrypt_create_iv php extension to create salt bytes
        $salt = base64_encode(mcrypt_create_iv(self::_salt_bytes, MCRYPT_DEV_URANDOM));
        return $salt . ":" .
        base64_encode(self::pbkdf2(
            self::_algorithm,
            $password,
            $salt,
            self::_iterations,
            self::_hash_bytes,
            true
        ));
    }

    /*
     public static function pbkdf2($algorithm = self::_algorithm, $password, $salt, $count, $key_length, $raw_output = false)
    {
//        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
//            //PHP native pbkdf2 function
//            return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
//        } else {
//            // Sort of fallback
//            return self::c_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
//        }
    }
    */

    /**
     * @param $algorithm
     * @param $password
     * @param $salt
     * @param $count
     * @param $key_length
     * @param bool $raw_output
     * @return string|void
     */
    public static function pbkdf2($algorithm = self::_algorithm, $password, $salt, $count, $key_length, $raw_output = false)
    {
        $algorithm = strtolower($algorithm);
        if (!in_array($algorithm, hash_algos(), true))
            die('PBKDF2 ERROR: Invalid hash algorithm.');
        if ($count <= 0 || $key_length <= 0)
            die('PBKDF2 ERROR: Invalid parameters.');

        if (function_exists("hash_pbkdf2")) {
            // The output length is in NIBBLES (4-bits) if $raw_output is false!
            if (!$raw_output) {
                $key_length = $key_length * 2;
            }
            return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
        }

        $hash_length = strlen(hash($algorithm, "", true));
        $block_count = ceil($key_length / $hash_length);

        $output = "";
        for ($i = 1; $i <= $block_count; $i++) {
            // $i encoded as 4 bytes, big endian.
            $last = $salt . pack("N", $i);
            // first iteration
            $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
            // perform the other $count - 1 iterations
            for ($j = 1; $j < $count; $j++) {
                $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
            }
            $output .= $xorsum;
        }

        if ($raw_output)
            return substr($output, 0, $key_length);
        else
            return bin2hex(substr($output, 0, $key_length));
    }

    /**
     * @param $password
     * @param $good_hash
     * @return bool
     */
    public static function verifyPassword($password, $good_hash)
    {
        $params = explode(":", $good_hash);
        if (count($params) < self::HASH_SECTIONS) {
            return false;
        }
        $pbkdf2 = base64_decode($params[self::HASH_PBKDF2_INDEX]);
        return self::slow_equals(
            $pbkdf2,
            self::pbkdf2(
                self::_algorithm,
                $password,
                $params[self::HASH_SALT_INDEX],
                (int)self::_iterations,
                strlen($pbkdf2),
                true
            )
        );
    }

    /**
     * @param $a
     * @param $b
     * @return bool
     */
    public static function slow_equals($a, $b)
    {
        $diff = strlen($a) ^ strlen($b);
        for ($i = 0; $i < strlen($a) && $i < strlen($b); $i++) {
            $diff |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $diff === 0;
    }
}//Class
