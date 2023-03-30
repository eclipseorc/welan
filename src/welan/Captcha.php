<?php
namespace WeLan\Lib;

/**
 * 图形验证码
 * Class Captcha
 * @package WeLan\Lib
 * author  oldtom
 * date    2023/3/30 17:45
 */
class Captcha
{
    // 验证码图片宽度
    protected $width = 90;
    // 验证码图片高度
    protected $height= 40;
    // 验证码字符集
    protected $element  = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9'];
    // 生成的验证码
    protected $string   = '';
    // 验证码过期时间
    protected $expire   = 300;

    /**
     * 生成图片验证码
     * author   oldtom
     * date     2023/3/30 17:42
     */
    public function captcha()
    {
        header('Content-type:image/jpeg');
        for ($i=0; $i<5; $i++) {
            $rand = rand(0, count($this->element) - 1);
            $this->string .= $this->element[$rand];
        }
        // 保存验证码
        $sessionCode    = [];
        $sessionCode['verify_code'] = strtolower($this->string);
        $sessionCode['verify_time'] = time();
        session('captcha', $sessionCode);
        // 创建图像
        $img        = imagecreatetruecolor($this->width, $this->height);
        // 设置图形验证码背景色
        $bgColor    = imagecolorallocate($img, rand(200, 255), rand(200, 255), rand(200, 255));
        // 设置验证码字体颜色
        $strColor   = imagecolorallocate($img, rand(10, 50), rand(10, 50), rand(10, 50));
        // 给图片填充背景色
        imagefill($img, 0, 0, $bgColor);
        // 设置图像随机线条
        for ($i=0; $i<3; $i++) {
            imageline($img, rand(0, $this->width/2), rand(0, $this->height), rand($this->width/2, $this->width), rand(0, $this->height), imagecolorallocate($img, rand(100,200), rand(100, 200), rand(100, 200)));
        }
        //
        imagettftext($img, 20, rand(-5,5), rand(5,15), rand(30,35), $strColor, '/static/css/admin/font/orange-juice-2-0-1.ttf', $this->string);
        ob_start();
        // 输出图像
        imagejpeg($img);
        //释放资源
        imagedestroy($img);
    }

    /**
     * 校验验证码
     * @param $code
     * @return bool
     * author   oldtom
     * date     2023/3/30 17:43
     */
    public function checkVerify($code)
    {
        $sessionCode = session('captcha');
        session('captcha', null);
        if (empty($code) || empty($sessionCode)) {
            return false;
        }
        // 验证码过期
        if ((time() - $sessionCode['verify_time']) > $this->expire) {
            return false;
        }
        // 验证码是否正确
        if (strtolower($code) == $sessionCode['verify_code']) {
            return true;
        }
        return false;
    }
}