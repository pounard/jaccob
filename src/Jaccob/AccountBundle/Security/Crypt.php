<?php

namespace Jaccob\AccountBundle\Security;

/**
 * Some of the functions in there such as the slowEquals() and the
 * createSalt() implementations have been taken from
 *
 *   https://crackstation.net/hashing-security.htm
 *
 * All credits goes to its author.
 */
class Crypt
{
    /**
     * Salt size
     *
     * Salt size should be at least the same as the hashing function byte length
     * Per default we set the SHA512 hash length
     */
    const SALT_BYTE_SIZE = 64;

    /**
     * Default hash algorithm
     */
    const HASH_ALGORITHM = 'sha512';

    /**
     * Create a non predictable but reproductible hash from the given string
     *
     * @return string
     */
    static public function getSimpleHash($string, $salt = null)
    {
        return base64_encode(hash_hmac(self::HASH_ALGORITHM, $string, $salt, true));
    }

    /**
     * Create a non predictable random hash
     *
     * @return string
     */
    static public function createRandomToken()
    {
        return base64_encode(hash_hmac(
            self::HASH_ALGORITHM,
            self::createPassword(64),
            self::createSalt(),
            true
        ));
    }

    /**
     * Create a non predictable plain text random hash
     *
     * @return string
     */
    static public function createRandomPlainToken()
    {
        return preg_replace('/[^a-zA-Z0-9]+/', '', self::createRandomToken());
    }

    /**
     * Create new salt
     *
     * @return string
     */
    static public function createSalt()
    {
        return base64_encode(mcrypt_create_iv(self::SALT_BYTE_SIZE, MCRYPT_DEV_URANDOM));
    }

    /**
     * Create a new random password
     *
     * WARNING: This won't give you a really secure password because I'm too
     * lazy to write a good algorithm
     *
     * @param int $length
     *
     * @return string
     */
    static public function createPassword($length = 10)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }

    /**
     * Get password hash
     *
     * @param string $password
     * @param string $salt
     */
    static public function getPasswordHash($password, $salt = null)
    {
        $options = [];

        if (null !== $salt) {
            $options['salt'] = $salt;
        }

        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    /**
     * Encrypt data
     *
     * @param string $text
     * @param string $key
     *
     * @return string
     */
    static public function encrypt($text, $key = null)
    {
        throw new \Exception("Not implemented yet");
    }

    /**
     * Decrypt data
     *
     * @param string $text
     * @param string $key
     *
     * @return string
     */
    static public function decrypt($text, $key = null)
    {
        throw new \Exception("Not implemented yet");
    }

    /**
     * Generate a new public/private key pairs
     *
     * @return string[]
     *   First value is private key, second value is public key, third is key type
     */
    static public function generateRsaKeys()
    {
        $config = [
            "digest_alg" => self::HASH_ALGORITHM,
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $privKey = '';
        $pubKey = '';
        $type = 'rsa';

        // Create the private and public key
        $res = openssl_pkey_new($config);

        // Extract the private key
        openssl_pkey_export($res, $privKey);

        // Extract the public key
        $pubKey = openssl_pkey_get_details($res);

        return [$privKey, $pubKey['key'], $type];
    }
}
