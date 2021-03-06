<?php

/**
 * Class Story_Model_Mapper_Result_MagicMapper
 */
class Story_Model_Mapper_Result_MagicMapper
{

    use Story_Model_Mapper_DbTableTrait;

    /**
     * @param int $characterId
     *
     * @return Story_Model_Magie[]
     * @throws Exception
     */
    public function getMagienToLearnByRpg ($characterId)
    {
        $returnArray = [];
        $select = $this->getDbTable('Magie')->select();
        $select->setIntegrityCheck(false);
        $select->from('magien');
        $select->joinLeft(
            'charakterMagien',
            'magien.magieId = charakterMagien.magieId AND charakterMagien.charakterId = ' . (int)$characterId,
            []
        );
        $select->where('magien.lernbedingung = "RPG-Ereignis" AND charakterMagien.magieId IS NULL');
        $result = $this->getDbTable('Magie')->fetchAll($select);
        foreach ($result as $row) {
            $magie = new Story_Model_Magie();
            $magie->setId($row->magieId);
            $magie->setBezeichnung($row->name);
            $returnArray[] = $magie;
        }
        return $returnArray;
    }


    /**
     * @param $episodeId
     * @param $characterId
     * @param $art
     * @param $request
     *
     * @throws Exception
     */
    public function removeSkillrequest ($episodeId, $characterId, $art, $request)
    {
        $db = $this->getDbTable('Magie')->getDefaultAdapter();
        $stmt = $db->prepare(
            'DELETE FROM episodenCharakterSkillRequest 
                                WHERE episodenId = ? 
                                AND charakterId = ? 
                                AND request = ? 
                                AND art = ?'
        );
        $stmt->execute([$episodeId, $characterId, $request, $art]);
    }


    /**
     * @param $episodeId
     * @param $characterId
     * @param $art
     * @param $request
     * @param array $ids
     *
     * @throws Exception
     */
    public function addSkillrequest ($episodeId, $characterId, $art, $request, $ids = [])
    {
        $db = $this->getDbTable('Magie')->getDefaultAdapter();
        $stmt = $db->prepare(
            'INSERT INTO episodenCharakterSkillRequest 
                                (episodenId, charakterId, art, id, request)
                                VALUES (?, ?, ?, ?, ?)'
        );
        foreach ($ids as $id) {
            $stmt->execute([$episodeId, $characterId, $art, $id, $request]);
        }
    }

    /**
     * @param int $characterId
     *
     * @return Application_Model_Magie[]
     * @throws Exception
     */
    public function getCharakterMagien ($characterId)
    {
        $returnArray = [];
        $select = $this->getDbTable('CharakterMagie')->select();
        $select->setIntegrityCheck(false);
        $select->from('charakterMagien', []);
        $select->joinInner(
            'magien',
            'charakterMagien.magieId = magien.magieId',
            ['magieId', 'name', 'beschreibung']
        );
        $select->where('charakterMagien.charakterId = ?', $characterId);
        $result = $this->getDbTable('CharakterMagie')->fetchAll($select);
        foreach ($result as $row) {
            $magie = new Story_Model_Magie();
            $magie->setId($row->magieId);
            $magie->setBezeichnung($row->name);
            $magie->setBeschreibung($row->beschreibung);
            $returnArray[] = $magie;
        }
        return $returnArray;
    }

    /**
     * @param int $episodeId
     * @param int $characterId
     *
     * @return array
     * @throws Exception
     */
    public function getRequestedMagic ($episodeId, $characterId)
    {
        $returnArray = [];
        $db = $this->getDbTable('Episoden')->getDefaultAdapter();
        $sql = 'SELECT magieId, name, request as requestType 
                FROM episodenCharakterSkillRequest AS eCSR
                INNER JOIN magien 
                    ON eCSR.art = "magie" 
                    AND eCSR.id = magien.magieId
                WHERE episodenId = ? AND charakterId = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute([$episodeId, $characterId]);
        $result = $stmt->fetchAll();
        foreach ($result as $row) {
            $magie = new Story_Model_Magie();
            $magie->setId($row['magieId']);
            $magie->setBezeichnung($row['name']);
            $magie->setRequestType($row['requestType']);
            $returnArray[] = $magie;
        }
        return $returnArray;
    }

}