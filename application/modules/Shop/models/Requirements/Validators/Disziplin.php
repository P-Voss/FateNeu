<?php

namespace Shop\Models\Requirements\Validators;

use Shop\Models\Requirements\ValidationInterface;

/**
 * Description of Disziplin
 *
 * @author Voß
 */
class Disziplin implements ValidationInterface {
    
    /**
     * @param \Application_Model_Charakter $charakter
     * @param mixed $value
     * @return boolean
     */
    public function check(\Application_Model_Charakter $charakter, $value){
        return $charakter->getCharakterwerte()->getDisziplin() >= $value;
    }

    /**
     * @param \Application_Model_Charakter $charakter
     * @param mixed $value
     * @return boolean
     */
    public function successfulWildcard (\Application_Model_Charakter $charakter, $value)
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isWildcard ()
    {
        return false;
    }

    /**
     * @param \Application_Model_Charakter $charakter
     * @param $value
     *
     * @return bool
     */
    public function isIncompatible (\Application_Model_Charakter $charakter, $value)
    {
        return false;
    }
}
