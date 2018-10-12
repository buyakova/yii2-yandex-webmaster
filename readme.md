# Класс для работы с api webmaster.yandex.ru

composer require klisl/yii2-mytest

## Возможности

1. Добавление сайта в вебмастер
2. Получение кода верификации
3. Подтверждение прав на сайт
4. Добавление карты сайта

## Зависимости

1. Yii2
4. Http-client (included with Yii)

## Установка:
Можно установить пакет с помощью [composer](https://getcomposer.org/).

Выполнив команду

`php composer.phar require --prefer-dist buyakova/yii2-yandex-webmaster "*"`

или добавив

`"buyakova/yii2-yandex-webmaster": "*"`

в ваш `composer.json`.

## Пример использования
 
```php
public function addWebmaster($model)
{
    $token = Yii::$app->params['yandex_webmaster_token'];
    $url = 'http://test.website.com';
    
    $webmaster = new YandexWebmaster($token);
    $host_id = $webmaster->addWebsite($url);
    $uin = $webmaster->getVerify($host_id);
    
    $model->yandex_webmaster_id = $host_id;
    $model->yandex_webmaster_uin = $uin;
    if ($model->save()) {
        return $webmaster->sendVerify($host_id);
    }
    return false;
}
```
