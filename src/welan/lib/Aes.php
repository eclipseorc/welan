<?php
namespace WeLan\Lib;

use Exception;

class Aes
{
    const BLOCK_SIZE = 16;  // aes分组加密按照字节加密，分组长度为128位，即16字节，aes加密不区分pkcs5和pkcs7

    private $cipher;

    public function __construct($cipher = 'AES-128-CBC')
    {
        $this->cipher = $cipher;
    }

    /**
     * 加密函数
     * @param $text
     * @param $aesKey
     * @param string $iv
     * @return string array|string
     * @throws Exception
     */
    public function encrypt($text, $aesKey, $iv = '')
    {
        try {
            $iv = empty($iv)? $aesKey : $iv;
            $text = $this->encode($text);
            $encrypt = openssl_encrypt($text, $this->cipher, $aesKey, OPENSSL_ZERO_PADDING, $iv);
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
    public function decrypt($encrypt, $aesKey, $iv = '')
    {
        try {
            $iv = empty($iv) ? $aesKey : $iv;
            $encrypt = base64_decode($encrypt);
            $decrypt = openssl_decrypt($encrypt, $this->cipher, $aesKey, OPENSSL_ZERO_PADDING, $iv);
            return $this->decode($decrypt);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * PKCS编码
     * @param $text
     * @return string
     */
    private function encode($text)
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
    private function decode($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > self::BLOCK_SIZE) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }
}