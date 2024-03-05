<?php

namespace App;

use Exception;

class AdsComparer
{
    private string $localFile;

    private string $remoteUrl;

    public function __construct($localFile, $remoteUrl) {
        $this->localFile = $localFile;
        $this->remoteUrl = $remoteUrl;
    }

    /**
     * @param string $uri
     * @return array
     * @throws Exception
     */
    public function getUniqueLocalLines(string $uri): array
    {
        $localLines = $this->getFileLines($this->localFile);

        $remoteAdsLoader = new RemoteAdsLoader($this->remoteUrl);
        $remoteLines = $remoteAdsLoader->loadRemoteAds($uri);

        return array_diff($localLines, $remoteLines);
    }

    /**
     * @param string $uri
     * @return void
     * @throws Exception
     */
    public function updateLocalFileWithRemoteEntries(string $uri): void
    {
        $localLines = $this->getFileLines($this->localFile);

        $remoteAdsLoader = new RemoteAdsLoader($this->remoteUrl);
        $remoteLines = $remoteAdsLoader->loadRemoteAds($uri);

        foreach ($localLines as $line) {
            if (preg_match('/^([^\s#]+)\s+.*$/', $line, $matches)) {
                $domain = $matches[1];
                foreach ($remoteLines as $remoteLine) {
                    if (strpos($remoteLine, $domain) !== false && !in_array($remoteLine, $localLines)) {
                        $localLines[] = $remoteLine;
                    }
                }
            }
        }

        file_put_contents($this->localFile, implode("\n", $localLines));
    }

    private function getFileLines($filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception("File $filePath does not exist.");
        }
        return file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
}
