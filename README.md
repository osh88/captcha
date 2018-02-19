Captcha
=======

    Ахтунг. капча не работает! похоже что это полная хрень..
    Не реализовано одноразовое использование.

This is a PHP >= 5.4 captcha implementation.

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
    return json_encode(app('Captcha\\Captcha')->make($lang, $length));
});

Route::get('/captcha/audio', function () {
    $c = request()->input('c', null);
    app('Captcha\\Captcha')->sendAudio($c);
});

Route::get('/captcha.js', function () {
    return app('Captcha\\Captcha')->getCaptchaJS();
});
```
resources/views/main.blade.php:
```html
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <script src="http://code.jquery.com/jquery-1.12.4.min.js"></script>
        <script src="/captcha.js"></script>
    </head>
    <body>
        <div id="c1">
            <img class="captcha-image"/>
            <i class="captcha-play">&#9836;</i><br/>
            <audio class="captcha-audio"></audio>
            <input name="captcha" type="text"/>
        </div>

        <div id="c2">
            <img class="captcha-image"/>
            <i class="captcha-play">&#9836;</i><br/>
            <audio class="captcha-audio"></audio>
            <input name="captcha" type="text"/>
        </div>

    <script>
        $(document).ready(function(){
            $('#c1').captcha('ru','6');
            $('#c2').captcha('en', '4', 'poll[captcha_data]');
        });
    </script>
    </body>
</html>
```

Installation
------------

The preferred installation method is [composer](https://getcomposer.org):

    composer require "osh88/captcha:*@dev"

Git

	git clone https://github.com/osh88/captcha.git

    require '/vendor/captcha/src/Captcha/Captcha.php';
	require '/vendor/captcha/src/Captcha/Encrypter.php';


License
-------
https://opensource.org/licenses/MIT