<?php

/**
 * Description of Story_Model_Mapper_PlotMapper
 *
 * @author Voß
 */
class Story_Model_Mapper_PlotMapper extends Application_Model_Mapper_PlotMapper {
    
    /**
     * @param int $plotId
     * @param int $gruppenId
     * @return int
     */
    public function connectGroupToPlot($plotId, $gruppenId) {
        $data = array(
            'plotId' => $plotId,
            'gruppenId' => $gruppenId,
        );
        return parent::getDbTable('PlotToGruppe')->insert($data);
    }
    
    /**
     * @param int $plotId
     * @return \Story_Model_Plot
     */
    public function getPlotById($plotId) {
        $plot = new Story_Model_Plot();
        $select = $this->getDbTable('Plots')->select();
        $select->where('plotId = ?', $plotId);
        $result = $this->getDbTable('Plots')->fetchRow($select);
        if($result !== null){
            $plot->setId($result->plotId);
            $plot->setName($result->name);
            $plot->setBeschreibung($result->beschreibung);
            $plot->setSlId($result->userId);
            $plot->setCreateDate($result->createDate);
            $plot->setZusammenfassung($result->ergebnis);
        }
        return $plot;
    }
    
    /**
     * @param Application_Model_Plot $plot
     * @return int
     */
    public function createPlot(Application_Model_Plot $plot) {
        $data = array(
            'userId' => $plot->getSlId(),
            'name' => $plot->getName(),
            'beschreibung' => $plot->getBeschreibung(),
            'createDate' => $plot->getCreateDate('Y-m-d H:i:s'),
        );
        return $this->getDbTable('Plots')->insert($data);
    }
    
    /**
     * @param int $plotId
     * @param string $genres
     */
    public function setGenres($plotId, $genres = array()) {
        $data = array('plotId' => $plotId);
        foreach ($genres as $genre) {
            $data['genre'] = $genre;
            $this->getDbTable('PlotGenres')->insert($data);
        }
    }
    
    /**
     * @param int $gruppenId
     * @return \Gruppen_Model_Plot
     */
    public function getPlotsByGruppe($gruppenId) {
        $returnArray = array();
        $select = $this->getDbTable('PlotToGruppe')->select();
        $select->setIntegrityCheck(false);
        $select->from('plotToGruppe');
        $select->join('plots', 'plots.plotId = plotToGruppe.plotId');
        $select->where('plotToGruppe.gruppenId = ?', $gruppenId);
        $result = $this->getDbTable('PlotToGruppe')->fetchAll($select);
        if($result->count() > 0){
            foreach ($result as $row) {
                $plot = new Gruppen_Model_Plot();
                $plot->setId($row->plotId);
                $plot->setName($row->name);
                $plot->setBeschreibung($row->beschreibung);
                $plot->setCreateDate($row->createDate);
                $returnArray[] = $plot;
            }
        }
        return $returnArray;
    }
    
    /**
     * @param int $plotId
     * @param int $userId
     * @return boolean
     */
    public function verifySl($plotId, $userId) {
        $select = $this->getDbTable('Spielergruppen')->select();
        $select->setIntegrityCheck(false);
        $select->from('spielergruppen');
        $select->joinInner('plotToGruppe', 'spielergruppen.gruppenId = plotToGruppe.gruppenId');
        $select->where('spielergruppen.userId = ?', $userId);
        $select->where('plotToGruppe.plotId = ?', $plotId);
        return $this->getDbTable('Spielergruppen')->fetchAll($select)->count() > 0;
    }
    
    /**
     * @param int $plotId
     * @return array
     */
    public function getEpisodes($plotId) {
        $returnArray = array();
        $select = $this->getDbTable('Episoden')->select();
        $select->setIntegrityCheck(false);
        $select->from('episoden');
        $select->joinInner('episodenToPlot', 'episoden.episodenId = episodenToPlot.episodenId');
        $select->where('episodenToPlot.plotId = ?', $plotId);
        $result = $this->getDbTable('Episoden')->fetchAll($select);
        foreach ($result as $row) {
            $episode = new Story_Model_Episode();
            $episode->setId($row->id);
            $episode->setName($row->name);
            $episode->setBeschreibung($row->beschreibung);
            $episode->setZusammenfassung($row->zusammenfassung);
            $episode->setCreateDate($row->createDate);
            $episode->setEditDate($row->editDate);
            $returnArray[] = $episode;
        }
        return $returnArray;
    }
    
    /**
     * @param int $plotId
     * @return \Application_Model_Charakter
     */
    public function getParticipantsByPlotId($plotId) {
        $returnArray = array();
        $select = $this->getDbTable('Charakter')->select();
        $select->setIntegrityCheck(false);
        $select->from('charakter');
        $select->joinInner('charakterPlots', 'charakter.charakterId = charakterPlots.charakterId');
        $select->where('charakterPlots.plotId = ?', $plotId);
        $select->order('charakter.vorname ASC');
        $result = $this->getDbTable('Charakter')->fetchAll($select);
        foreach ($result as $row) {
            $charakter = new Application_Model_Charakter();
            $charakter->setCharakterid($row->charakterId);
            $charakter->setVorname($row->vorname);
            $charakter->setNachname($row->nachname);
            $returnArray[] = $charakter;
        }
        return $returnArray;
    }
    
    /**
     * @param int $plotId
     * @return \Application_Model_Charakter
     */
    public function getParticipantsNotInPlot($plotId) {
        $returnArray = array();
        $select = $this->getDbTable('Charakter')->select();
        $select->setIntegrityCheck(false);
        $select->from('plotToGruppe');
        $select->joinInner('charakterGruppen', 'charakterGruppen.gruppenId = plotToGruppe.gruppenId');
        $select->joinLeft('charakterPlots', 'charakterPlots.plotId = plotToGruppe.plotId AND charakterPlots.charakterId = charakterGruppen.charakterId');
        $select->joinLeft('charakter', 'charakterGruppen.charakterId = charakter.charakterId');
        $select->where('plotToGruppe.plotId = ? AND charakterPlots.charakterId IS NULL', $plotId);
        $select->order('charakter.vorname ASC');
        $result = $this->getDbTable('Charakter')->fetchAll($select);
        foreach ($result as $row) {
            $charakter = new Application_Model_Charakter();
            $charakter->setCharakterid($row->charakterId);
            $charakter->setVorname($row->vorname);
            $charakter->setNachname($row->nachname);
            $returnArray[] = $charakter;
        }
        return $returnArray;
    }
    
    /**
     * @param array $slId
     * @return \Gruppen_Model_Plot
     */
    public function getPlotsBySLId($slId) {
        $returnArray = array();
        $select = $this->getDbTable('Plots')->select();
        $select->from('plots');
        $select->where('userId = ?', $slId);
        $result = $this->getDbTable('Plots')->fetchAll($select);
        if($result->count() > 0){
            foreach ($result as $row) {
                $plot = new Gruppen_Model_Plot();
                $plot->setId($row->plotId);
                $plot->setSlId($slId);
                $plot->setName($row->name);
                $plot->setBeschreibung($row->beschreibung);
                $plot->setCreateDate($row->createDate);
                $returnArray[] = $plot;
            }
        }
        return $returnArray;
    }
    
    /**
     * @param int $plotId
     * @param int $charakterId
     */
    public function addParticipant($plotId, $charakterId) {
        $data = [
            'charakterId' => $charakterId,
            'plotId' => $plotId,
        ];
        $this->getDbTable('CharakterPlots')->insert($data);
    }
    
}
