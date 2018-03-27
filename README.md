Captcha
=======

This is a PHP >= 5.4 captcha implementation.

* Код капчи лежит в одном месте.
* Для капч не создаются никакие файлы на диске.
* Капча регистрируется в сессии.
* Для проверки капчи вызывается одна функция.
* Простое прикручивание капчи к интерфейсу.

Принцип работы:
С клиента идет AJAX-запрос новой капчи.
На сервере генерируется текст.
Текст новой капчи добавляется в реестр.
На основе текста создается изображение.
Текст шифруется.
На форму отправляется шифрованый текст капчи и изображение в embedded-формате.
Клиент добавляет полученую картинку на форму.
Если пользователь запускает воспроизведение аудиокапчи, на сервер идет запрос на получение аудиофайла,
    в качестве параметра передается шифрованая капча. На сервере капча расшифровывается, на основе ее
    текста склеиваются короткие аудиофайлы, содержащие озвучку букв и цифр. Полученый файл отправляется
    клиенту. С помощью тэга audio происходит воспроизведение.
При нажатии ЛКМ на картинке на сервер отправляется запрос новой капчи.

Example of use (laravel)
------------------------

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

// Отдает данные новой капчи
Route::get('/captcha', function () {
    $lang = request()->input('lang', 'en');
    $length = request()->input('length', 5);
    return json_encode(app('Captcha\\Captcha')->make($lang, $length));
});

// Отдает аудиофайл капчи
Route::get('/captcha/audio', function () {
    $c = request()->input('c', null);
    app('Captcha\\Captcha')->sendAudio($c);
});

// Отдает скрипт для работы с капчей
Route::get('/captcha.js', function () {
    return app('Captcha\\Captcha')->getCaptchaJS();
});

// Проверка работы капчи
Route::post('/submit', function () {
    $answer = request()->input('captcha', null); // answer
    $data = request()->input('cdata', null); // crypted captcha

    return app('Captcha\\Captcha')->verify($data, $answer) ? 'Valid':'Bad';
});
```
resources/views/main.blade.php:
```html
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="http://code.jquery.com/jquery-1.12.4.min.js"></script>

        {{-- Подключаем скрипт --}}
        <script src="/captcha.js"></script>
    </head>
    <body>

        {{-- Для работы капчи в форме достаточно двух объектов: --}}
        <form id="checkCaptcha">
            {{-- Тэг img с классом captcha-image - сюда будет выводиться картинка --}}
            <img class="captcha-image"/>

            {{-- Любой тэг с классом captcha-play - при нажатии на него, будет воспроизводиться аудиокапча --}}
            <i class="captcha-play" style="font-size: xx-large;">&#9836;</i><br/>

            {{-- Остальные поля могут быть какими угодно --}}
            <input name="captcha" type="text"/>
            <input type="button" value="check" class="check">
            <div class="result"></div>
        </form>

        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).ready(function(){
                var $form = $('#checkCaptcha');

                // Создаем новую капчу
                $form.captcha('en', '4', 'cdata');
                // $form.captcha(); // default params
                // $form.captcha('en', 5, 'captcha_data'); // default params

                // Отправляем форму на проверку
                $form.find('.check').click(function(){
                    $.post('/submit', $form.serialize(), function(result) {
                        $form.find('.result').html(result);
                    });
                });
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
