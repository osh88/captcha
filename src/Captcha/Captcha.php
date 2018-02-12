<?php

namespace Captcha;

use Captcha\Encrypter;
use Captcha\PhpCaptcha;

class Captcha {
    private $chars = [
        'ru' => 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя',
        'en' => 'abcdefghijklmnopqrstuvwxyz',
        'num' => '0123456789',
    ];

    private $key = '1234567890123456';
    private $encoder = null;

    private static $instance = null;

    private function __construct() {
        $this->encoder = new Encrypter($this->key);
    }

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function newCaptcha($lang = 'en', $length = 5) {
        $lang = (string)$lang;
        if (!in_array($lang, ['ru','en'])) {
            $lang = 'en';
        };

        $length = (int)$length;
        if ($length < 1 || $length > 10) {
            $length = 5;
        };

        $chars  = $this->chars[$lang];
        $chars .= $this->chars['num'];

        $result = '';
        for($i=0; $i<$length; $i++) {
            $result .= mb_substr($chars, rand(0, mb_strlen($chars)-1), 1);
        }

        return $this->encoder->encryptString($result);
    }

    public function decryptCaptcha($data) {
        return $this->encoder->decryptString($data);
    }

    public function getImage($data) {
        $c = $this->decryptCaptcha($data);
        $fontsDir = dirname(__FILE__).'/fonts/';
        $fonts = [ $fontsDir.'moster.ttf'];
        $phpCaptcha = new PhpCaptcha($fonts,200,60);
        $phpCaptcha->Create($c);
    }

    public function getAudio($data) {
        $c = $this->decryptCaptcha($data);
        $voice = dirname(__FILE__).'/voice/';

        header('Content-type: audio/mp3');
        for($i=0; $i<mb_strlen($c); $i++) {
            echo file_get_contents($voice . mb_substr($c, $i, 1) . '.mp3');
        }
    }

}