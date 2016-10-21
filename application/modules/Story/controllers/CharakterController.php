<?php

/**
 * Description of CharakterController
 *
 * @author Philipp Voß <voss.ph@web.de>
 */
class Story_CharakterController extends Zend_Controller_Action {
    
    /**
     * @var Application_Service_Charakter 
     */
    protected $charakterService;
    
    /**
     * @var Application_Model_Charakter
     */
    protected $charakter;

    /**
     * @var Story_Service_Plot
     */
    protected $plotService;
    
    public function init(){
        $config = HTMLPurifier_Config::createDefault();
        $this->view->purifier = new HTMLPurifier($config);
        $this->charakterService = new Application_Service_Charakter();
        if($this->_helper->logincheck() === false){
            $this->redirect('index');
        }
        $this->charakter = $this->charakterService->getCharakterByUserid(Zend_Auth::getInstance()->getIdentity()->userId);
        $this->plotService = new Story_Service_Plot();
    }
    
    public function indexAction() {
        $this->redirect('Story/plots/show/id/' . $this->getRequest()->getParam('plot'));
    }
    
    public function showAction() {
        if($this->plotService->isSL($this->getRequest()->getParam('plot'), Zend_Auth::getInstance()->getIdentity()->userId)){
            $charakter = $this->charakterService->getCharakter($this->getRequest(), true);
            $this->view->charakter = $charakter;
            
            $magieService = new Shop_Service_Magie();
            $magieschulen = $magieService->getMagieschulenForCharakter($charakter);
            $schulen = array();
            foreach ($magieschulen as $schule){
                if($schule->getLearned() === true){
                    $schule->setMagien($magieService->getLearnedMagieBySchule($charakter->getCharakterid(), $schule));
                    $schulen[] = $schule;
                }
            }
            $this->view->magieschulen = $schulen;

            $skillService = new Shop_Service_Skill();
            $skillarten = $skillService->getSkillArtenForCharakter($charakter);
            foreach ($skillarten as $skillart) {
                if($skillart->getLearned() === false){
                    unset($skillart);
                }else{
                    $skillart->setSkills($skillService->getLearnedSkillBySkillart($charakter->getCharakterid(), $skillart));
                }
            }
            $this->view->skillarten = $skillarten;
            
        }elseif(!is_null($this->getRequest()->getParam('plot'))){
            $this->redirect('Story/plots/show/id/' . $this->getRequest()->getParam('plot'));
        }else{
            $this->redirect('Story');
        }
    }
    
}
