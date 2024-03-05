<?php
declare(strict_types=1);

namespace App\Presenters;

use App\AdsComparer;
use App\AdsConverter;
use App\AdsValidator;
use App\RemoteAdsLoader;
use Exception;
use Nette;

final class HomePresenter extends Nette\Application\UI\Presenter
{
    public function actionConvert(): void
    {
        $inputFile = WWW_DIR . '/assets/ads.json';
        $outputFile = WWW_DIR . "/assets/ads.txt";

        $converter = new AdsConverter($inputFile, $outputFile);
        try {
            $converter->convertToTxt();
            $this->flashMessage('Conversion has been successful','success');
        } catch (Exception $e) {
            $this->flashMessage('Conversion failed', 'error');
        }

        $this->redirect('Home:default');
    }

    public function actionValidate(): void
    {
        $inputFile = WWW_DIR . "/assets/ads.txt";
        $outputFile = WWW_DIR ."/assets/valid_ads.txt";

        $validator = new AdsValidator($inputFile, $outputFile);
        try {
            $validator->validateAds();
            $this->flashMessage('Validation has been successful','success');
        } catch (Exception $e) {
            $this->flashMessage('Validation failed', 'error');
        }

        $this->redirect('Home:default');
    }

    public function actionLoad(): void
    {
        $remoteUrl = "https://trackad.cz";
        $remoteLoader = new RemoteAdsLoader($remoteUrl);

        try {
            $remoteLoader->loadRemoteAds('ads.txt');
            $this->flashMessage('Loading has been successful','success');
        } catch (Exception $e) {
            $this->flashMessage('Loading failed', 'error');
        }

        $this->redirect('Home:default');
    }

    public function actionCompare(): void
    {
        $localFile = WWW_DIR . "/assets/ads.txt";
        $remoteUrl = "https://trackad.cz/";

        $comparator = new AdsComparer($localFile, $remoteUrl);

        $uniqueLocalAds = $comparator->getUniqueLocalLines('ads.txt');
        $this->template->uniqueLocalAds = $uniqueLocalAds;

        $comparator->getUniqueLocalLines('ads.txt');
    }

    public function actionUpdateLocalFile(): void
    {
        $localFile = WWW_DIR . "/assets/ads.txt";
        $remoteUrl = "https://trackad.cz/";

        $updater = new AdsComparer($localFile, $remoteUrl);

        try {
            $updater->updateLocalFileWithRemoteEntries('ads.txt');
            $this->flashMessage('Update has been successful','success');
        } catch (Exception $e) {
            $this->flashMessage('Update failed', 'error');
        }

        $this->redirect('Home:default');
    }
}
