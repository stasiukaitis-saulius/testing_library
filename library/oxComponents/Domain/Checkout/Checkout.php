<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Domain\Checkout;

use OxidEsales\TestingLibrary\oxComponents\Component;
use OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Checkout as CheckoutPath;

//TODO: this class is extended some other components, need to check this if it's good solution
class Checkout extends Component
{
    /**
     * @return CheckoutPath
     */
    public function getPath()
    {
        return new CheckoutPath();
    }
}