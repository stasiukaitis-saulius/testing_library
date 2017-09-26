<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Pay;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Input;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Pay\ShippingMethods as ShippingMethodsPath;

class ShippingMethods extends Component
{
    /**
     * @return object|ShippingMethodsPath
     */
    public function getPath()
    {
        return new ShippingMethodsPath();
    }

    /**
     * @return Input
     */
    public function checkCashOnDeliveryInput()
    {
        return new Input($this->getNode(), 'paymentid');
    }
}