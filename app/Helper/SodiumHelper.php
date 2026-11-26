<?php

namespace App\Helper;

class SodiumHelper
{
    public static function generateKey()
    {
        $keypair = sodium_crypto_box_keypair();
        $publicKey = base64_encode(sodium_crypto_box_publickey($keypair));
        $privateKey = base64_encode(sodium_crypto_box_secretkey($keypair));

        $signKeypair = sodium_crypto_sign_keypair();
        $signPublicKey = base64_encode(sodium_crypto_sign_publickey($signKeypair));
        $signPrivateKey = base64_encode(sodium_crypto_sign_secretkey($signKeypair));

        return (object) [
            'publicKey' => $publicKey,
            'privateKey' => $privateKey,
            'signPublicKey' => $signPublicKey,
            'signPrivateKey' => $signPrivateKey
        ];
    }

    /* ===================== */
    /* ENCRYPT / DECRYPT */
    /* ===================== */

    public static function encrypt(string $publicKeyTujuanBase64, string $privateKeyBase64, string $plainText): string
    {
        $publicKey = base64_decode($publicKeyTujuanBase64);
        $privateKey = base64_decode($privateKeyBase64);

        $nonce = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);

        $keyPair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
            $privateKey,
            $publicKey
        );

        $cipher = sodium_crypto_box(
            $plainText,
            $nonce,
            $keyPair
        );

        return base64_encode($nonce . $cipher);
    }

    public static function decrypt(string $publicKeyPengirimBase64, string $privateKeyBase64, string $payloadBase64): string
    {
        $publicKey = base64_decode($publicKeyPengirimBase64);
        $privateKey = base64_decode($privateKeyBase64);
        $data = base64_decode($payloadBase64);

        $nonce = substr($data, 0, SODIUM_CRYPTO_BOX_NONCEBYTES);
        $cipher = substr($data, SODIUM_CRYPTO_BOX_NONCEBYTES);

        $keyPair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
            $privateKey,
            $publicKey
        );

        return sodium_crypto_box_open(
            $cipher,
            $nonce,
            $keyPair
        );
    }

    /* ===================== */
    /* SIGN / VERIFY */

    /* ===================== */
    public static function sign(string $signPrivateKeyBase64, string $payloadBase64): string
    {
        $signPrivateKey = base64_decode($signPrivateKeyBase64);
        $payload = base64_decode($payloadBase64);

        return base64_encode(
            sodium_crypto_sign_detached($payload, $signPrivateKey)
        );
    }

    public static function verify(string $signPublicKeyPengirimBase64, string $payloadBase64, string $signatureBase64): bool
    {
        return sodium_crypto_sign_verify_detached(
            base64_decode($signatureBase64),
            base64_decode($payloadBase64),
            base64_decode($signPublicKeyPengirimBase64)
        );
    }
}
