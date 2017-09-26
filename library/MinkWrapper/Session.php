<?php

class Session extends \Behat\Mink\Session
{
    public function __construct($oMinkDriver, $oSelectorsHandler = null)
    {
        parent::__construct($oMinkDriver, $oSelectorsHandler);
    }
}