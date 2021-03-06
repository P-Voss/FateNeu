<?php

namespace Logs\Models\Mappers;

use Exception;
use Logs\Models\Log;

/**
 * Description of LogMapper
 *
 * @author Voß
 */
class LogMapper extends \Application_Model_Mapper_LogMapper {

    /**
     * @param int $userId
     *
     * @return boolean
     */
    public function isLogleser($userId) {
        try {
            $db = $this->getDbTable('Logs')->getDefaultAdapter();
            $result = $db->query('SELECT * FROM logleser WHERE userId = ?', [$userId]);
            return count($result->fetchAll()) > 0;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @param int $episodenId
     * @return Log[]
     * @throws Exception
     */
    public function getLogsByEpisodenId($episodenId) {
        $returnArray = [];
        $db = $this->getDbTable('Episoden')->getDefaultAdapter();
        $sql = 'SELECT name, beschreibung, logId, md5 FROM logs WHERE episodenId = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute([$episodenId]);
        foreach ($stmt->fetchAll() as $row) {
            $log = new Log();
            $log->setId($row['logId']);
            $log->setName($row['name']);
            $log->setMd5($row['md5']);
            $log->setBeschreibung($row['beschreibung']);
            $returnArray[] = $log;
        }
        return $returnArray;
    }

    /**
     * @param int $logId
     * @param int $episodeId
     * @return Log
     * @throws Exception
     */
    public function getLogByLogIdAndEpisodeId($logId, $episodeId) {
        $log = new Log();
        $select = $this->getDbTable('Logs')->select();
        $select->where('logId = ?', $logId);
        $select->where('episodenId = ?', $episodeId);
        $result = $this->getDbTable('Logs')->fetchRow($select);
        if($result !== null){
            $log->setId($result->logId);
            $log->setName($result->name);
            $log->setEpisodenId($result->episodenId);
            $log->setMd5($result->md5);
        }
        return $log;
    }
    
}
