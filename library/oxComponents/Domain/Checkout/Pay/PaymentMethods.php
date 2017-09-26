<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Pay;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Input;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Pay\PaymentMethods as PaymentMethodsPath;

class PaymentMethods extends Component
{
    /**
     * @return object|PaymentMethodsPath
     */
    public function getPath()
    {
        return new PaymentMethodsPath();
    }

    /**
     * @param $iMethodNumber
     * @return PaymentMethod
     */
    public function getMethod($iMethodNumber)
    {
        return new PaymentMethod($this->getNode(), $iMethodNumber);
    }

    /**
     * @return Input
     */
    public function getCashOnDeliveryInput()
    {
        return new Input($this->getNode(), 'paymentid');
    }
}