<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\TestingLibrary\oxComponents\Paths\Domain\Checkout\Pay;

use OxidEsales\TestingLibrary\oxComponents\Paths\Path;

class PaymentMethod extends Path
{
    /**
     * @var string
     */
    protected $_sXPath = '//dl[1]';

    /**
     * @var string
     */
    protected $_sCustomXPath = "//dl[%d]";
}