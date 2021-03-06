<?php

class Application_Model_Mapper_CircuitMapper implements Application_Model_Erstellung_Information_InformationInterface{

    
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
    
    public function getPunkte($id){
        $select = $this->getDbTable('Circuit')->select();
        $select->setIntegrityCheck(false);
        $select->from('circuit', array('Punkte' => new Zend_Db_Expr('SUM(kosten)')));
        $select->where('circuitId = ?', $id);
        $result = $this->getDbTable('Circuit')->fetchAll($select);
        if($result->count() > 0){
            foreach ($result as $row){
                $return = $row->Punkte;
            }
            return $return;
        }else{
            return null;
        }
    }
    
    public function getBeschreibung($id) {
        $select = $this->getDbTable('Circuit')->select();
        $select->setIntegrityCheck('false');
        $select->from('circuit', array('besonderheit'));
        $select->where('circuitId = ?', $id);
        $result = $this->getDbTable('Circuit')->fetchAll($select);
        if($result->count() > 0){
            foreach ($result as $row){
                $return = $row->besonderheit;
            }
            return $return;
        }else{
            return null;
        }
    }
    
}
