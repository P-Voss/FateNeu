<?php

class Application_Model_Mapper_VorteilMapper implements Application_Model_Erstellung_Information_InformationInterface{

    
    public function getDbTable($tablename) {
        $className = 'Application_Model_DbTable_' . $tablename;
        if(!class_exists($className)){
            throw new Exception('Falsche Tabellenadapter angegeben');
        }
        $dbTable = new $className();
        if(!$dbTable instanceof Zend_Db_Table_Abstract){
            throw new Exception('Invalid table data gateway provided');
        }
        return $dbTable;
    }
    
    public function getPunkte($ids){
        $select = $this->getDbTable('Vorteil')->select();
        $select->setIntegrityCheck(false);
        $select->from('Vorteile', array('Punkte' => new Zend_Db_Expr('SUM(Kosten)')));
        $select->where('VorteilID IN(?)', $ids);
        $result = $this->getDbTable('Vorteil')->fetchAll($select);
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
        $select = $this->getDbTable('Vorteil')->select();
        $select->setIntegrityCheck('false');
        $select->from('Vorteile', array('Beschreibung' => 'Beschreibung'));
        $select->where('VorteilID IN (?)', $ids);
        $result = $this->getDbTable('Vorteil')->fetchAll($select);
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
