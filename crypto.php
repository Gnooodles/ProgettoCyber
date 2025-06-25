<?php

function getEncryptionKey(): string {
    // Chiave a 256 bit (32 byte)
    return hash('sha256', 'uB8yFz7pQx2T#94k&dLwA1Vh6ZsNm!eP', true);
}

function encryptData(string $plaintext): string {
    $key = getEncryptionKey();
    $iv = random_bytes(16); // AES-256-CBC richiede 16 byte di IV
    $ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    if ($ciphertext === false) {
        return '';
    }
    return base64_encode($iv . $ciphertext);
}

function decryptData(string $encodedData): string|false {
    $data = base64_decode($encodedData, true);
    if ($data === false || strlen($data) <= 16) {
        return false;
    }
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);
    $key = getEncryptionKey();
    return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

?>