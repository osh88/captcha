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
    return view('captcha', compact('id', 'c'));
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

    if (!$c || !$a) return 'false';

    return app('Captcha\\Captcha')->verify($c, $a) ? 'true':'false';
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

    composer require "osh88/captcha:*@dev"

License
-------
https://opensource.org/licenses/MIT