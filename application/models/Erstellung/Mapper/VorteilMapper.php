<?php

class Application_Model_Erstellung_Mapper_VorteilMapper implements Application_Model_Erstellung_Beschreibung_BeschreibungInterface, Application_Model_Erstellung_Punkte_PunkteInterface{

    protected $dbTableVorteil;

    public function getDbTableVorteil() {
        if (null === $this->dbTableVorteil) {
            $this->setDbTableVorteil('Application_Model_DbTable_Vorteil');
        }
        return $this->dbTableVorteil;
    }

    public function setDbTableVorteil($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->dbTableVorteil = $dbTable;
        return $this;
    }
    
    public function getPunkte($ids){
        $select = $this->getDbTableVorteil()->select();
        $select->setIntegrityCheck(false);
        $select->from('Vorteile', array('Punkte' => new Zend_Db_Expr('SUM(Kosten)')));
        $select->where('VorteilID IN(?)', $ids);
        $result = $this->getDbTableVorteil()->fetchAll($select);
        if($result->count() > 0){
            foreach ($result as $row){
                $return = $row->Punkte;
            }
            return $return;
        }else{
            return null;
        }
    }
    
    public function getBeschreibung($ids) {
        $select = $this->getDbTableVorteil()->select();
        $select->setIntegrityCheck('false');
        $select->from('Vorteile', array('Beschreibung' => 'Beschreibung'));
        $select->where('VorteilID IN (?)', $ids);
        $result = $this->getDbTableVorteil()->fetchAll($select);
        if($result->count() > 0){
            foreach ($result as $row){
                $return = $row->Beschreibung;
            }
            return $return;
        }else{
            return null;
        }
    }
    
}
