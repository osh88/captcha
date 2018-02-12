<?php

namespace Captcha;

use Captcha\Encrypter;

class Captcha {
    private $chars = [
        'ru' => 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя',
        'en' => 'abcdefghijklmnopqrstuvwxyz',
        'num' => '0123456789',
    ];

    // key for encrypt captcha
    private $key;

    // default cipher
    private $cipher = 'AES-128-CBC';

    // encrypter instance
    private $encrypter = null;

    public function __construct() {
        // set defaults for Encrypter
        $this->setKey(Encrypter::generateKey($this->cipher));

        // set defaults for PhpCaptcha
        $this->SetFonts([ dirname(__FILE__).'/fonts/moster.ttf' ]);
        $this->SetNumChars(5);
        $this->SetWidth(200); // 200, max 500
        $this->SetHeight(50); // 50, max 200
    }

    /**
     * Create new encrypted captcha
     * @return mixed
     */
    public function make($lang = 'en', $length = 5) {
        if (!in_array($lang, ['ru','en'])) {
            $lang = 'en';
        };

        $length = (int)$length;
        if ($length < 1 || 10 < $length) {
            $length = 5;
        };

        $chars  = $this->chars[$lang];
        $chars .= $this->chars['num'];

        $result = '';
        for($i=0; $i<$length; $i++) {
            $result .= mb_substr($chars, rand(0, mb_strlen($chars)-1), 1);
        }

        return $this->encrypter->encryptString($result);
    }

    /**
     * Get captcha plain text
     * @param string $captcha encrypted captcha
     * @return mixed
     */
    public function decryptCaptcha($captcha) {
        return $this->encrypter->decryptString($captcha);
    }

    /**
     * Send image file for captcha
     * @param string $captcha base64 encoded captcha data
     */
    public function sendImage($captcha) {
        $c = $this->decryptCaptcha($captcha);
        $this->Create($c);
    }

    /**
     * Send audio file for captcha
     * @param string $captcha base64 encoded captcha data
     */
    public function sendAudio($captcha) {
        $c = $this->decryptCaptcha($captcha);
        $voice = dirname(__FILE__).'/voice/';

        header('Content-type: audio/mp3');
        for($i=0; $i<mb_strlen($c); $i++) {
            echo file_get_contents($voice . mb_substr($c, $i, 1) . '.mp3');
        }
    }

    /**
     * Check captcha
     * @param string $captcha base64 encoded captcha data
     * @param string $answer plain text (plain captcha)
     * @return bool
     */
    public function verify($captcha, $answer) {
        return $this->decryptCaptcha($captcha) === mb_strtolower($answer);
    }

