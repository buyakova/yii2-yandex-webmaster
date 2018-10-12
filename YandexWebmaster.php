<?php

namespace buyakova\webmaster;

use Yii;
use yii\helpers\Json;
use yii\httpclient\Client;

/**
 * Запросы к API Яндекс.Вебмастер
 * @package app\components
 */
class YandexWebmaster
{

    /**
     * базовый адрес для запросов
     */
    const url = 'https://api.webmaster.yandex.net/v3';

    private $userId = null;
    private $token = '';

    function __construct($token) {
        $this->token = $token;
        $this->setUserId($token);
    }
    /**
     * Получение идентификатора пользователя
     * https://tech.yandex.ru/webmaster/doc/dg/reference/hosts-add-site-docpage/
     * @param $token
     *
     * @return string
     */
    public function setUserId($token)
    {
        $client = new Client();

        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('get')
            ->setUrl(self::url . '/user/')
            ->setHeaders([
                'Authorization' => 'OAuth ' . $token
            ])
            ->send();
        if ($response->headers['http-code'] == 200) {
            $content = Json::decode($response->content);
            $this->userId = $content['user_id'];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Добавление сайта
     * https://tech.yandex.ru/webmaster/doc/dg/reference/hosts-add-site-docpage/
     * @param $url
     *
     * @return string
     */
    public function addWebsite($url)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('post')
            ->setUrl(self::url . '/user/' . $this->userId . '/hosts/')
            ->setHeaders([
                'Authorization' => 'OAuth ' . $this->token
            ])
            ->setData(array(
                'host_url' => $url
            ))
            ->send();
        if ($response->headers['http-code'] == 201) {
            return Json::decode($response->content)['host_id'];
        } else {
            return false;
        }

    }

    /**
     * Получение информации о подтверждении сайта
     * https://tech.yandex.ru/webmaster/doc/dg/concepts/verification-docpage/
     * @param $host_id
     *
     * @return mixed
     */
    public function getVerify($host_id)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('get')
            ->setUrl(self::url . '/user/'.$this->userId.'/hosts/'.$host_id.'/verification/')
            ->setHeaders([
                'Authorization' => 'OAuth ' . $this->token,
            ])
            ->send();
        if ($response->headers['http-code'] == 200) {
            return Json::decode($response->content)['verification_uin'];
        } else {
            return $response->data['message'];
        }
    }

    /**
     * Запрос проверки подтверждения сайта
     * https://tech.yandex.ru/webmaster/doc/dg/reference/host-verification-post-docpage/
     * @param $host_id
     * @param string $type
     *
     * @return mixed
     */
    public function sendVerify($host_id, $type = 'META_TAG')
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('post')
            ->setUrl(self::url . '/user/'.$this->userId.'/hosts/'.$host_id.'/verification/?verification_type='.$type)
            ->setHeaders([
                'Authorization' => 'OAuth ' . $this->token,
                'Content-Type'  => 'application/x-www-form-urlencoded'
            ])
            ->send();
        if ($response->headers['http-code'] == 200) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Добавление карты сайта
     * https://tech.yandex.ru/webmaster/doc/dg/reference/host-user-added-sitemaps-post-docpage/
     * @param $url
     * @param $host_id
     *
     * @return bool
     */
    public function addSitemap($url, $host_id)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setMethod('post')
            ->setUrl(self::url . '/user/'.$this->userId.'/hosts/'.$host_id.'/user-added-sitemaps/')
            ->setHeaders([
                'Authorization' => 'OAuth ' . $this->token
            ])
            ->setData(array(
                'url' => $url
            ))
            ->send();
        if ($response->headers['http-code'] == 201) {
            return true;
        } else {
            return false;
        }
    }

}