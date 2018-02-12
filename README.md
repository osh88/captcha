Captcha
=======

This is a PHP >= 5.4 captcha implementation.

Example of use
--------------

routes/web.php:
```php
<?php

use Captcha\Captcha;

Route::get('/', function () {
    return view('main');
});

Route::get('/captcha', function () {
    $lang = request()->input('lang', 'en');
    $length = request()->input('length', 5);

    $id = rand(1000, 100000);
    $c = Captcha::instance()->newCaptcha($lang, $length);
    return view('captcha', compact('id', 'c'));
});

Route::get('/captcha/image', function () {
    $c = request()->input('c', null);
    if (!$c) return;
    Captcha::instance()->getImage($c);
});

Route::get('/captcha/audio', function () {
    $c = request()->input('c', null);
    if (!$c) return;
    Captcha::instance()->getAudio($c);
});

Route::get('/captcha/check', function () {
    $c = request()->input('c', null);
    $a = request()->input('a', null);

    if (!$c || !$a) return 'false';
    $result = Captcha::instance()->decryptCaptcha($c) === mb_strtolower($a);

    return $result ? 'true':'false';
});
```
resources/views/captcha.blade.php:
```php
<div id="captcha{{$id}}">
    <img id="image" src="/captcha/image?c={{$c}}"/>
    <i style="font-size: x-large; cursor: pointer;" id="play">&#9836;</i><br/>
    <audio id="audio" src="/captcha/audio?c={{$c}}" preload="none"></audio>

    <input type="text" id="answer"/>
    <input type="button" value="Check" id="check">
    <div id="result">---</div>

    <script>
        $(document).ready(function(){
            $('#captcha{{$id}}').find('#check').click(function(){
                var answer = $('#captcha{{$id}}').find('#answer').val();
                $.get('/captcha/check', {'c':'{{$c}}', 'a': answer}, function(data) {
                    $('#captcha{{$id}}').find('#result').html(data);
                });
            });

            $('#captcha{{$id}}').find('#play').click(function(){
                $('#captcha{{$id}}').find('#audio')[0].play();
            });
        });
    </script>
</div>
```

resources/views/main.blade.php:
```php
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <script src="http://code.jquery.com/jquery-1.12.4.min.js"></script>
    </head>
    <body>
        <div id="c1"></div>
        <div id="c2"></div>

    <script>
        $(document).ready(function(){
            $.get('/captcha', {lang:'ru', length: 5}, function(data) { $('#c1').html(data); });
            $.get('/captcha', {}, function(data) { $('#c2').html(data); });
        });
    </script>
    </body>
</html>
```

Installation
------------

The preferred installation method is [composer](https://getcomposer.org):

    php composer.phar require osh88/captcha

License
-------
https://opensource.org/licenses/MIT