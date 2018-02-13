'use strict';

(function ( $ ) {
    $.fn.captcha = function(lang, length, captchaDataName) {

        var $captcha = this;
        var $image = $captcha.find('img.captcha-image');
        var $audio = $captcha.find('audio.captcha-audio');
        var $play  = $captcha.find('.captcha-play');

        if (captchaDataName == undefined || captchaDataName == '') {
            captchaDataName = 'captcha_data';
        }

        $captcha.append('<input type="hidden" class="captcha-data" name="'+captchaDataName+'">');
        var $data  = $captcha.find('.captcha-data');

        var update = function() {
            $.get('/captcha', {lang:lang,length:length}, function(r) {
                r = JSON.parse(r);

                $image.attr('src', r.image);
                $audio.attr('src', '/captcha/audio?c=' + r.data);
                $data.val(r.data);
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