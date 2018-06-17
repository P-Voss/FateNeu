<?php

/**
 * Description of Layout
 *
 * @author Vosser
 */
class Application_Service_Layout {

    /**
     * @param $auth
     *
     * @return Application_Model_Layout
     * @throws Zend_Db_Statement_Exception
     * @throws Exception
     */
    public function getLayoutData($auth){
        $informationService = new Application_Service_Information();
        $charakterBuilder = new Application_Service_CharakterBuilder();
        $layoutModel = new Application_Model_Layout();
        $userMapper = new Application_Model_Mapper_UserMapper();
        $charakterMapper = new Application_Model_Mapper_CharakterMapper();
        
        $layoutModel->setUnreadPmCount($userMapper->countNewPm($auth->userId));
        $layoutModel->setUsergruppe($auth->usergruppe);
        $layoutModel->setLogleser($userMapper->isLogleser($auth->userId));
        $layoutModel->setNotifications($userMapper->getNotifications($auth->userId));
        if($userMapper->hasChara($auth->userId)){
            
            if ($charakterBuilder->initCharakterByUserId($auth->userId)) {
                $charakterBuilder->setVorteile()
                    ->setNachteile()
                    ->setCircuit()
                    ->setNaturelement()
                    ->setClassData()
                    ->setLuck()
                    ->setMagien()
                    ->setMagieschulen()
                    ->setOdo()
                    ->setProfile()
                    ->setSkills()
                    ->setVermoegen()
                    ->setWerte();
                $charakter = $charakterBuilder->getCharakter();
                $layoutModel->setHasChara(true);
                $layoutModel->setCharakter($charakter);
                $layoutModel->setCharakterTraining($charakterMapper->getCurrentTraining($charakter->getCharakterid()));
                $informationService->setCharakter($charakter);
            } else {
                $layoutModel->setHasChara(false);
            }
        }
        $layoutModel->setInformations($informationService->getInformations());
        return $layoutModel;
    }
}
