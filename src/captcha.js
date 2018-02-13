'use strict';

(function ( $ ) {
    $.fn.captcha = function(lang, length) {

        var $captcha = this;
        var $image = $captcha.find('img.captcha-image');
        var $audio = $captcha.find('audio.captcha-audio');
        var $play  = $captcha.find('.captcha-play');

        $captcha.append('<input type="hidden" class="captcha-data" name="captcha-data">');
        var $data  = $captcha.find('.captcha-data');

        var update = function() {
            $.get('/captcha', {lang:lang,length:length}, function(encCaptcha) {
                $image.attr('src', '/captcha/image?c='+encCaptcha);
                $audio.attr('src', '/captcha/audio?c='+encCaptcha);
                $data.val(encCaptcha);
            });
        };

        $play.click(function(){
            $audio[0].play();
        });

        $image.click(update);

        update();

        // set styles
        $image.css('cursor', 'pointer');
        $play.css('cursor', 'pointer');

        return this;
    };

}( jQuery ));