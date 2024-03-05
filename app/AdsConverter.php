<?php

namespace App;

use Exception;

class AdsConverter
{
    private string $inputFile;

    private string $outputFile;

    public function __construct($inputFile, $outputFile)
    {
        $this->inputFile = $inputFile;
        $this->outputFile = $outputFile;
    }

    /**
     * @throws Exception
     * @return void
     */
    public function convertToTxt(): void
    {
        $jsonContent = file_get_contents($this->inputFile);
        $data = json_decode($jsonContent, true);

        $txtContent = "";

        foreach ($data as $ad) {
            $field4 = $ad['Field #4'] === '' ? '""' : $ad['Field #4'];

            $txtContent .=  $ad['Field #1'] . ', ' .
                $ad['Field #2'] . ', ' .
                $ad['Field #3'] . ', ' .
                $field4 .
                "\n";
        }

        file_put_contents($this->outputFile, $txtContent);
    }
}
