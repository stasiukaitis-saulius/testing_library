<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Checkout\Pay;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Base\Input;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Pay\PaymentMethod as PaymentMethodPath;

class PaymentMethod extends Component
{
    /**
     * @return object|PaymentMethodPath
     */
    public function getPath()
    {
        return new PaymentMethodPath($this->getIdentity());
    }

    /**
     * @return Input
     */
    public function getRadioButton()
    {
        return new Input($this->getNode(), 'paymentid');
    }
}