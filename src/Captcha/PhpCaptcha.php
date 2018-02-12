<?php
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

namespace Captcha;

class PhpCaptcha {
   var $oImage;
   var $aFonts;
   var $iWidth;
   var $iHeight;
   var $iNumChars;
   var $iNumLines;
   var $iSpacing;
   var $bCharShadow;
   var $bCaseInsensitive;
   var $vBackgroundImages;
   var $iMinFontSize;
   var $iMaxFontSize;
   var $bUseColour;

   function __construct (
      $aFonts, // array of TrueType fonts to use - specify full path
      $iWidth = 200, // width of image, max 500
      $iHeight = 50 // height of image, max 200
   ) {
      // get parameters
      $this->aFonts = $aFonts;
      $this->SetNumChars(5);
      $this->SetNumLines(70);
      $this->DisplayShadow(false);
      $this->CaseInsensitive(true);
      $this->SetBackgroundImages('');
      $this->SetMinFontSize(16);
      $this->SetMaxFontSize(25);
      $this->UseColour(false);
      $this->SetWidth($iWidth);
      $this->SetHeight($iHeight);
   }

   function CalculateSpacing() {
      $this->iSpacing = (int)($this->iWidth / $this->iNumChars);
   }

   function SetWidth($iWidth) {
      $this->iWidth = $iWidth;
      if ($this->iWidth > 500) $this->iWidth = 500; // to prevent perfomance impact
      $this->CalculateSpacing();
   }

   function SetHeight($iHeight) {
      $this->iHeight = $iHeight;
      if ($this->iHeight > 200) $this->iHeight = 200; // to prevent performance impact
   }

   function SetNumChars($iNumChars) {
      $this->iNumChars = $iNumChars;
      $this->CalculateSpacing();
   }

   function SetNumLines($iNumLines) {
      $this->iNumLines = $iNumLines;
   }

   function DisplayShadow($bCharShadow) {
      $this->bCharShadow = $bCharShadow;
   }

   function CaseInsensitive($bCaseInsensitive) {
      $this->bCaseInsensitive = $bCaseInsensitive;
   }

   function SetBackgroundImages($vBackgroundImages) {
      $this->vBackgroundImages = $vBackgroundImages;
   }

   function SetMinFontSize($iMinFontSize) {
      $this->iMinFontSize = $iMinFontSize;
   }

   function SetMaxFontSize($iMaxFontSize) {
      $this->iMaxFontSize = $iMaxFontSize;
   }

   function UseColour($bUseColour) {
      $this->bUseColour = $bUseColour;
   }

   function DrawLines() {
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

   function DrawCharacters($sCode) {
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

   function Create($sCode) {
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
