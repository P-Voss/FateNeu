<?php

/**
 * Description of NachrichtenMapper
 *
 * @author Voß
 */
class Nachrichten_Model_Mapper_NachrichtenMapper {

    /**
     * @param type $tablename
     * @return \Zend_Db_Table_Abstract
     * @throws Exception
     */
    public function getDbTable($tablename) {
        $className = 'Application_Model_DbTable_' . $tablename;
        if (!class_exists($className)) {
            throw new Exception('Falsche Tabellenadapter angegeben');
        }
        $dbTable = new $className();
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        return $dbTable;
    }

    /**
     * @param int $userId
     * @return \Nachrichten_Model_Nachricht
     */
    public function getNachrichtenByReceiverId($userId) {
        $returnArray = array();
        $select = $this->getDbTable('Pm')->select();
        $select->where('empfaengerId = ?', $userId);
        $select->where('status != ?', 'archiv');
        $select->order('creationDate DESC');
        $result = $this->getDbTable('Pm')->fetchAll($select);
        foreach ($result as $row) {
            $nachricht = new Nachrichten_Model_Nachricht();
            $nachricht->setId($row->nachrichtId);
            $nachricht->setVerfasserId($row->verfasserId);
            $nachricht->setEmpfaengerId($row->empfaengerId);
            $nachricht->setBetreff($row->betreff);
            $nachricht->setNachricht($row->nachricht);
            $nachricht->setCreationDate($row->creationDate);
            $nachricht->setStatus($row->status);
            $returnArray[] = $nachricht;
        }
        return $returnArray;
    }

    /**
     * @param int $userId
     * @return \Nachrichten_Model_Nachricht
     */
    public function getNachrichtenByDispatcherId($userId) {
        $returnArray = array();
        $select = $this->getDbTable('Pm')->select();
        $select->where('verfasserId = ?', $userId);
        $select->order('creationDate DESC');
        $result = $this->getDbTable('Pm')->fetchAll($select);
        foreach ($result as $row) {
            $nachricht = new Nachrichten_Model_Nachricht();
            $nachricht->setId($row->nachrichtId);
            $nachricht->setVerfasserId($row->verfasserId);
            $nachricht->setEmpfaengerId($row->empfaengerId);
            $nachricht->setBetreff($row->betreff);
            $nachricht->setNachricht($row->nachricht);
            $nachricht->setCreationDate($row->creationDate);
            $nachricht->setStatus($row->status);
            $returnArray[] = $nachricht;
        }
        return $returnArray;
    }

    /**
     * @param int $userId
     * @return \Nachrichten_Model_Nachricht
     */
    public function getNachrichtenarchivById($userId) {
        $returnArray = array();
        $select = $this->getDbTable('Pm')->select();
        $select->where('empfaengerId = ?', $userId);
        $select->where('status = ?', 'archiv');
        $select->order('creationDate DESC');
        $result = $this->getDbTable('Pm')->fetchAll($select);
        foreach ($result as $row) {
            $nachricht = new Nachrichten_Model_Nachricht();
            $nachricht->setId($row->nachrichtId);
            $nachricht->setVerfasserId($row->verfasserId);
            $nachricht->setEmpfaengerId($row->empfaengerId);
            $nachricht->setBetreff($row->betreff);
            $nachricht->setNachricht($row->nachricht);
            $nachricht->setCreationDate($row->creationDate);
            $nachricht->setStatus($row->status);
            $returnArray[] = $nachricht;
        }
        return $returnArray;
    }

    /**
     * @param int $nachrichtId
     * @return \Nachrichten_Model_Nachricht
     */
    public function getNachrichtById($nachrichtId) {
        $nachricht = new Nachrichten_Model_Nachricht();
        $select = $this->getDbTable('Pm')->select();
        $select->where('nachrichtId = ?', $nachrichtId);
        $result = $this->getDbTable('Pm')->fetchRow($select);
        if($result !== null){
            $nachricht->setId($result->nachrichtId);
            $nachricht->setVerfasserId($result->verfasserId);
            $nachricht->setEmpfaengerId($result->empfaengerId);
            $nachricht->setBetreff($result->betreff);
            $nachricht->setNachricht($result->nachricht);
            $nachricht->setCreationDate($result->creationDate);
            $nachricht->setStatus($result->status);
        }
        return $nachricht;
    }
    
    /**
     * @param Nachrichten_Model_Nachricht $nachricht
     */
    public function saveMessage(Nachrichten_Model_Nachricht $nachricht) {
        $date = new DateTime();
        $data = [
            'verfasserId' => $nachricht->getVerfasserId(),
            'empfaengerId' => $nachricht->getEmpfaengerId(),
            'betreff' => $nachricht->getBetreff(),
            'nachricht' => $nachricht->getNachricht(),
            'creationDate' => $date->format('Y-m-d H:i:s'),
        ];
        $this->getDbTable('Pm')->insert($data);
    }
    
    /**
     * @param int $nachrichtId
     * @return int
     */
    public function setRead($nachrichtId) {
        $data = array('status' => 'gelesen');
        return $this->getDbTable('Pm')->update($data, array('nachrichtId = ?' => $nachrichtId));
    }
    
    /**
     * @param Nachrichten_Model_Nachricht $nachricht
     * @return int
     */
    public function deleteMessage(Nachrichten_Model_Nachricht $nachricht) {
        $data = array('status' => 'archiv');
        return $this->getDbTable('Pm')->update($data, array('nachrichtId = ?' => $nachricht->getId()));
    }

}
