<?php
/**
* #PHPHEADER_OXID_LICENSE_INFORMATION#
*/

namespace OxidEsales\TestingLibrary\oxComponents\Domain;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Button;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\CurrencyBox as CurrencyBoxPath;

class CurrencyBox extends Component
{
    public function getPath()
    {
        return new CurrencyBoxPath();
    }

    public function openCurrencyMenu()
    {
        $select = new Button($this->getNode());
        $select->click();
    }

    public function getActiveCurrency()
    {
        $select = new Button($this->getNode());
        return $select->getText();
    }
}