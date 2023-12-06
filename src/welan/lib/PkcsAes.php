<?php
namespace WeLan\Lib;

use Exception;

class PkcsAes
{
    const BLOCK_SIZE = 16;  // PKCS5

    /**
     * 加密函数
     * @param $text
     * @param $aesKey
     * @param string $iv
     * @return string array|string
     * @throws Exception
     */
    public static function encrypt($text, $aesKey, $iv = '')
    {
        try {
            $iv = empty($iv) ? $aesKey : $iv;
            $text = self::AesPKCSEncode($text);
            $encrypt = openssl_encrypt($text, 'AES-128-CBC', $aesKey, OPENSSL_ZERO_PADDING, $iv);
            return base64_encode($encrypt);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 解密函数
     * @param $encrypt
     * @param $aesKey
     * @param string $iv
     * @return string array|string
     * @throws Exception
     */
    public static function decrypt($encrypt, $aesKey, $iv = '')
    {
        try {
            $iv = empty($iv) ? $aesKey : $iv;
            $encrypt = base64_decode($encrypt);
            $decrypt = openssl_decrypt($encrypt, 'AES-128-CBC', $aesKey, OPENSSL_ZERO_PADDING, $iv);
            return self::AesPKCSDecode($decrypt);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * PKCS编码
     * @param $text
     * @return string
     */
    public static function AesPKCSEncode($text)
    {
        $amountToPad = self::BLOCK_SIZE - (strlen($text) % self::BLOCK_SIZE);
        if ($amountToPad == 0) {
            $amountToPad = self::BLOCK_SIZE;
        }
        $padChr = chr($amountToPad);
        $tmp = str_repeat($padChr, $amountToPad);
        return $text . $tmp;
    }

    /**
     * PKCS解码
     * @param $text
     * @return string
     */
    public static function AesPKCSDecode($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > self::BLOCK_SIZE) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }
}