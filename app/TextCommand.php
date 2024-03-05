<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:text',
    description: '',
)]
final class TextCommand extends Command
{
    private const ARGUMENT_LOCAL_FILE = 'localFile';
    private const ARGUMENT_REMOTE_URL = 'remoteUrl';

    protected function configure(): void
    {
        $this->addArgument(self::ARGUMENT_LOCAL_FILE, InputArgument::REQUIRED, "Path to local file");
        $this->addArgument(self::ARGUMENT_REMOTE_URL, InputArgument::REQUIRED, "Url to remote file");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $localFile = $input->getArgument(self::ARGUMENT_LOCAL_FILE);
        $remoteUrl = $input->getArgument(self::ARGUMENT_REMOTE_URL);

        $this->convert($output, $localFile);
        $this->validate($output, $localFile);
        $this->load($output, $remoteUrl);
        $this->create($output, $localFile, $remoteUrl);

        return 0;
    }

    private function convert(OutputInterface $output, string $localFile): void
    {
        $outputFile = WWW_DIR . "/assets/ads.txt";

        $converter = new AdsConverter($localFile . '/ads.json', $outputFile);
        $converter->convertToTxt();

        $output->writeln('Conversion has been successful');
    }

    private function validate(OutputInterface $output, string $localFile): void
    {
        $outputFile = WWW_DIR ."/assets/valid_ads.txt";

        $validator = new AdsValidator($localFile . '/ads.txt', $outputFile);
        $validator->validateAds();

        $output->writeln('Validation has been successful');
    }

    private function load(OutputInterface $output, string $remoteUrl): void
    {
        $remoteLoader = new RemoteAdsLoader($remoteUrl);
        $remoteLoader->loadRemoteAds('ads.txt');

        $output->writeln('Loading has been successful');
    }

    private function create(OutputInterface $output, string $localFile, string $remoteUrl): void
    {
        $comparator = new AdsComparer($localFile . '/ads.txt', $remoteUrl);
        $uniqueLines = $comparator->getUniqueLocalLines('ads.txt');

        file_put_contents(WWW_DIR . '/invalid-ads.txt', $uniqueLines);

        $currentDateTime = new \DateTime();
        $output->writeln('this file was generated at:' . $currentDateTime->format('d.m.Y. H:i:s'));
    }
}