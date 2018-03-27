'use strict';

(function ( $ ) {
    $.fn.captcha = function(lang, length, captchaDataName) {

        var $captcha = this;
        var $image = $captcha.find('img.captcha-image');
        var $play  = $captcha.find('.captcha-play');

        if (captchaDataName == undefined || captchaDataName == '') {
            captchaDataName = 'captcha_data';
        }

        // Если повторно применили функцию на элементе
        $captcha.find('audio.captcha-audio').remove();
        $captcha.find('.captcha-data').remove(); // удаляем шифрованую капчу
        $play.off('click'); // убираем обработчики для аудикапчи
        $image.off('click'); // убираем обработчики для капчи

        // Добавляем тег для воспроизведения аудиокапчи
        $captcha.append('<audio class="captcha-audio" style="display: none;"></audio>');
        var $audio = $captcha.find('audio.captcha-audio');

        // Добавляем поле для хранения шифрованой капчи
        $captcha.append('<input type="hidden" class="captcha-data" name="'+captchaDataName+'">');
        var $data = $captcha.find('.captcha-data');

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