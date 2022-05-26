<?php


namespace SL\WebsiteBundle\Services;


use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Message;

class ApiService
{
    private $em;
    private $apiUrl;
    private $apiClient;
    private $apiKey;
    public static $client;

    public function __construct(EntityManagerInterface $em, $apiUrl, $apiClient, $apiKey)
    {
        $this->em = $em;
        $this->apiUrl = $apiUrl;
        $this->apiClient = $apiClient;
        $this->apiKey = $apiKey;
    }

    function getClient() {
        if (!isset(self::$client)) {
            self::$client = new Client(array(
                'timeout' => 5,
                'base_uri' => $this->apiUrl,
                'headers' => [
                    'uloc-mi' => $this->apiClient,
                    'X-AUTH-TOKEN' => $this->apiKey
                ],
                '',
                'verify' => false
            ));
        }
        return self::$client;
    }

    public function requestToken($username, $password) {
        try {
            $response = $this->getClient()->request('POST', '/api/auth', [
                'form_params' => [
                    'user' => $username,
                    'pass' => $password
                ]
            ]);
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody(), true);
            if (isset($body['detail'])) {
                throw new \Exception($body['detail']);
            }
            throw new \Exception((string)$body);
        } catch (\Throwable $exception) {
            throw $exception;
        }
        return json_decode($response->getBody(), true);
    }

    public function requestAllActiveSiteData () {
        return $this->getClient()->request('POST', '/api/public/site/all-data', [
        ]);
    }


}