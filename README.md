Captcha
=======

This is a PHP >= 7.0 captcha implementation.

Example of use
--------------

app/Providers/AppServiceProvider.php:
```php
use Captcha\Captcha;

public function register()
{
    $this->app->singleton('Captcha\\Captcha', function($app) {
        return new Captcha('1234567890123456');
    });
}
```

routes/web.php:
```php
<?php

Route::get('/', function () {
    return view('main');
});

Route::get('/captcha', function () {
    $lang = request()->input('lang', 'en');
    $length = request()->input('length', 5);

    $id = rand(1000, 100000);
    $c = app('Captcha\\Captcha')->make($lang, $length);
    return view('captcha', compact('id', 'c', 'lang', 'length'));
});

Route::get('/captcha/update', function () {
    $lang = request()->input('lang', 'en');
    $length = request()->input('length', 5);

    $c = app('Captcha\\Captcha')->make($lang, $length);
    $data = [
        'captcha' => $c,
        'image' => '/captcha/image?c=' . $c,
        'audio' => '/captcha/audio?c=' . $c,
    ];
    return json_encode($data);
});

Route::get('/captcha/image', function () {
    $c = request()->input('c', null);
    app('Captcha\\Captcha')->sendImage($c);
});

Route::get('/captcha/audio', function () {
    $c = request()->input('c', null);
    app('Captcha\\Captcha')->sendAudio($c);
});

Route::get('/captcha/check', function () {
    $c = request()->input('c', null);
    $a = request()->input('a', null);

    if (!$c || !$a) return json_encode(['result' => false]);

    return json_encode(['result' => app('Captcha\\Captcha')->verify($c, $a)]);
});
```
resources/views/captcha.blade.php:
```html
<style>
    .captcha .image {
        cursor: pointer;
    }

    .captcha .play {
        font-size: x-large;
        cursor: pointer;
    }
</style>

<div class="captcha" id="captcha{{$id}}" data="{{$c}}">
    <img class="image" src="/captcha/image?c={{$c}}"/>
    <i class="play">&#9836;</i><br/>
    <audio class="audio" src="/captcha/audio?c={{$c}}" preload="none"></audio>

    <input class="answer" type="text"/>
    <input class="check" type="button" value="Check">
    <div class="result">---</div>

    <script>
        'use strict';

        $(document).ready(function() {
            var $captcha = $('#captcha{{$id}}');

            $captcha.find('.check').click(function(){
                var c = $captcha.attr('data');
                var a = $captcha.find('.answer').val();

                $.get('/captcha/check', {c:c, a:a}, function(data) {
                    $captcha.find('.result').html(JSON.parse(data).result ? 'Yes':'No');
                });
            });

            $captcha.find('.play').click(function(){
                $captcha.find('.audio')[0].play();
            });

            $captcha.find('.image').click(function(){
                $.get('/captcha/update', {lang:'{{$lang}}',length:'{{$length}}'}, function(data) {
                    data = JSON.parse(data);
                    $captcha.attr('data', data.captcha);
                    $captcha.find('.image').attr('src', data.image);
                    $captcha.find('.audio').attr('src', data.audio);
                });
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

    composer require "osh88/captcha:*@dev"

License
-------
https://opensource.org/licenses/MIT