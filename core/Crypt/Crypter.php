<?php

namespace Core\Crypt;

use Core\Exceptions\Exception;

class Crypter
{
    /**
     * Crypter is booted
     *
     * @var bool
     */
    private static $booted;

    /**
     * Crypter instance
     *
     * @var Core\Crypter\Crypter
     */
    private static $instance;

    /**
     * Cipher name
     *
     * @var string
     */
    private $cipher;

    /**
     * Length of cipher
     *
     * @var int
     */
    private $length;

    /**
     * Application key
     *
     * @var string
     */
    private $key;

    /**
     * Constructor of cryptographic class
     *
     * @param Core\Bootstrapers\Application
     * @return Core\Crypt\Crypter
     */
    public function __construct(string $cipher)
    {
        // Cipher is not supported
        if ( ! static::supported($cipher) ) {
            throw new \RuntimeException('The only ciphers supported are AES-256-CBC, AES-128-CBC, not [{$cipher]');
        }

        $this->cipher = $cipher;
        $this->length  = $this->length();
        $this->key = $this->random($this->length);
    }

    /**
     * Boot crypter
     *
     * @param string $cipher
     * @return Core\Crypter\Crypter
     */
    public static function boot(string $cipher)
    {
        if ( ! self::$booted ) {
            self::$instance = new self($cipher);
        }

        return self::$instance;
    }

    /**
     * Get a singleton instance of class
     *
     * @return Core\Crypter\Crypter
     */
    static function instance()
    {
        return self::$instance;
    }

    /**
     * Verify if given cipher is supported by application
     *
     * @param string $cipher Cipher name
     * @return bool
     */
    static function supported(string $cipher)
    {
        return ($cipher == 'AES-256-CBC' || $cipher == 'AES-128-CBC');
    }

    /**
     * Get application key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

     /**
     * Generate a cryptographically secure random bytes
     *
     * @param int $length Length of bytes
     * @return string
     */
    static function random(int $length = 16)
    {
        return base64_encode(random_bytes($length));
    }

    /**
     * Encrypt value using encrypter cipher
     *
     * @throws Core\Exceptions\Crypter\CrypterException
     *
     * @param string $value Value to encrypt
     * @param bool $serialize Wheter should serialize value before encrypt
     * @return string
     */
    public function encrypt(string $value, bool $serialize = true)
    {
        // Initial vectors of AES algorithm
        $iv = $this->random(openssl_cipher_iv_length($this->cipher));

        // The value will be encrypted with OpenSSL. After that it will proceed
        // to calculate the HMAC of the encrypted value for further authenticity
        // purposes.
        $data = \openssl_encrypt(
            $serialize ? serialize($value) : $value,
            $this->cipher,
            base64_decode($this->key),
            0,
            $iv
        );

        // Data returned false
        if ( ! $data ) {
            throw new CrypterException('Data could not be encrypted.');
        }

        // Here the initial vector will be encoded with base64 to calculate the HMAC
        // with the encrypted data so then we can verify content authenticity. After that
        // we JSON data with payload.
        $hmac = $this->hmac($iv = base64_encode($iv), $data);
        $json = \json_encode(compact('data', 'iv', 'hmac'));

        // JSON has errors
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            throw new CrypterException('Data could not be encrypted.');
        }

        return \base64_encode($json);
    }

    /**
     * Decrypt payload using OpenSSL
     *
     * @throws Core\Exceptions\Crypter\CrypterException
     *
     * @param string $payload Payload to decrypt
     * @param bool $unserialize Should unserialize data after decrypt
     * @return string
     */
    public function decrypt(string $payload, bool $unserialize = true)
    {
        // Get JSON payload
        $payload = $this->getPayload($payload);

        // Base64 decoded initial vector
        $iv = base64_decode($payload['iv']);

        // Decrypt payload using OpenSSL.
        $decrypted = \openssl_decrypt(
            $payload['data'],
            $this->cipher,
            base64_decode($this->key),
            0,
            $iv
        );

        if ( ! $decrypted ) {
            throw new CrypterException('Could not decrypt payload.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * Get JSON payload
     *
     * @throws Core\Exceptions\Crypter\CrypterException
     *
     * @param string $payload
     * @return array
     */
    private function getPayload(string $payload)
    {
        $payload = json_decode(\base64_decode($payload), true);

        // Check wheter payload is a valid payload, with valid keys
        // and validate it's authenticity using HMAC to calculate the hash
        if ( ! $this->validJson($payload) ) {
            throw new CrypterException('Invalid JSON payload.');
        }

        if ( ! $this->validHMAC($payload) ) {
            throw new CrypterException('Failed authenticating payload.');
        }

        return $payload;
    }

    /**
     * Validate payload JSON with valid keys
     *
     * @param mixed $payload Payload
     * @return bool
     */
    private function validJson($payload)
    {
        return is_array($payload) && isset(
            $payload['iv'], $payload['data'], $payload['hmac']
        );
    }

    /**
     * Validate payload JSON with valid keys
     *
     * @param mixed $payload Payload
     * @return bool
     */
    private function validHMAC($payload)
    {
        $calculated = $this->calculateHMAC($payload, $bytes = self::random());

        return hash_equals(
            hash_hmac('sha256', $payload['hmac'], $bytes, true), $calculated
        );
    }

    /**
     * Get a calculated HMAC from payload initial vector and data using bytes as key
     *
     * @param mixed $payload
     * @param mixed $bytes@
     * @return string
     */
    private function calculateHMAC($payload, $bytes)
    {
        return \hash_hmac('sha256', $this->hmac($payload['iv'], $payload['data']), $bytes, true);
    }

    /**
     * Calculate HMAC from initial vector and encrypted data
     *
     * @param mixed $iv Initial vector
     * @param mixed $data Encrypted data
     * @return string
     */
    private function hmac($iv, $data)
    {
        return hash_hmac('sha256', $iv.$data, base64_decode($this->key));
    }

    /**
     * Get cipher length
     *
     * @return int
     */
    private function length()
    {
        return $this->cipher == 'AES-128-CBC' ? 16:32;
    }
}
