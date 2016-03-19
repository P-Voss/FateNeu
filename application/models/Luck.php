<?php

/**
 * Description of Luck
 *
 * @author Philipp Voß <voss.ph@web.de>
 */
class Application_Model_Luck {
    
    protected $id;
    protected $kategorie;
    protected $beschreibung;
    protected $kosten;
    
    
    public function getId() {
        return $this->id;
    }

    public function getKategorie() {
        return $this->kategorie;
    }

    public function getBeschreibung() {
        return $this->beschreibung;
    }

    public function getKosten() {
        return $this->kosten;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setKategorie($kategorie) {
        $this->kategorie = $kategorie;
    }

    public function setBeschreibung($beschreibung) {
        $this->beschreibung = $beschreibung;
    }

    public function setKosten($kosten) {
        $this->kosten = $kosten;
    }
    
}
