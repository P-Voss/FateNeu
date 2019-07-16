<?php

use Logs\Models\Auswertung;
use Logs\Services\Plot;
use Logs\Services\Log;
use Logs\Services\Episode;

/**
 * Description of Logs_ReviewController
 *
 * @author Voß
 */
class Logs_ReviewController extends Zend_Controller_Action {
    
    /**
     * @var Plot
     */
    private $plotService;
    /**
     * @var Episode
     */
    private $episodenService;
    /**
     * @var Log
     */
    private $logService;
    
    private $auth;
    
    
    public function init() {
        if(!$this->_helper->logincheck()){
            $this->redirect('index/index');
        }
        $this->logService = new Log();
        $config = HTMLPurifier_Config::createDefault();
        $this->view->purifier = new HTMLPurifier($config);
        $this->plotService = new Plot();
        $this->episodenService = new Episode();
        $this->auth = Zend_Auth::getInstance()->getIdentity();
    }
    
    
    public function indexAction() {
        $this->redirect('Logs');
    }
    
    
    public function showAction() {
        $episodenId = (int) $this->getRequest()->getParam('episode');
        if($episodenId <= 0
            || ((bool)!$this->auth->isLogleser && $this->episodenService->needsLogleser($episodenId)))
        {
            $this->redirect('index');
        }
        try {
            $this->view->episode = $this->episodenService->getEpisode($episodenId);
            $this->view->participants = $this->episodenService->getParticipantsByEpisode($episodenId);
            $this->view->logs = $this->logService->getLogsForEpisode($episodenId);
        } catch (Exception $exception) {
            $this->redirect('index');
        }
    }
    
    
    public function downloadAction() {
        $episodenId = (int) $this->getRequest()->getParam('episode');
        $logId = (int)$this->getRequest()->getParam('log');
        if(
            ($episodenId <= 0 || $logId <= 0)
                || ((bool)!$this->auth->isLogleser && $this->episodenService->needsLogleser($episodenId)))
        {
            $this->redirect('index');
        }
        $log = $this->logService->getLogByLogIdAndEpisodeId($logId, $episodenId);
        if(!$this->logService->downloadLog($log)){
            $this->redirect('Logs/review/show/episode/' . $episodenId);
        }
    }
    
    public function gesamtlogAction() {
        $episodenId = (int) $this->getRequest()->getParam('episode');
        if($episodenId <= 0
            || ((bool)!$this->auth->isLogleser && $this->episodenService->needsLogleser($episodenId)))
        {
            $this->redirect('index');
        }
        try {
            $episode = $this->episodenService->getEpisode($episodenId);
            $logs = $this->logService->getLogsForEpisode($episodenId);
            if(!$this->logService->downloadGesamtlog($logs, $episode->getName())){
                $this->redirect('Logs/review/show/episode/' . $episodenId);
            }
        } catch (Exception $exception) {
            $this->redirect('Logs/review/show/episode/' . $episodenId);
        }
    }
    
    public function reviewAction() {
        $episodenId = (int) $this->getRequest()->getParam('episodenId');
        if($episodenId <= 0
            || ((bool)!$this->auth->isLogleser && $this->episodenService->needsLogleser($episodenId)))
        {
            $this->redirect('index');
        }
        try {
            $auswertung = new Auswertung();
            $auswertung->setDescription($this->getRequest()->getPost('feedback', ''));
            $auswertung->setUserId(Zend_Auth::getInstance()->getIdentity()->userId);
            $auswertung->setIsAccepted((int)$this->getRequest()->getPost('isAccepted', 0) === 1);
            $this->episodenService->saveAuswertung($auswertung, $episodenId);
            $this->redirect('Logs');
        } catch (Exception $exception) {
            $this->redirect('index');
        }
    }
    
}
