<?php
namespace Core;
use Core\Exception\Error;

/**
 * OpenSSL based encrypt decrypt library
 */
class Crypt
{
    /**
     * Default cipher mode
     * @var string
     */
	const cipher = 'aes-256-cbc';

    /**
     * First bytes to hide cipher
     * @var int
     */
	const FIRST_SALT = 3;

    /**
     * Second bytes to hide cipher
     * @var int
     */
	const SECONT_SALT = 5;
	
	/**
	 * IV configuration
	 * @var int
	 */
	private int $length;

	/**
	 * @param string $key 		hash key
	 * @param string $cipher 	cipher algorythm
	 * @throws Exception
	 */
	public function __construct(private ?string $key = null, private ?string $cipher = self::cipher)
	{
		$this->length   = openssl_cipher_iv_length($cipher);
	}
	
	/**
	 * Encode to base64 cripted string
	 * @param array $data
	 * @return string
	 * @throws Error
	 */
	public function encode(array $data):string
	{
	    $iv = openssl_random_pseudo_bytes($this->length);
	    $encoded = openssl_encrypt(serialize($data), $this->cipher, $this->key, OPENSSL_RAW_DATA , $iv) or
            throw new Error('Unable to encrypt data');

        // Hiding iv between two heaps of bytes 	        
	    $hideIv = openssl_random_pseudo_bytes(self::FIRST_SALT).$iv.openssl_random_pseudo_bytes(self::SECONT_SALT);
	    	    
		$result = base64_encode($hideIv.$encoded) or
			throw new Error('Unable to encode data');

		return $result;
	}
	
	/**
	 * Decode from base64 string
	 *
	 * @param string $data
	 * @return array
	 * @throws Error
	 */
	public function decode(string $data):array
	{	    
	    $data  = base64_decode($data);
	    $iv    = substr($data, self::FIRST_SALT, $this->length); 
	    $data  = substr($data, self::FIRST_SALT + $this->length + self::SECONT_SALT);
	    
	    $result= openssl_decrypt($data, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv) or
			throw new Error('Unable to decrypt data');
			
		return unserialize(trim($result));
	}
}
