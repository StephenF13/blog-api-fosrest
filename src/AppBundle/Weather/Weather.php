<?php

namespace AppBundle\Weather;

use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use Psr\Log\LoggerInterface;

class Weather
{
    private $weatherClient;
    private $serializer;
    private $apiKey;
    private $logger;

    public function __construct(Client $weatherClient, Serializer $serializer, $apiKey, LoggerInterface $logger)
    {
        $this->weatherClient = $weatherClient;
        $this->serializer = $serializer;
        $this->apiKey = $apiKey;
    }

    public function getCurrent()
    {
        $uri = '/data/2.5/weather?q=Grenoble&APPID=' . $this->apiKey;

        try {
            $response = $this->weatherClient->get($uri);
        } catch (\Exception $e) {
            // Logger l'erreur.
            $this->logger->error('The weather API returned an error: '.$e->getMessage());

            return ['error' => 'Les informations ne sont pas disponibles pour le moment.'];
        }


        $data = $this->serializer->deserialize($response->getBody()->getContents(), 'array', 'json');

        return [
            'city'        => $data['name'],
            'description' => $data['weather'][0]['main'],
        ];
    }
}