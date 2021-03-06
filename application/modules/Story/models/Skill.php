<?php

/**
 * Description of Story_Model_Skill
 *
 * @author Voß
 */
class Story_Model_Skill extends Application_Model_Skill {

    /**
     * @var string
     */
    private $requestType;

    /**
     * @return string
     */
    public function getRequestType ()
    {
        return $this->requestType;
    }

    /**
     * @param string $requestType
     *
     * @return Story_Model_Skill
     */
    public function setRequestType ($requestType)
    {
        $this->requestType = $requestType;
        return $this;
    }

    /**
     * @return  string
     */
    public function __toString ()
    {
        return $this->bezeichnung;
    }

}
