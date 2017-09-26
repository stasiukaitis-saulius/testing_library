<?php
/**
* #PHPHEADER_OXID_LICENSE_INFORMATION#
*/

namespace OxidEsales\TestingLibrary\oxComponents\Domain;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Select;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\CurrencyMenu as CurrencyMenuPath;

class CurrencyMenu extends Component
{
    public function getPath()
    {
        return new CurrencyMenuPath();
    }

    public function selectCurrency($name)
    {
        $select = new Select($this->getNode());
        $select->selectByName($name);
    }
}