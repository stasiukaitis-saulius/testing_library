<?php
/**
* #PHPHEADER_OXID_LICENSE_INFORMATION#
*/

namespace OxidEsales\TestingLibrary\oxComponents\Domain;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Input;
use OxidEsales\TestingLibrary\oxComponents\Base\Button;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\LoginBox as LoginBoxPath;

class LoginBox extends Component
{
    /**
     * @var \OxidEsales\TestingLibrary\oxComponents\Domain\Header
     */
    private $_oHeader;

    public function getPath()
    {
        return new LoginBoxPath();
    }

    /**
     * @param \OxidEsales\TestingLibrary\oxComponents\Domain\Header $oHeader
     */
    public function setHeader($oHeader)
    {
        $this->_oHeader = $oHeader;
    }

    public function logIn($sUserName, $sPassword)
    {
        $oInputName = new Input($this->getNode(), 'lgn_usr');
        $oInputName->setValue($sUserName);

        //TODO: this added because of syn.js issue
        //Related with https://tracker.moodle.org/browse/MDL-44286
        $this->_oHeader->getLogInLink()->click();

        $oInputPassword = new Input($this->getNode(), 'lgn_pwd');
        $oInputPassword->setValue($sPassword);
        $oButton = new Button($this->getNode());
        $oButton->click();
    }
}