<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Basket;

use OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Checkout;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Basket\Basket as BasketPath;

/**
 * Class Basket
 * @package OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Steps\Cart\Basket
 */
class Basket extends Checkout
{
    /**
     * Function returns checkout page basket item object.
     *
     * @param int $iItemNumber
     *
     * @return Item
     */
    public function getItem($iItemNumber = 1)
    {
        $oItems = new Items($this->_getMainBasketNode());

        return $oItems->getItem($iItemNumber);
    }

    /**
     * @return Functions
     */
    public function getFunctions()
    {
        return new Functions($this->_getMainBasketNode());
    }

    /**
     * @return \Behat\Mink\Element\NodeElement|null
     */
    private function _getMainBasketNode()
    {
        return $this->findNode(new BasketPath());
    }
}