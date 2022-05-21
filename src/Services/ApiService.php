<?php


namespace SL\WebsiteBundle\Services;


use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;

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
        if (isset(self::$client)) {
            return self::$client;
        }
        $host = $this->apiUrl;
        $client = new Client(array(
            'timeout' => 5,
            'base_uri' => $_ENV['SL_API'],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Referer' => $host
            ],
            '',
            'verify' => false
        ));
    }


}