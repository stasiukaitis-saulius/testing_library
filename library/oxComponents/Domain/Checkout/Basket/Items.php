<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Basket;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Basket\Items as ItemsPath;

class Items extends Component
{
    /**
     * @return ItemsPath
     */
    public function getPath()
    {
        return new ItemsPath();
    }

    /**
     * @param $iItemNumber
     * @return null|Item
     */
    public function getItem($iItemNumber)
    {
        return new Item($this->getNode(), $iItemNumber);
    }
}