    /**
     * @param $key
     * @return $this
     */
    public function setKey($key) {
        if ($key !== $this->key && Encrypter::supported($key, $this->cipher)) {
            $this->key = $key;
            $this->encrypter = new Encrypter($this->key);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getKey() {
        return $this->key;
    }

    /***************************************************************/
    /* PhpCaptcha - A visual and audio CAPTCHA generation library

       Software License Agreement (BSD License)

       Copyright (C) 2005-2006, Edward Eliot.
       All rights reserved.

       Redistribution and use in source and binary forms, with or without
       modification, are permitted provided that the following conditions are met:

          * Redistributions of source code must retain the above copyright
            notice, this list of conditions and the following disclaimer.
          * Redistributions in binary form must reproduce the above copyright
            notice, this list of conditions and the following disclaimer in the
            documentation and/or other materials provided with the distribution.
          * Neither the name of Edward Eliot nor the names of its contributors
            may be used to endorse or promote products derived from this software
            without specific prior written permission of Edward Eliot.

       THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS" AND ANY
       EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
       WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
       DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
       DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
       (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
       LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
       ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
       (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
       SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

       Last Updated:  18th April 2006                               */
    /***************************************************************/

    private $oImage;
    private $aFonts; // array of TrueType fonts to use - specify full path
    private $iWidth;
    private $iHeight;
    private $iNumChars;
    private $iNumLines = 70;
    private $iSpacing;
    private $bCharShadow = false;
    private $vBackgroundImages = '';
    private $iMinFontSize = 16;
    private $iMaxFontSize = 25;
    private $bUseColour = false;

    public function SetFonts($fonts) {
        $this->aFonts = $fonts;
        return $this;
    }

    private function CalculateSpacing() {
        $this->iSpacing = (int)($this->iWidth / $this->iNumChars);
    }

    public function SetWidth($iWidth) {
        $this->iWidth = $iWidth;
        if ($this->iWidth > 500) $this->iWidth = 500; // to prevent perfomance impact
        $this->CalculateSpacing();
        return $this;
    }

    public function SetHeight($iHeight) {
        $this->iHeight = $iHeight;
        if ($this->iHeight > 200) $this->iHeight = 200; // to prevent performance impact
        return $this;
    }

    public function SetNumChars($iNumChars) {
        $this->iNumChars = $iNumChars;
        $this->CalculateSpacing();
        return $this;
    }

    public function SetNumLines($iNumLines) {
        $this->iNumLines = $iNumLines;
        return $this;
    }

    public function DisplayShadow($bCharShadow) {
        $this->bCharShadow = $bCharShadow;
        return $this;
    }

    public function SetBackgroundImages($vBackgroundImages) {
        $this->vBackgroundImages = $vBackgroundImages;
        return $this;
    }

    public function SetMinFontSize($iMinFontSize) {
        $this->iMinFontSize = $iMinFontSize;
        return $this;
    }

    public function SetMaxFontSize($iMaxFontSize) {
        $this->iMaxFontSize = $iMaxFontSize;
        return $this;
    }

    public function UseColour($bUseColour) {
        $this->bUseColour = $bUseColour;
        return $this;
    }

    private function DrawLines() {
        for ($i = 0; $i < $this->iNumLines; $i++) {
            // allocate colour
            if ($this->bUseColour) {
                $iLineColour = imagecolorallocate($this->oImage, rand(100, 250), rand(100, 250), rand(100, 250));
            } else {
                $iRandColour = rand(100, 250);
                $iLineColour = imagecolorallocate($this->oImage, $iRandColour, $iRandColour, $iRandColour);
            }

            // draw line
            imageline($this->oImage, rand(0, $this->iWidth), rand(0, $this->iHeight), rand(0, $this->iWidth), rand(0, $this->iHeight), $iLineColour);
        }
    }

    private function DrawCharacters($sCode) {
        $this->SetNumChars(mb_strlen($sCode));

        // loop through and write out selected number of characters
        for ($i = 0; $i < mb_strlen($sCode); $i++) {
            // select random font
            $sCurrentFont = $this->aFonts[array_rand($this->aFonts)];

            // select random colour
            if ($this->bUseColour) {
                $iTextColour = imagecolorallocate($this->oImage, rand(0, 100), rand(0, 100), rand(0, 100));

                if ($this->bCharShadow) {
                    // shadow colour
                    $iShadowColour = imagecolorallocate($this->oImage, rand(0, 100), rand(0, 100), rand(0, 100));
                }
            } else {
                $iRandColour = rand(0, 100);
                $iTextColour = imagecolorallocate($this->oImage, $iRandColour, $iRandColour, $iRandColour);

                if ($this->bCharShadow) {
                    // shadow colour
                    $iRandColour = rand(0, 100);
                    $iShadowColour = imagecolorallocate($this->oImage, $iRandColour, $iRandColour, $iRandColour);
                }
            }

            // select random font size
            $iFontSize = rand($this->iMinFontSize, $this->iMaxFontSize);

            // select random angle
            $iAngle = rand(-30, 30);

            //select cyrillic char
            $char = mb_strtoupper( mb_substr($sCode,$i,1) );

            // get dimensions of character in selected font and text size
            $aCharDetails = imagettfbbox($iFontSize, $iAngle, $sCurrentFont, $char);

            // calculate character starting coordinates
            $iX = $this->iSpacing / 4 + $i * $this->iSpacing;
            $iCharHeight = $aCharDetails[2] - $aCharDetails[5];
            $iY = $this->iHeight / 2 + $iCharHeight / 4;

            // write text to image
            imagettftext($this->oImage, $iFontSize, $iAngle, $iX, $iY, $iTextColour, $sCurrentFont, $char);

            if ($this->bCharShadow) {
                $iOffsetAngle = rand(-30, 30);

                $iRandOffsetX = rand(-5, 5);
                $iRandOffsetY = rand(-5, 5);

                imagettftext($this->oImage, $iFontSize, $iOffsetAngle, $iX + $iRandOffsetX, $iY + $iRandOffsetY, $iShadowColour, $sCurrentFont, $char);
            }
        }
    }

    private function Create($sCode) {
        // check for required gd functions
        if (!function_exists('imagecreate') || !function_exists("imagejpeg") || ($this->vBackgroundImages != '' && !function_exists('imagecreatetruecolor'))) {
            return false;
        }
        // get background image if specified and copy to CAPTCHA
        if (is_array($this->vBackgroundImages) || $this->vBackgroundImages != '') {
            // create new image
            $this->oImage = imagecreatetruecolor($this->iWidth, $this->iHeight);

            // create background image
            if (is_array($this->vBackgroundImages)) {
                $iRandImage = array_rand($this->vBackgroundImages);
                $oBackgroundImage = imagecreatefromjpeg($this->vBackgroundImages[$iRandImage]);
            } else {
                $oBackgroundImage = imagecreatefromjpeg($this->vBackgroundImages);
            }

            // copy background image
            imagecopy($this->oImage, $oBackgroundImage, 0, 0, 0, 0, $this->iWidth, $this->iHeight);

            // free memory used to create background image
            imagedestroy($oBackgroundImage);
        } else {
            // create new image
            $this->oImage = imagecreate($this->iWidth, $this->iHeight);
        }

        // allocate white background colour
        imagecolorallocate($this->oImage, 255, 255, 255);

        // check for background image before drawing lines
        if (!is_array($this->vBackgroundImages) && $this->vBackgroundImages == '') {
            $this->DrawLines();
        }

        $this->DrawCharacters($sCode);

        // write out image to browser
        header("Content-type: image/jpeg");
        imagejpeg($this->oImage);

        // free memory used in creating image
        imagedestroy($this->oImage);

        return true;
    }
}