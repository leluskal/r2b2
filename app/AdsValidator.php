<?php

namespace App;

class AdsValidator
{
    private string $inputFile;

    private string $outputFile;

    public function __construct($inputFile, $outputFile) {
        $this->inputFile = $inputFile;
        $this->outputFile = $outputFile;
    }

    /**
     * @return void
     */
    public function validateAds(): void
    {
        $lines = file($this->inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $validLines = [];
        foreach ($lines as $line) {
            $fields = explode(', ', $line);

            if (count($fields) !== 4) {
                continue;
            }

            $url = trim($fields[0]);
            $value = trim($fields[1]);
            $type = strtoupper(trim($fields[2]));
            $optionalField = isset($fields[3]) ? trim($fields[3]) : '';

            if (!$this->isValidUurl($url)) {
                continue;
            }

            if ($value === '') {
                continue;
            }

            if ($type !== 'RESELLER' && $type !== 'DIRECT') {
                continue;
            }

            $validLine = implode(', ', [$url, $value, $type, $optionalField]);
            $validLines[] = $validLine;
        }

        $outputContent = implode("\n", $validLines);
        file_put_contents($this->outputFile, $outputContent);
    }

    /**
     * @param $url
     * @return bool
     */
    public function isValidUurl($url): bool
    {
        $pattern = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';

        if (preg_match($pattern, $url)) {
            return true;
        } else {
            return false;
        }
    }
}


