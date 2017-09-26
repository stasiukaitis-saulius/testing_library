<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Pay;

use OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Checkout;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Pay\Pay as PayPath;

/**
 * Class Basket
 * @package OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Steps\Cart\Basket
 */
class Pay extends Checkout
{
    /**
     * @return ShippingMethods
     */
    public function getShippingMethods()
    {
        return new ShippingMethods($this->getNode());
    }

    /**
     * @return PaymentMethods
     */
    public function getPaymentMethods()
    {
        return new PaymentMethods($this->getNode());
    }

    /**
     * @return \Behat\Mink\Element\NodeElement|null
     */
    private function _getMainStepPayNode()
    {
        return $this->findNode(new PayPath());
    }
}