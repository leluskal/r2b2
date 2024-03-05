<?php

namespace App;

use GuzzleHttp\Client;

class RemoteAdsLoader
{
    private string $remoteUrl;

    public function __construct($remoteUrl) {
        $this->remoteUrl = $remoteUrl;
    }

    /**
     * @param string $uri
     * @return array
     */
    public function loadRemoteAds(string $uri): array
    {
        $client = new Client([
            'base_uri' => $this->remoteUrl,
            'timeout' => 5.0
        ]);

        $response = $client->request('GET', $uri);
        $responseBody = $response->getBody()->getContents();

        $lines = explode("\n", $responseBody);

        $validAds = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || $line[0] === '#') {
                continue;
            }

            $validAds[] = $line;
        }

        return $validAds;
    }
}
