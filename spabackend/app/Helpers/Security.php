<?php
use Spatie\Crypto\Rsa\KeyPair;
use Spatie\Crypto\Rsa\PublicKey;
use Spatie\Crypto\Rsa\PrivateKey;

if (!function_exists('hGenerateIntegrationKeys')) {
    
    function hGenerateIntegrationKeys($password='') {
        // $rsa = new \Crypt_RSA();
        // $rsa = new \Crypt_DES();
        // $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);
        // $rsa->setPrivateKeyFormat(CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
        // $rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_PKCS1);
        // extract($rsa->createKey());
        

        // $rsa = new Crypt_RSA();
        // extract($rsa->createKey());
        // return [$privatekey, $publickey];

        //Spatie Ctypto 
        try {
            [$private_key, $public_key] = (new KeyPair())->generate();
            return [$private_key, $public_key];
        } catch (\Throwable $th) {
            return null;
        }        
    }

}

if (!function_exists('hCryptoEncryptByPub')) {

    function hCryptoEncryptByPub($str_public_key, $plain_data) {
        try {
            $public_key = PublicKey::fromString($str_public_key);
            return base64_encode($public_key->encrypt($plain_data));
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
        
    }

}

if (!function_exists('hCryptoEncryptByPri')) {

    function hCryptoEncryptByPri($str_private_key, $plain_data) {
        $private_key = PrivateKey::fromString($str_private_key);
        return base64_encode($private_key->encrypt($plain_data));
    }

}

if (!function_exists('hCryptoDecryptByPri')) {

    function hCryptoDecryptByPri($str_private_key, $encrypted_data) {
        $private_key = PrivateKey::fromString($str_private_key);
        return $private_key->decrypt($encrypted_data);
    }

}

if (!function_exists('hCryptoCanDecryptByPri')) {

    function hCryptoCanDecryptByPri($str_private_key, $encrypted_data) {
        $private_key = PrivateKey::fromString($str_private_key);
        return $private_key->canDecrypt($encrypted_data);
    }

}

if (!function_exists('hCryptoCanDecryptByPub')) {

    function hCryptoCanDecryptByPub($str_public_key, $encrypted_data) {
        $public_key = PrivateKey::fromString($str_public_key);
        return $public_key->canDecrypt($encrypted_data);
    }

}



